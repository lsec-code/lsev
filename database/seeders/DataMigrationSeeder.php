<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Connect to OLD Database
        // Assuming user/pass matches the one in .env or is root/empty
        // We use a separate PDO connection for the old DB to retrieve data
        
        $old_host = '127.0.0.1';
        $old_db = 'vidoy_db';
        $old_user = 'root';
        $old_pass = '';
        
        try {
            $pdo = new \PDO("mysql:host=$old_host;dbname=$old_db", $old_user, $old_pass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // 1. MIGRATE USERS
            $users = $pdo->query("SELECT * FROM users")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($users as $u) {
                // Check if exists
                if (DB::table('users')->where('email', $u['email'])->exists()) {
                    continue;
                }
                
                DB::table('users')->insert([
                    'id' => $u['id'], // Preserve ID
                    'name' => $u['username'], // Populate name with username for Auth UI compatibility
                    'username' => $u['username'],
                    'email' => $u['email'],
                    'password' => $u['password'], // Hash is compatible
                    'avatar' => $u['avatar'],
                    'balance' => $u['balance'] ?? 0,
                    'is_admin' => $u['is_admin'] ?? 0,
                    'created_at' => $u['created_at'] ?? now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('Users migrated: ' . count($users));
            
            // 2. MIGRATE VIDEOS
            // Requires 'videos' table in old DB
            try {
                $videos = $pdo->query("SELECT * FROM videos")->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($videos as $v) {
                     if (DB::table('videos')->where('id', $v['id'])->exists()) continue;
                     
                     DB::table('videos')->insert([
                         'id' => $v['id'],
                         'user_id' => $v['user_id'],
                         'title' => $v['title'],
                         'slug' => $v['slug'] ?? \Illuminate\Support\Str::slug($v['title']) . '-' . uniqid(),
                         'filename' => $v['filename'],
                         'thumbnail' => $v['thumbnail'] ?? null,
                         'views' => $v['views'] ?? 0,
                         'duration' => $v['duration'] ?? '00:00',
                         'status' => 'active', // Default
                         'created_at' => $v['created_at'] ?? now(),
                         'updated_at' => now(),
                     ]);
                }
                $this->command->info('Videos migrated: ' . count($videos));
            } catch (\Exception $e) {
                $this->command->warn('Videos table not found or empty in old DB.');
            }
            
            // 3. MIGRATE WITHDRAWALS
             try {
                $withdrawals = $pdo->query("SELECT * FROM withdrawals")->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($withdrawals as $w) {
                     if (DB::table('withdrawals')->where('id', $w['id'])->exists()) continue;
                     
                     DB::table('withdrawals')->insert([
                         'id' => $w['id'],
                         'user_id' => $w['user_id'],
                         'amount' => $w['amount'],
                         'status' => $w['status'],
                         'payment_method' => $w['method'] ?? 'Unknown', // Map column 'method' from old db if exists
                         'payment_details' => $w['payment_details'] ?? '',
                         'created_at' => $w['created_at'] ?? now(),
                         'updated_at' => now(),
                     ]);
                }
                $this->command->info('Withdrawals migrated: ' . count($withdrawals));
            } catch (\Exception $e) {
                $this->command->warn('Withdrawals migration skipped: ' . $e->getMessage());
            }

            // 4. MIGRATE SITE SETTINGS
            try {
                $settings = $pdo->query("SELECT * FROM site_settings")->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($settings as $s) {
                    if (DB::table('site_settings')->where('setting_key', $s['setting_key'])->exists()) continue;
                    
                    DB::table('site_settings')->insert([
                        'setting_key' => $s['setting_key'],
                        'setting_value' => $s['setting_value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $this->command->info('Settings migrated: ' . count($settings));
            } catch (\Exception $e) {
                $this->command->warn('Settings table mismatch: ' . $e->getMessage());
            }

            // 5. MIGRATE COMMENTS
            try {
                $comments = $pdo->query("SELECT * FROM comments ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($comments as $c) {
                    if (DB::table('comments')->where('id', $c['id'])->exists()) continue;
                    
                    DB::table('comments')->insert([
                        'id' => $c['id'],
                        'user_id' => $c['user_id'],
                        'video_id' => $c['video_id'],
                        'parent_id' => $c['parent_id'] ?? null,
                        'comment' => $c['comment'],
                        'created_at' => $c['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);
                }
                $this->command->info('Comments migrated: ' . count($comments));
            } catch (\Exception $e) {
                $this->command->warn('Comments migration skipped: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            $this->command->error("Migration Failed: " . $e->getMessage());
        }
    }
}
