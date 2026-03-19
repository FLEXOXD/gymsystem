<?php

namespace App\Models;

use App\Services\SubscriptionService;
use Illuminate\Database\Eloquent\Builder;
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
     * Login events recorded for the main gym admin accounts.
     */
    public function adminLoginEvents(): HasMany
    {
        return $this->hasMany(GymAdminLoginEvent::class);
    }

    /**
     * Last-known activity snapshot for the principal gym admin.
     */
    public function adminActivityState(): HasOne
    {
        return $this->hasOne(GymAdminActivityState::class);
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
     * Get products that belong to the gym.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get product sales for this gym.
     */
    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    /**
     * Get stock movements for this gym.
     */
    public function productStockMovements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class);
    }

    /**
     * Get subscriptions that belong to the gym.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Charge events generated for the SaaS subscription of this gym.
     */
    public function subscriptionChargeEvents(): HasMany
    {
        return $this->hasMany(SubscriptionChargeEvent::class);
    }

    /**
     * Subscriptions billed under this gym as owner.
     */
    public function billedSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'billing_owner_gym_id');
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

    public function supportChatConversations(): HasMany
    {
        return $this->hasMany(SupportChatConversation::class);
    }

    /**
     * Get the latest subscription.
     */
    public function latestSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    /**
     * Demo session linked to this gym (if it is a temporary demo tenant).
     */
    public function demoSession(): HasOne
    {
        return $this->hasOne(DemoSession::class);
    }

    /**
     * Links where this gym acts as multi-branch hub.
     */
    public function branchLinks(): HasMany
    {
        return $this->hasMany(GymBranchLink::class, 'hub_gym_id');
    }

    /**
     * Links where this gym acts as linked branch.
     */
    public function parentHubLinks(): HasMany
    {
        return $this->hasMany(GymBranchLink::class, 'branch_gym_id');
    }

    /**
     * Scope only persistent gyms (exclude temporary demo gyms).
     */
    public function scopeWithoutDemoSessions(Builder $query): Builder
    {
        return $query->whereDoesntHave('demoSession');
    }
}
