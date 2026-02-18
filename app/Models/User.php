<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'name',
        'email',
        'theme',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the gym that owns the user.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get attendances registered by this user.
     */
    public function attendancesCreated(): HasMany
    {
        return $this->hasMany(Attendance::class, 'created_by');
    }

    /**
     * Get cash sessions opened by this user.
     */
    public function openedCashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class, 'opened_by');
    }

    /**
     * Get cash sessions closed by this user.
     */
    public function closedCashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class, 'closed_by');
    }

    /**
     * Get cash movements created by this user.
     */
    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'created_by');
    }

    /**
     * Get subscription notifications managed by this user.
     */
    public function subscriptionNotificationsManaged(): HasMany
    {
        return $this->hasMany(SubscriptionNotification::class, 'created_by');
    }
}
