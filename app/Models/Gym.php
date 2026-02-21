<?php

namespace App\Models;

use App\Services\SubscriptionService;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gym extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Gym $gym): void {
            app(SubscriptionService::class)->ensureSubscription($gym->id);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'address',
        'address_country_code',
        'address_country_name',
        'address_state',
        'address_city',
        'address_line',
        'timezone',
        'currency_code',
        'language_code',
        'logo_path',
        'avatar_male_path',
        'avatar_female_path',
        'avatar_neutral_path',
    ];

    /**
     * Get the users that belong to the gym.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get clients that belong to the gym.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get plans that belong to the gym.
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Get promotions that belong to the gym.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Get cash sessions that belong to the gym.
     */
    public function cashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class);
    }

    /**
     * Get cash movements that belong to the gym.
     */
    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    /**
     * Get subscriptions that belong to the gym.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get subscription notifications for this gym.
     */
    public function subscriptionNotifications(): HasMany
    {
        return $this->hasMany(SubscriptionNotification::class);
    }

    /**
     * Get contact suggestions sent by this gym.
     */
    public function contactSuggestions(): HasMany
    {
        return $this->hasMany(ContactSuggestion::class);
    }

    /**
     * Get the latest subscription.
     */
    public function latestSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
}
