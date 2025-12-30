<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;

class LeaderboardSeeder extends Seeder
{
    public function run()
    {
        // Create 10 Users with random high balances
        for ($i = 1; $i <= 10; $i++) {
            $balance = rand(1000000, 100000000); // 1M to 100M
            
            $user = User::create([
                'name' => 'Sultan ' . $i,
                'username' => 'sultan' . $i,
                'email' => 'sultan' . $i . '@example.com',
                'password' => Hash::make('password'),
                'balance' => $balance,
                'is_admin' => 0,
                'email_verified_at' => now(),
            ]);

            // Create some random withdrawals for them
            if (rand(0, 1)) {
                $withdrawAmount = rand(500000, 50000000);
                Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $withdrawAmount,
                    'status' => 'approved',
                    'payment_method' => 'bank_transfer',
                    'payment_details' => 'Bank BCA',
                ]);
            }
        }
    }
}
