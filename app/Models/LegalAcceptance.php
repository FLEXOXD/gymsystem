<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalAcceptance extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'gym_id',
        'full_name',
        'email',
        'document_key',
        'document_label',
        'legal_version',
        'accepted',
        'accepted_via',
        'session_id',
        'source_url',
        'location_permission',
        'latitude',
        'longitude',
        'location_accuracy_m',
        'contract_code',
        'accepted_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted' => 'bool',
            'accepted_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'location_accuracy_m' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class, 'gym_id');
    }
}
