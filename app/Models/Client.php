<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'first_name',
        'last_name',
        'document_number',
        'phone',
        'photo_path',
        'gender',
        'status',
    ];

    /**
     * Get the gym that owns the client.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get the credentials for the client.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(ClientCredential::class);
    }

    /**
     * Get the memberships for the client.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the attendances for the client.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the full display name for the client.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Scope records for a specific gym.
     */
    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }

    /**
     * Scope by free-text search on document and name.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $value = trim((string) $search);
        if ($value === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($value): void {
            $subQuery->where('document_number', 'like', "%{$value}%")
                ->orWhere('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%");
        });
    }
}
