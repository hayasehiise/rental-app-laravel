<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel Expired Pending Bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $bookings = Booking::where('status', 'pending')
            ->where(function ($q) use ($now) {
                $q->where('end_time', '<', $now)
                    ->orWhere('created_at', '<', $now->subMinutes(30));
            })
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->payment) {
                $booking->payment->update(['transaction_status' => 'cancelled']);
            }

            $count++;
        }

        $this->info("{$count} Bookings (and Payment) cancelled");
    }
}
