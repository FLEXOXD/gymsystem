<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingContactMessage extends Model
{
    use HasFactory;

    public const BELL_WINDOW_HOURS = 4;
    public const INBOX_RETENTION_HOURS = 24;
    public const PRUNE_HOURS = 24;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'message',
        'read_at',
        'read_by',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function scopeWithinBellWindow($query)
    {
        return $query->where('created_at', '>=', now()->subHours(self::BELL_WINDOW_HOURS));
    }

    public function scopeWithinInboxWindow($query)
    {
        return $query->where('created_at', '>=', now()->subHours(self::INBOX_RETENTION_HOURS));
    }

    public function scopeWithinRetention($query)
    {
        return $this->scopeWithinInboxWindow($query);
    }
}
