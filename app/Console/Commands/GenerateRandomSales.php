<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class GenerateRandomSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-random-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a random sale and clear dashboard cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $store = Store::inRandomOrder()->first();

        if (!$store) {
            $this->error('No stores found. Please seed the database first.');
            return;
        }

        $amount = rand(100000, 1000000);

        Sale::create([
            'store_id' => $store->id,
            'amount' => $amount,
            'transaction_date' => now(),
        ]);

        $this->info("Successfully generated sale for store '{$store->name}' with amount Rp " . number_format($amount, 0, ',', '.'));

        // 3. Logic to clear all dashboard sales cache
        $this->clearDashboardCache();
    }

    private function clearDashboardCache()
    {
        try {
            // Since we are using Redis (configured in .env), we can clear by pattern
            
            $prefix = config('cache.prefix');
            $patterns = [
                $prefix . 'sales_data_*',
                $prefix . 'sales_stats_*'
            ];
            
            $totalCleared = 0;
            foreach ($patterns as $pattern) {
                $keys = Redis::keys($pattern);

                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $cleanKey = str_replace($prefix, '', $key);
                        Redis::del($cleanKey);
                        $totalCleared++;
                    }
                }
            }

            if ($totalCleared > 0) {
                $this->info("Cleared " . $totalCleared . " dashboard cache keys.");
            } else {
                $this->info("No dashboard cache keys found to clear.");
            }
        } catch (\Exception $e) {
            $this->warn("Could not clear Redis cache: " . $e->getMessage());
            // If Redis is not available, we can't clear by pattern easily,
            // but we'll let it fail silently or with a warning to the console.
        }
    }
}
