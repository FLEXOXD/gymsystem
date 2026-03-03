<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_OWNER = 'owner';
    public const ROLE_CASHIER = 'cashier';
    public const ROLE_EMPLOYEE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'name',
        'email',
        'country_iso',
        'country_name',
        'address_state',
        'address_city',
        'address_line',
        'gender',
        'birth_date',
        'identification_type',
        'identification_number',
        'phone_country_iso',
        'phone_country_dial',
        'phone_number',
        'profile_photo_path',
        'support_contact_label',
        'support_contact_email',
        'support_contact_phone',
        'support_contact_whatsapp',
        'support_contact_link',
        'support_contact_message',
        'support_contact_logo_path',
        'support_contact_logo_light_path',
        'support_contact_logo_dark_path',
        'role',
        'is_active',
        'can_open_cash',
        'can_close_cash',
        'can_manage_cash_movements',
        'theme',
        'timezone',
        'legal_accepted_at',
        'legal_accepted_version',
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
            'last_login_at' => 'datetime',
            'legal_accepted_at' => 'datetime',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'can_open_cash' => 'boolean',
            'can_close_cash' => 'boolean',
            'can_manage_cash_movements' => 'boolean',
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

    /**
     * Get contact suggestions sent by this user.
     */
    public function contactSuggestionsSent(): HasMany
    {
        return $this->hasMany(ContactSuggestion::class, 'user_id');
    }

    /**
     * Get contact suggestions reviewed by this user.
     */
    public function reviewedContactSuggestions(): HasMany
    {
        return $this->hasMany(ContactSuggestion::class, 'reviewed_by');
    }

    /**
     * Active demo session assigned to this user (if any).
     */
    public function demoSession(): HasOne
    {
        return $this->hasOne(DemoSession::class);
    }

    /**
     * Legal acceptance records linked to this user.
     */
    public function legalAcceptances(): HasMany
    {
        return $this->hasMany(LegalAcceptance::class, 'user_id');
    }

    /**
     * Push subscriptions registered by this user (one per device/browser).
     */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function roleKey(): string
    {
        if ($this->gym_id === null) {
            return self::ROLE_SUPERADMIN;
        }

        $resolved = strtolower(trim((string) ($this->role ?? '')));
        if ($resolved !== '') {
            return $resolved;
        }

        return self::ROLE_OWNER;
    }

    public function hasRole(string ...$roles): bool
    {
        $resolved = $this->roleKey();
        $normalized = collect($roles)
            ->map(static fn (string $role): string => strtolower(trim($role)))
            ->filter(static fn (string $role): bool => $role !== '')
            ->values()
            ->all();

        if ($normalized === []) {
            return false;
        }

        return in_array($resolved, $normalized, true);
    }

    public function isCashier(): bool
    {
        return $this->hasRole(self::ROLE_CASHIER);
    }

    public function isOwner(): bool
    {
        return $this->hasRole(self::ROLE_OWNER);
    }

    public function isActiveAccount(): bool
    {
        if (! array_key_exists('is_active', $this->attributes) && ! isset($this->is_active)) {
            return true;
        }

        return (bool) ($this->is_active ?? true);
    }

    public function canOpenCashBox(): bool
    {
        if ($this->isOwner() || $this->hasRole(self::ROLE_SUPERADMIN)) {
            return true;
        }
        if (! $this->isCashier()) {
            return false;
        }

        return (bool) ($this->can_open_cash ?? false);
    }

    public function canCloseCashBox(): bool
    {
        if ($this->isOwner() || $this->hasRole(self::ROLE_SUPERADMIN)) {
            return true;
        }
        if (! $this->isCashier()) {
            return false;
        }

        return (bool) ($this->can_close_cash ?? false);
    }

    public function canManageCashMovements(): bool
    {
        if ($this->isOwner() || $this->hasRole(self::ROLE_SUPERADMIN)) {
            return true;
        }
        if (! $this->isCashier()) {
            return false;
        }

        // Backward-compatible default for legacy cashier rows.
        return (bool) ($this->can_manage_cash_movements ?? true);
    }
}
