<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Master\Coupon;
use Carbon\Carbon;

class DeactivateExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate all expired coupons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find all active coupons that have expired
        $expiredCoupons = Coupon::where('is_active', true)
            ->where('valid_to', '<', $now)
            ->get();
        
        if ($expiredCoupons->isEmpty()) {
            $this->info('No expired coupons found.');
            return 0;
        }
        
        $count = 0;
        foreach ($expiredCoupons as $coupon) {
            $coupon->update(['is_active' => false]);
            $this->info("Deactivated coupon: {$coupon->code} (expired at {$coupon->valid_to})");
            $count++;
        }
        
        $this->info("Successfully deactivated {$count} expired coupon(s).");
        
        return 0;
    }
}
