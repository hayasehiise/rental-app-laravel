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
        $unit = RentalUnit::with(['rental.category', 'lapanganPrice', 'gedungPrice', 'kendaraanPrice'])->findOrFail($unitId);
        $category = $unit->rental->category->slug;

        $start = Carbon::parse($data['start_time']);
        $isMember = false;
        $end = null;

        // tentukan end_time untuk tiap kategori
        if ($category === 'lapangan') {
            $isMember = $data['member'];

            if ($isMember) {
                $end = $start->copy()->addHours(4);
            } else {
                $end = Carbon::parse($data['end_time']);
            }
        } elseif ($category === 'gedung') {
            $gedungPrice = $unit->gedungPrice->where('id', $data['gedung_price_id'])->first();
            $end = $start->copy()->addDays($gedungPrice->per_day);
        } elseif ($category === 'kendaraan') {
            $end = Carbon::parse($data['end_time']);
        }

        // Validasi Sabtu
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
            if ($category === 'lapangan') {
                $lapanganPrice = $unit->lapanganPrice;

                if (!$isMember) {
                    $diffMinutes = $start->diffInMinutes($end);
                    $hours = max(1, ceil($diffMinutes / 60));
                    $price = $lapanganPrice->guest_price * $hours;
                    $finalPrice = $price;
                } else {
                    $price = $lapanganPrice->member_price;
                    $finalPrice = $price;
                }

                // Booking Pertama
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'rental_unit_id' => $unit->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'price' => $price,
                    'final_price' => $finalPrice,
                    'status' => 'pending',
                ]);

                // trigger event
                event(new BookingCreated($booking));

                if ($isMember) {
                    $memberQuota = $lapanganPrice->member_quota;
                    $currentStart = $start->copy();
                    $currentEnd = $end->copy();

                    for ($i = 1; $i < $memberQuota; $i++) {
                        $currentStart->addWeek();
                        $currentEnd->addWeek();

                        $exists = Booking::where('rental_unit_id', $unit->id)
                            ->whereIn('status', ['pending', 'paid'])
                            ->where(function ($q) use ($currentStart, $currentEnd) {
                                $q->whereBetween('start_time', [$currentStart, $currentEnd])
                                    ->orWhereBetween('end_time', [$currentStart, $currentEnd])
                                    ->orWhere(function ($q2) use ($currentStart, $currentEnd) {
                                        $q2->where('start_time', '<=', $currentStart)
                                            ->where('end_time', '>=', $currentEnd);
                                    });
                            })
                            ->exists();

                        if ($exists) break;

                        Booking::create([
                            'user_id' => $user->id,
                            'rental_unit_id' => $unit->id,
                            'start_time' => $currentStart->copy(),
                            'end_time' => $currentEnd->copy(),
                            'price' => $price,
                            'final_price' => $finalPrice,
                            'status' => 'pending',
                            'parent_booking_id' => $booking->id,
                        ]);
                    }
                }
            }

            if ($category === 'kendaraan') {
                $kendaraanPrice = $unit->kendaraanPrice;

                // Hitung Hari (minimal 1 hari)
                $days = max(1, $start->diffInDays($end) + ($end->gt($start->copy()->addDays($start->diffInDays($end))) ? 1 : 0));

                $price = $kendaraanPrice->price * $days;
                $finalPrice = $price;

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'rental_unit_id' => $unit->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'price' => $price,
                    'final_price' => $finalPrice,
                    'status' => 'pending',
                ]);

                event(new BookingCreated($booking));
            }

            if ($category === 'gedung') {
                $gedungPrice = $unit->gedungPrice->where('id', $data['gedung_price_id'])->first();

                $price = $gedungPrice->price;
                $finalPrice = $price;

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'rental_unit_id' => $unit->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'price' => $price,
                    'final_price' => $finalPrice,
                    'status' => 'pending',
                ]);

                event(new BookingCreated($booking));
            }

            return $booking;
        });
    }
}
