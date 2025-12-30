<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use App\Models\SiteSetting;

trait VerifiesCaptcha
{
    protected function verifyCaptcha($token, $driver)
    {
        $secret = '';
        $url = '';

        if ($driver === 'google') {
            $secret = SiteSetting::where('setting_key', 'recaptcha_secret_key')->value('setting_value');
            $url = 'https://www.google.com/recaptcha/api/siteverify';
        } elseif ($driver === 'cloudflare') {
            $secret = SiteSetting::where('setting_key', 'turnstile_secret_key')->value('setting_value');
            $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        }

        if (!$secret) return true;

        try {
            $response = Http::asForm()->post($url, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            return $response->successful() && $response->json('success');
        } catch (\Exception $e) {
            return false;
        }
    }
}
