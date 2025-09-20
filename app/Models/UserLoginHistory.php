<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class UserLoginHistory extends Model
{
    protected $table = 'user_login_history';

    protected $fillable = [
        'user_id',
        'login_at',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'operating_system',
        'location',
        'successful',
        'failure_reason',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'successful' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loginHistory) {
            // Parse user agent if provided
            if ($loginHistory->user_agent) {
                $agent = new Agent();
                $agent->setUserAgent($loginHistory->user_agent);

                $loginHistory->device_type = $agent->isMobile() ? 'mobile' :
                                           ($agent->isTablet() ? 'tablet' : 'desktop');
                $loginHistory->browser = $agent->browser();
                $loginHistory->operating_system = $agent->platform();
            }
        });
    }

    public static function logLogin(User $user, string $ipAddress = null, string $userAgent = null, bool $successful = true, string $failureReason = null): self
    {
        return static::create([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }
}
