<?php

namespace App\Console\Commands;

use App\Models\VerifyUser;
use Illuminate\Console\Command;

class UserVerifyExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Expired Clean-Up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expired = VerifyUser::where('verified_status', false)
            ->where('verification_expire_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expired as $v) {
            $user = $v->user;
            if (!$user->hasAnyRole(['super admin', 'staff admin', 'finance admin'])) {
                $v->delete();
                $user->delete();
            } else {
                $v->delete(); // aman untuk admin
            }
            $count++;
        }
        $this->info("{$count} User Expired");
    }
}
