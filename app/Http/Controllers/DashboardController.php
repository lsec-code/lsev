<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 1. Personal Stats
        $personal_videos = \App\Models\Video::where('user_id', $user->id)->count();
        $total_views = \App\Models\Video::where('user_id', $user->id)->sum('views');
        
        // 2. Financial Stats
        $today = now()->toDateString();
        $week_start = now()->startOfWeek()->toDateString();
        $month_start = now()->startOfMonth()->toDateString();
        
        $daily_stats_query = \App\Models\DailyStat::where('user_id', $user->id);
        
        $today_earning = (clone $daily_stats_query)->where('date', $today)->sum('earnings');
        $week_earning = (clone $daily_stats_query)->where('date', '>=', $week_start)->sum('earnings');
        $month_earning = (clone $daily_stats_query)->where('date', '>=', $month_start)->sum('earnings');
        
        // Fix for Data Synchronization:
        // If daily_stats is missing data but User has Balance, we determine Total Lifetime
        // by summing Current Balance + Total Approved Withdrawals.
        $total_withdrawals = $user->withdrawals()->where('status', 'approved')->sum('amount');
        $lifetime_derived = $user->balance + $total_withdrawals;
        
        // Use the greater value to ensure we don't under-report if user spent balance on things other than withdraw
        $lifetime_earning = max((clone $daily_stats_query)->sum('earnings'), $lifetime_derived);
        
        $financial_stats = [
            'today_earning' => $today_earning,
            'week_earning' => $week_earning,
            'month_earning' => $month_earning,
            'total_lifetime' => $lifetime_earning, 
            'current_balance' => $user->balance,
        ];

        // 3. Chart Data (Last 7 Days)
        $chart_data = [];
        $days = [];
        
        // Loop back 6 days to today
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->locale('id')->isoFormat('ddd'); // Mon, Tue, etc.
            
            $earning = (clone $daily_stats_query)->where('date', $date)->sum('earnings');
            
            $chart_data[] = $earning;
            $days[] = $dayName;
        }

        // 4. Active Viewers (users watching my videos)
        $online_count = \DB::table('active_viewers')
            ->join('videos', 'active_viewers.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('active_viewers.last_heartbeat', '>=', now()->subSeconds(30))
            ->distinct('active_viewers.session_id')
            ->count('active_viewers.session_id');

        $user_stats = [
            'video_count' => $personal_videos,
            'total_views' => $total_views,
            'online_count' => $online_count,
        ];

        $financial_stats = [
            'daily_earning' => $today_earning,
            'week_earning' => $week_earning,
            'month_earning' => $month_earning,
            'total_lifetime' => $lifetime_earning, 
            'current_balance' => $user->balance,
        ];
        
        return view('dashboard', compact(
            'user_stats', 
            'financial_stats',
            'chart_data',
            'days'
        ));
    }

    /**
     * Get active viewers count for AJAX refresh
     */
    public function getActiveViewers()
    {
        $user = auth()->user();
        
        $count = \DB::table('active_viewers')
            ->join('videos', 'active_viewers.video_id', '=', 'videos.id')
            ->where('videos.user_id', $user->id)
            ->where('active_viewers.last_heartbeat', '>=', now()->subSeconds(30))
            ->distinct('active_viewers.session_id')
            ->count('active_viewers.session_id');

        return response()->json(['count' => $count]);
    }
}
