<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportChatMessage extends Model
{
    use HasFactory;

    public const SENDER_VISITOR = 'visitor';
    public const SENDER_GYM = 'gym';
    public const SENDER_SUPERADMIN = 'superadmin';
    public const SENDER_BOT = 'bot';
    public const SENDER_SYSTEM = 'system';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_user_id',
        'sender_label',
        'message',
        'message_type',
        'read_by_superadmin_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_by_superadmin_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportChatConversation::class, 'conversation_id');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function isFromGymSide(): bool
    {
        return in_array((string) $this->sender_type, [self::SENDER_VISITOR, self::SENDER_GYM], true);
    }
}

