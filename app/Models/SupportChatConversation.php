<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportChatConversation extends Model
{
    use HasFactory;

    public const CHANNEL_LANDING = 'landing';
    public const CHANNEL_GYM_PANEL = 'gym_panel';

    public const REQUESTER_VISITOR = 'visitor';
    public const REQUESTER_GYM_USER = 'gym_user';

    public const STATUS_BOT = 'bot';
    public const STATUS_WAITING_AGENT = 'waiting_agent';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'channel',
        'requester_type',
        'gym_id',
        'initiated_by_user_id',
        'visitor_name',
        'visitor_email',
        'visitor_gym_name',
        'subject',
        'status',
        'representative_requested_at',
        'representative_joined_at',
        'closed_at',
        'last_message_at',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'representative_requested_at' => 'datetime',
            'representative_joined_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_message_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportChatMessage::class, 'conversation_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function statusLabel(): string
    {
        return match ((string) $this->status) {
            self::STATUS_WAITING_AGENT => 'Esperando representante',
            self::STATUS_ACTIVE => 'En conversacion',
            self::STATUS_CLOSED => 'Cerrado',
            default => 'Atendido por bot',
        };
    }

    public function sourceLabel(): string
    {
        return (string) $this->channel === self::CHANNEL_GYM_PANEL
            ? 'Panel del gimnasio'
            : 'Pagina principal';
    }

    public function requesterLabel(): string
    {
        return (string) $this->requester_type === self::REQUESTER_GYM_USER ? 'Gimnasio logeado' : 'Visitante';
    }

    public function displayName(): string
    {
        $gymName = trim((string) ($this->gym?->name ?? ''));
        if ($gymName !== '') {
            return $gymName;
        }

        $visitorGymName = trim((string) $this->visitor_gym_name);
        if ($visitorGymName !== '') {
            return $visitorGymName;
        }

        $visitorName = trim((string) $this->visitor_name);
        if ($visitorName !== '') {
            return $visitorName;
        }

        return 'Gimnasio sin identificar';
    }
}
