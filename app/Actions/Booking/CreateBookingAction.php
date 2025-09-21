<?php

namespace App\Actions\Booking;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\Discount;
use App\Models\RentalUnit;
use App\Events\BookingCreated;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Validation\ValidationException;

class CreateBookingAction
{
    use AsAction;

    public function handle(array $data, int $unitId, User $user): Booking
    {
        $unit = RentalUnit::with(['rental.category'])->findOrFail($unitId);

        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);

        $isMember = $user->hasRole('member');

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

        //return DB Transaction
        return DB::transaction(function () use ($data, $unit, $start, $end, $isMember, $category, $user) {
            if ($category === 'kendaraan') {
                // hitung harga
                $days = max(1, $start->diffInDays($end) + 1);
                $basePrice = $unit->price;
                $price = $basePrice * $days;
            } else {
                // hitung harga
                $hours = max(1, $start->diffInHours($end));
                $basePrice = $unit->price;
                $price = $basePrice * $hours;
            }

            $discounts = Discount::where('is_member_only', $isMember)
                ->where(function ($q) use ($start) {
                    $q->whereNull('start_time')->orWhere('start_time', '<=', $start);
                })
                ->where(function ($q) use ($end) {
                    $q->whereNull('end_time')->orWhere('end_time', '>=', $end);
                })
                ->orderByDesc('percentage')
                ->get();

            // cek kode diskon tambahan
            if ($code = data_get($data, 'discount_code')) {
                $codeDiscount = Discount::where('code', $code)->first();
                if ($codeDiscount) {
                    $discounts->push($codeDiscount);
                }
            }

            // hitung final price
            $totalPercentage = $discounts->sum('percentage');
            $finalPrice = $price - ($price * $totalPercentage / 100);


            $booking = Booking::create([
                'user_id' => $user->id,
                'rental_unit_id' => $unit->id,
                'start_time' => $start,
                'end_time' => $end,
                'price' => $price,
                'final_price' => $finalPrice,
                'status' => 'pending',
            ]);

            // simpan ke pivot booking_discounts
            if ($discounts->isNotEmpty()) {
                $booking->discounts()->attach($discounts->pluck('id')->toArray());
            }

            // trigger event
            event(new BookingCreated($booking));

            return $booking;
        });
    }
}
