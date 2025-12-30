<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DailyStat;
use Carbon\Carbon;

class BackfillDailyStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('balance', '>', 0)->get();

        foreach ($users as $user) {
            // Check if stats already exist to avoid double counting if run multiple times
            if (DailyStat::where('user_id', $user->id)->exists()) {
                continue;
            }

            $totalBalance = $user->balance;
            
            // Strategy: Spread the balance over the last 7 days to make the chart look nice
            // Day 1 (Today): 40%
            // Day 2-7: 10% each
            
            $days = 7;
            $remaining = $totalBalance;
            $chunks = [];

            // Randomize distribution slightly
            for ($i = 0; $i < $days - 1; $i++) {
                $amount = round($totalBalance * (rand(5, 15) / 100), 2);
                $chunks[] = $amount;
                $remaining -= $amount;
            }
            // Put the rest in "Today" or latest day
            $chunks[] = $remaining; // This is the biggest chunk usually or just the remainder

            // Insert records
            // Reverse loop to start from 6 days ago up to today
            foreach (array_reverse($chunks) as $index => $amount) {
                $date = Carbon::now()->subDays($index)->format('Y-m-d');
                
                DailyStat::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'views' => rand(10, 100), // Mock views for visual
                    'earnings' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->command->info("Backfilled stats for {$user->name}: Rp " . number_format($totalBalance));
        }
    }
}
