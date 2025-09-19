<?php

namespace App\Services;

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Booking;
use App\Models\Discount;
use App\Models\RentalUnit;
use App\Events\BookingCreated;
use App\Events\PaymentCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(array $data, int $unitId, bool $isMember): Booking
    {
        $unit = RentalUnit::with(['rental.category'])->findOrFail($unitId);

        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);

        // Validasi Sabtu
        $category = $unit->rental->category->slug;
        if ((in_array($category, ['lapangan', 'gedung'])) && ($start->isSaturday() || $end->isSaturday())) {
            throw ValidationException::withMessages([
                'booking' => 'Tidak bisa booking pada hari sabtu',
            ]);
        }

        // double check ketersediaan slot
        $exists = Booking::where('rental_unit_id', $unit->id)
            ->whereIn('status', ['pending', 'paid'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'booking' => 'Slot sudah dipesan',
            ]);
        }

        return DB::transaction(function () use ($data, $unit, $start, $end, $isMember) {
            if ($category === 'kendaraan') {
                // hitung harga
                $days = max(1, $start->diffInDays($end));
                $basePrice = $unit->price;
                $price = $basePrice * $days;
            } else {
                // hitung harga
                $hours = max(1, $start->diffInHours($end));
                $basePrice = $unit->price;
                $price = $basePrice * $hours;
            }

            $discount = Discount::where('is_member_only', $isMember)
                ->where(function ($q) use ($start) {
                    $q->whereNull('start_time')->orWhere('start_time', '<=', $start);
                })
                ->where(function ($q) use ($end) {
                    $q->whereNull('end_time')->orWhere('end_time', '>=', $end);
                })
                ->orderByDesc('percentage')
                ->first();

            $discountvalue = $discount ? $discount->percentage : 0;
            $finalPrice = $price - ($price * ($discountvalue / 100));


            $booking = Booking::create([
                'user_id' => auth()->user()->id,
                'rental_unit_id' => $unit->id,
                'discount_id' => $discount?->id,
                'start_time' => $start,
                'end_time' => $end,
                'price' => $price,
                'final_price' => $finalPrice,
                'status' => 'pending',
            ]);

            // trigger event
            event(new BookingCreated($booking));

            return $booking;
        });
    }

    public function createOrGetSnapToken(Booking $booking): string
    {
        $booking->load(['unit', 'payment', 'user']);
        $payment = $booking->payment;

        // Setup Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Hitung Biaya Transaksi
        $transactionFee = (int) round($booking->final_price * 0.04);
        $grossAmount = (int) $booking->final_price + $transactionFee;

        if (!$payment->snap_token) {
            $orderId = $payment->order_id;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $booking->user->name,
                    'email' => $booking->user->email,
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 15,
                ],
                'item_details' => [
                    [
                        'id' => 'Booking_' . $booking->id,
                        'price' => (int) $booking->final_price,
                        'quantity' => 1,
                        'name' => 'Booking Unit ' . $booking->unit->name,
                    ],
                    [
                        'id' => 'fee_admin',
                        'price' => $transactionFee,
                        'quantity' => 1,
                        'name' => 'Biaya Transaksi (4%)',
                    ],
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $payment->update([
                'snap_token' => $snapToken,
                'transaction_status' => 'pending'
            ]);

            event(new PaymentCreated($payment));
        } else {
            $snapToken = $payment->snap_token;
        }

        return $snapToken;
    }
}
