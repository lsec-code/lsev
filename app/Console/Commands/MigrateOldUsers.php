<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PDO;
use Exception;

class MigrateOldUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:old-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from the old vidoy_db database to the new Laravel system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user migration from vidoy_db...');

        // Database connection settings for the old DB
        $host = '127.0.0.1';
        $dbName = 'vidoy_db';
        $user = 'root';
        $pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->query("SELECT * FROM users");
            $oldUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->info('Found ' . count($oldUsers) . ' users to migrate.');
            $count = 0;
            $updated = 0;

            foreach ($oldUsers as $oldUser) {
                // Skip the Administrator if it exists to avoid messing up current admin (unless explicitly wanted)
                // But we'll use updateOrCreate, so it's mostly safe.
                
                $username = $oldUser['username'];
                $email = $oldUser['email'];

                $newUser = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $oldUser['username'], // Using username as name
                        'email' => $email,
                        'password' => $oldUser['password'], // Old site used hashed passwords compatible with Laravel/Bcrypt
                        'balance' => $oldUser['balance'],
                        'avatar' => $oldUser['avatar'],
                        'is_admin' => $oldUser['is_admin'],
                        'security_question' => $oldUser['security_question'] ?? null,
                        'security_answer' => $oldUser['security_answer'] ?? null,
                        'payment_method' => $oldUser['payment_method'] ?? null,
                        'payment_number' => $oldUser['payment_number'] ?? null,
                        'payment_name' => $oldUser['payment_name'] ?? null,
                        'allow_download' => (bool) ($oldUser['allow_download'] ?? false),
                        'email_verified_at' => now(), // Force verified
                        'created_at' => $oldUser['created_at'],
                    ]
                );

                if ($newUser->wasRecentlyCreated) {
                    $count++;
                } else {
                    $updated++;
                }
            }

            $this->info("Migration completed! $count users created, $updated users updated.");

        } catch (Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
