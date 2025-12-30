<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = auth()->user()->withdrawals()->latest()->paginate(10);
        $min_idr = \App\Models\SiteSetting::where('setting_key', 'min_withdrawal')->value('setting_value') ?? 250000;
        return view('withdrawals.index', compact('withdrawals', 'min_idr'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $min_idr = \App\Models\SiteSetting::where('setting_key', 'min_withdrawal')->value('setting_value') ?? 250000;
        
        // Rate Limit Check (3 attempts / 5 mins)
        $key = 'withdraw-security-'.$user->id;
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->with('cooldown', $seconds)->withErrors(['security_answer' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik."]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:'.$min_idr,
            'security_answer' => 'required|string',
        ]);
        
        // Security Check
        if ($request->security_answer !== $user->security_answer) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 300); // 5 mins cooldown
            $remaining = \Illuminate\Support\Facades\RateLimiter::remaining($key, 3);
            return back()->withErrors(['security_answer' => "Kode Keamanan Salah! Sisa percobaan: {$remaining}."]);
        }

        // Success - Clear attempts
        \Illuminate\Support\Facades\RateLimiter::clear($key);
        
        if ($user->balance < $request->amount) {
            // Tampering Attempt Detection
            \App\Models\SecurityAlert::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'alert_type' => 'data_tampering',
                'severity' => 'medium',
                'pattern_detected' => "Withdrawal Balance Bypass: Requested Rp {$request->amount} stats balance Rp {$user->balance}",
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            \App\Models\Notification::notifyAdmins(
                'security',
                'Potensi Manipulasi Data (WD)!',
                "User {$user->username} mencoba menarik saldo (Rp " . number_format($request->amount, 0, ',', '.') . ") melebihi saldo yang dimiliki.",
                route('admin.security_alerts')
            );

            return back()->withErrors(['amount' => 'Saldo tidak mencukupi. Manipulasi data terdeteksi dan dilaporkan.']);
        }

        // Pull Payment Info from Profile if not provided in request (though we'll use profile as source of truth)
        $payment_method = $user->payment_method ?? 'Unknown';
        $payment_details = "No: " . ($user->payment_number ?? '-') . " (a.n " . ($user->payment_name ?? '-') . ")";
        
        if (!$user->payment_method || !$user->payment_number) {
            return back()->withErrors(['amount' => 'Lengkapi detail pembayaran di Profil Anda sebelum melakukan penarikan.']);
        }

        // Clean amount (Round down to nearest 1.000)
        $requestedAmount = floor($request->amount / 1000) * 1000;
        
        if ($requestedAmount < $min_idr) {
            // Tampering Attempt Detection (Bypassing frontend min check)
            \App\Models\SecurityAlert::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'alert_type' => 'data_tampering',
                'severity' => 'low',
                'pattern_detected' => "Withdrawal Minimum Bypass: Requested Rp {$requestedAmount} min is Rp {$min_idr}",
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors(['amount' => 'Minimal penarikan adalah Rp. ' . number_format($min_idr, 0, ',', '.')]);
        }

        if ($user->balance < $requestedAmount) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi.']);
        }
        
        // Calculate Fees
        $isEwallet = in_array($user->payment_method, ['DANA', 'GOPAY', 'OVO', 'SHOPEEPAY', 'LINKAJA', 'iSAKU']);
        $feePercent = $isEwallet ? 3 : 5;
        $feeAmount = ($requestedAmount * $feePercent) / 100;
        $finalAmount = $requestedAmount - $feeAmount;
        
        // Deduct Balance (Full requested amount)
        $user->decrement('balance', $requestedAmount);
        
        // Create Request
        $wd = \App\Models\Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $requestedAmount, 
            'status' => 'pending',
            'payment_method' => $payment_method,
            'payment_details' => $payment_details . " | Net: Rp " . number_format($finalAmount, 0, ',', '.') . " (Fee {$feePercent}%: Rp " . number_format($feeAmount, 0, ',', '.') . ")"
        ]);

        // Notify Admins
        \App\Models\Notification::notifyAdmins(
            'withdrawal',
            'Permintaan Penarikan Baru',
            "User {$user->username} mengajukan penarikan sebesar Rp " . number_format($requestedAmount, 0, ',', '.'),
            route('admin.withdrawals') // Link to admin withdrawal page
        );
        
        return back()->with('success', 'Permintaan penarikan berhasil dikirim!');
    }
}
