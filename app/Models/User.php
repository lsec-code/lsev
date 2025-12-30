<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'balance',
        'is_admin',
        'security_question',
        'security_answer',
        'payment_method',
        'payment_number',
        'payment_name',
        'allow_download',
        'custom_domain',
        'domain_verified',
        'domain_verified_at',
        'is_suspended',
        'suspension_reason',
        'suspended_at',
        'verification_code',
        'last_activity_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'domain_verified_at' => 'datetime',
        'suspended_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_suspended' => 'boolean',
        'allow_download' => 'boolean',
        'domain_verified' => 'boolean',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
    
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    
    public function loginActivities()
    {
        return $this->hasMany(LoginActivity::class);
    }
            
    /**
     * Get user's avatar URL (random sticker if no photo uploaded)
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return asset('uploads/avatars/' . $this->avatar);
        }
        
        // Random avatar based on user ID (consistent for each user)
        $avatarNumber = ($this->id % 4) + 1; // 1-4
        return asset("images/avatars/avatar-{$avatarNumber}.png");
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Get video share URL (custom domain if verified)
     */
    public function getVideoShareUrl($videoSlug)
    {
        if ($this->custom_domain && $this->domain_verified) {
            // Use custom domain (ensure it has http/https)
            $domain = $this->custom_domain;
            if (!preg_match('/^https?:\/\//', $domain)) {
                $domain = 'https://' . $domain;
            }
            return rtrim($domain, '/') . '/watch/' . $videoSlug;
        }
        
        // Use default domain
        return url('/watch/' . $videoSlug);
    }

    /**
     * Get avatar border classes based on Rank or Admin status
     */
    public function getAvatarType()
    {
        if ($this->is_admin) {
            return 'dev';
        }

        // Calculate my total earnings
        $myTotal = $this->balance + $this->withdrawals()->where('status', 'approved')->sum('amount');

        // Check Rank (Top 3 Total Earnings)
        // We compare with other users' (balance + sum of approved withdrawals)
        $rank = User::where('is_admin', 0)
            ->whereRaw('(balance + (SELECT IFNULL(SUM(amount), 0) FROM withdrawals WHERE user_id = users.id AND status = "approved")) > ?', [$myTotal])
            ->count() + 1;

        if ($rank === 1) return 'gold';
        if ($rank === 2) return 'silver';
        if ($rank === 3) return 'bronze';

        return 'default';
    }

    /**
     * Get the mapped user for the current request (if accessing via custom domain)
     */
    public static function getMappedUser()
    {
        return request()->attributes->get('mapped_user');
    }
}
