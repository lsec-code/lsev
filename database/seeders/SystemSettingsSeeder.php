<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'cpm_rate' => '15000',
            'min_withdraw' => '765000', // Equivalent to $51 at current rate
            'site_name' => 'Cloud Host',
            'ad_code' => '', // Leave empty as ads were removed
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }
    }
}
