<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // 1. Top 7 Total Earnings (Balance + Approved Withdrawals)
        // Using a subquery to avoid ONLY_FULL_GROUP_BY SQL errors
        $topBalances = User::where('is_admin', 0)
            ->select('*')
            ->selectRaw('((SELECT IFNULL(SUM(amount), 0) FROM withdrawals WHERE user_id = users.id AND status = "approved") + balance) as total_earnings')
            ->orderByDesc('total_earnings')
            ->take(7)
            ->get();

        // 2. Top 5 Daily Earnings (Today) - Exclude Admin
        $today = now()->toDateString();
        $topDailyEarnings = \App\Models\DailyStat::select('user_id', DB::raw('sum(earnings) as daily_earning'))
            ->where('date', $today)
            ->whereHas('user', function($query) {
                $query->where('is_admin', 0);
            })
            ->groupBy('user_id')
            ->orderByDesc('daily_earning')
            ->with('user')
            ->take(5)
            ->get();

        return view('leaderboard.index', compact('topBalances', 'topDailyEarnings'));
    }
}
