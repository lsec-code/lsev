<?php

use App\Http\Controllers\ProfileController;

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', [VerifyCodeController::class, 'show'])
//                 ->name('verification.notice');

//     Route::post('verify-email', [VerifyCodeController::class, 'store'])
//                 ->middleware('throttle:6,1')
//                 ->name('verification.code.store');
// });
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SecurityResetController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::post('forgot-password-check', [SecurityResetController::class, 'checkEmail'])->name('password.check');
    Route::get('forgot-password-check', function () { return redirect()->route('password.request'); }); // Fallback
    Route::get('reset-password-security', [SecurityResetController::class, 'showSecurityResetForm'])->name('password.reset.verify');
    Route::post('reset-password-final', [SecurityResetController::class, 'reset'])->name('password.security.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/security/verify', [\App\Http\Controllers\SessionVerificationController::class, 'show'])->name('security.verify.show');
    Route::post('/security/verify', [\App\Http\Controllers\SessionVerificationController::class, 'verify'])->name('security.verify.submit');
    Route::post('/security/cancel', [\App\Http\Controllers\SessionVerificationController::class, 'cancel'])->name('security.verify.cancel');

    // First Login Verification Routes (Must be accessible for unverified users)
    Route::get('/first-login/verify', [App\Http\Controllers\FirstLoginController::class, 'show'])->name('first-login.verify');
    Route::post('/first-login/verify', [App\Http\Controllers\FirstLoginController::class, 'verify'])->name('first-login.verify.submit');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');



Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::view('/security-alert', 'errors.security_alert')->name('security.alert');
Route::view('/account-suspended', 'errors.suspended')->name('account.suspended');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/active-viewers', [DashboardController::class, 'getActiveViewers'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.active-viewers');

use App\Http\Controllers\VideoController;
use App\Http\Controllers\WithdrawalController;
Route::get('/watch/{slug}', [VideoController::class, 'show'])->name('videos.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/upload', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/upload', [VideoController::class, 'store'])->name('videos.store');
    Route::post('/upload/chunk', [VideoController::class, 'storeChunk'])->name('videos.store.chunk'); // Chunked Upload
    Route::post('/folders', [VideoController::class, 'storeFolder'])->name('folders.store'); // New Folder Route
    Route::put('/folders/{id}', [VideoController::class, 'updateFolder'])->name('folders.update');
    Route::delete('/folders/{id}', [VideoController::class, 'destroyFolder'])->name('folders.destroy');
    Route::put('/videos/{id}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/bulk-delete', [VideoController::class, 'bulkDestroy'])->name('videos.bulk-delete');
    Route::post('/videos/bulk-move', [VideoController::class, 'bulkMove'])->name('videos.bulk-move');
    
    // Financial Routes
    Route::get('/withdraw', [WithdrawalController::class, 'index'])->name('withdraw.index');
    Route::post('/withdraw', [WithdrawalController::class, 'store'])->name('withdraw.store');
    
    // Leaderboard Route
    Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
    
    // Comment Route
    Route::post('/watch/{slug}/comment', [VideoController::class, 'postComment'])->name('videos.comment');
});

// Active Viewer Heartbeat (Allow guests)
Route::post('/watch/{slug}/heartbeat', [VideoController::class, 'heartbeat'])->name('videos.heartbeat');

// Unique View Tracking (Allow guests)
Route::post('/watch/{slug}/record-view', [VideoController::class, 'recordView'])->name('videos.record-view');
Route::post('/watch/{slug}/update-duration', [VideoController::class, 'updateWatchDuration'])->name('videos.update-duration');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/remote-upload', function () {
        return view('remote_upload');
    })->name('remote_upload');
    Route::post('/remote-upload', [\App\Http\Controllers\RemoteUploadController::class, 'store'])->name('remote_upload.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/sessions/{sessionId}', [ProfileController::class, 'logoutSession'])->name('profile.sessions.logout');
    


    // Notifications
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\AdminController::class, 'markAsReadAndRedirect'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\AdminController::class, 'markAllNotificationsRead'])->name('notifications.mark_all_read');

    // Chat
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // Admin Panel Routes
    Route::middleware(['admin', 'admin.secure'])->prefix('admin')->name('admin.')->group(function () {
        // Security Verification
        Route::get('/verify-security', [\App\Http\Controllers\AdminController::class, 'verifySecurity'])->name('verify.security');
        Route::post('/verify-security', [\App\Http\Controllers\AdminController::class, 'checkSecurity'])->name('verify.security.check');

        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::get('/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
        
        // Withdrawal Management
        Route::get('/withdrawals', [\App\Http\Controllers\AdminController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/withdrawals/{id}/{action}', [\App\Http\Controllers\AdminController::class, 'updateWithdrawal'])->name('withdrawals.update');
        
        // Statistics Management
        Route::get('/statistics', [\App\Http\Controllers\AdminController::class, 'statistics'])->name('statistics');
        Route::get('/statistics/search', [\App\Http\Controllers\AdminController::class, 'searchUser'])->name('statistics.search');
        Route::post('/statistics/update', [\App\Http\Controllers\AdminController::class, 'updateUserStats'])->name('statistics.update');
        
        // Logs & Security
        
        // Logs & Security
        Route::get('/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('logs');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'userList'])->name('users.list');
        Route::get('/users/online', [\App\Http\Controllers\AdminController::class, 'onlineUsers'])->name('users.online');
        Route::get('/users/{id}/files', [\App\Http\Controllers\AdminController::class, 'userFiles'])->name('users.files');
        Route::get('/users/{id}/files/{folderId}', [\App\Http\Controllers\AdminController::class, 'userFiles'])->name('users.files.folder');
        
        Route::get('/users/create', [\App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
        
        // Data Delete / Cleanup
        Route::get('/data/cleanup', [\App\Http\Controllers\AdminController::class, 'dataCleanup'])->name('data.cleanup');
        Route::post('/chat/toggle', [\App\Http\Controllers\AdminController::class, 'toggleChat'])->name('chat.toggle');
        Route::post('/features/remote-upload/toggle', [\App\Http\Controllers\AdminController::class, 'toggleRemoteUpload'])->name('features.remote_upload.toggle');
        Route::post('/features/custom-domain/toggle', [\App\Http\Controllers\AdminController::class, 'toggleCustomDomain'])->name('features.custom_domain.toggle');
        Route::post('/settings/toggle', [\App\Http\Controllers\AdminController::class, 'toggleSetting'])->name('settings.toggle');
        Route::post('/chat/clear', [\App\Http\Controllers\AdminController::class, 'clearChatHistory'])->name('chat.clear');
        Route::post('/users/delete', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('/data/reset-database', [\App\Http\Controllers\AdminController::class, 'resetDatabaseTotal'])->name('data.reset-database');
        
        // ADS Management
        Route::get('/ads', [\App\Http\Controllers\AdminController::class, 'adsManagement'])->name('ads');
        Route::post('/ads/update', [\App\Http\Controllers\AdminController::class, 'updateAds'])->name('ads.update');
        
        // Captcha Settings
        Route::get('/captcha', [\App\Http\Controllers\AdminController::class, 'captchaSettings'])->name('captcha');
        Route::post('/captcha/update', [\App\Http\Controllers\AdminController::class, 'updateCaptcha'])->name('captcha.update');
        
        // CPM Settings
        Route::get('/cpm', [\App\Http\Controllers\AdminController::class, 'cpmSettings'])->name('cpm');
        Route::post('/cpm/update', [\App\Http\Controllers\AdminController::class, 'updateCpm'])->name('cpm.update');
        
        // Announcements
        Route::get('/announcements', [\App\Http\Controllers\AdminController::class, 'announcements'])->name('announcements');
        Route::put('/announcements/update', [\App\Http\Controllers\AdminController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [\App\Http\Controllers\AdminController::class, 'deleteAnnouncement'])->name('announcements.delete');
        
        Route::get('/security-alerts', [\App\Http\Controllers\AdminController::class, 'securityAlerts'])->name('security_alerts');
        
        // Ban Management
        Route::get('/bans', [\App\Http\Controllers\AdminController::class, 'bans'])->name('bans');
        Route::post('/bans/ip/{id}/unban', [\App\Http\Controllers\AdminController::class, 'unbanIp'])->name('bans.unban_ip');
        Route::post('/bans/user/{id}/unban', [\App\Http\Controllers\AdminController::class, 'unbanUser'])->name('bans.unban_user');
        
        // All Videos
        Route::get('/videos', [\App\Http\Controllers\AdminController::class, 'allVideos'])->name('videos.all');
        
        // Earnings Overview
        Route::get('/earnings', [\App\Http\Controllers\AdminController::class, 'earnings'])->name('earnings');
        
        // Banned Users
        Route::get('/users/banned', [\App\Http\Controllers\AdminController::class, 'bannedUsers'])->name('users.banned');
        
        // Withdrawals by Status
        Route::get('/withdrawals/{status}', [\App\Http\Controllers\AdminController::class, 'withdrawalsByStatus'])
            ->where('status', 'pending|approved|rejected')
            ->name('withdrawals.status');
        
        // Theme Customization (Standalone)
        Route::get('/theme', [\App\Http\Controllers\AdminController::class, 'theme'])->name('theme');
        Route::post('/theme/update', [\App\Http\Controllers\AdminController::class, 'updateTheme'])->name('theme.update');
        Route::post('/theme/logo', [\App\Http\Controllers\AdminController::class, 'uploadLogo'])->name('theme.logo');
        
        // System Health Monitor
        Route::get('/system-health', [\App\Http\Controllers\AdminController::class, 'systemHealth'])->name('system.health');
        
        // Backup & Restore
        Route::get('/backup', [\App\Http\Controllers\AdminController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [\App\Http\Controllers\AdminController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/settings', [\App\Http\Controllers\AdminController::class, 'updateBackupSettings'])->name('backup.settings.update');
        Route::get('/backup/download/{file}', [\App\Http\Controllers\AdminController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{file}', [\App\Http\Controllers\AdminController::class, 'deleteBackup'])->name('backup.delete');
        Route::post('/backup/restore/{filename}', [\App\Http\Controllers\AdminController::class, 'restoreFromBackup'])->name('backup.restore');
        Route::post('/backup/upload-restore', [\App\Http\Controllers\AdminController::class, 'uploadAndRestore'])->name('backup.upload.restore');
        Route::post('/verify-password', [\App\Http\Controllers\AdminController::class, 'verifyAdminPassword'])->name('verify.password');
        
        // Badges & Leaderboard
        Route::get('/badges', [\App\Http\Controllers\AdminController::class, 'badges'])->name('badges');
        Route::post('/badges/seed', [\App\Http\Controllers\AdminController::class, 'seedBadges'])->name('badges.seed');
        Route::get('/leaderboard', [\App\Http\Controllers\AdminController::class, 'leaderboard'])->name('leaderboard');
        
        // Viewer Booster
        Route::post('/boosts', [\App\Http\Controllers\AdminController::class, 'storeBoost'])->name('boosts.store');
        Route::delete('/boosts/{id}', [\App\Http\Controllers\AdminController::class, 'endBoost'])->name('boosts.destroy');
        Route::get('/boosts/process', [\App\Http\Controllers\AdminController::class, 'processBoosts'])->name('boosts.process');
        
    });
});

// Public Pages (Legal & Info)
Route::controller(App\Http\Controllers\PageController::class)->group(function () {
    Route::get('/about', 'about')->name('page.about');
    Route::get('/contact', 'contact')->name('page.contact');
    Route::get('/privacy', 'privacy')->name('page.privacy');
    Route::get('/terms', 'terms')->name('page.terms');
});

require __DIR__.'/auth.php';
