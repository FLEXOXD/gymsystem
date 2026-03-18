<?php

namespace App\Services;

use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SupportChatLifecycleService
{
    public function runInactivitySweep(?int $conversationId = null): void
    {
        if (! $this->inactivityEnabled() || ! $this->supportSchemaReady()) {
            return;
        }

        $this->purgeExpiredClosedConversations($conversationId);

        $conversationSelect = ['id', 'status', 'last_message_at'];
        if (Schema::hasColumn('support_chat_conversations', 'meta')) {
            $conversationSelect[] = 'meta';
        }

        $query = SupportChatConversation::query()
            ->whereIn('status', [
                SupportChatConversation::STATUS_BOT,
            ])
            ->orderBy('id');

        if (($conversationId ?? 0) > 0) {
            $query->whereKey((int) $conversationId);
        }

        $conversations = $query->get($conversationSelect);
        foreach ($conversations as $conversation) {
            try {
                $this->processConversation($conversation);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    public function finalizeAndDelete(SupportChatConversation $conversation, string $reason = 'manual'): void
    {
        if (! $this->supportSchemaReady()) {
            return;
        }

        $meta = $this->normalizedMeta($conversation);
        unset($meta['idle']);
        $meta['finalization'] = [
            'reason' => trim($reason) !== '' ? trim($reason) : 'manual',
            'finalized_at' => now()->toIso8601String(),
            'purge_after_minutes' => $this->closeAfterMinutes(),
        ];

        $conversation->forceFill([
            'status' => SupportChatConversation::STATUS_CLOSED,
            'closed_at' => now(),
        ])->save();

        $this->saveConversationMeta($conversation, $meta);
    }

    private function processConversation(SupportChatConversation $conversation): void
    {
        $latestMessage = SupportChatMessage::query()
            ->where('conversation_id', (int) $conversation->id)
            ->latest('id')
            ->first(['id', 'sender_type', 'created_at']);

        if (! $latestMessage instanceof SupportChatMessage) {
            return;
        }

        $latestSenderType = (string) $latestMessage->sender_type;
        $meta = $this->normalizedMeta($conversation);
        $idleMeta = is_array($meta['idle'] ?? null) ? $meta['idle'] : [];
        $anchorMessageId = max(0, (int) ($idleMeta['anchor_message_id'] ?? 0));
        $reminderMessageId = max(0, (int) ($idleMeta['reminder_message_id'] ?? 0));
        $reminderSentAt = trim((string) ($idleMeta['reminder_sent_at'] ?? ''));

        if ($this->isClientSenderType($latestSenderType)) {
            $this->clearIdleMeta($conversation, $meta);

            return;
        }

        if (! $this->isSupportSenderType($latestSenderType)) {
            return;
        }

        if ((int) $latestMessage->id !== $reminderMessageId && (int) $latestMessage->id !== $anchorMessageId) {
            $meta['idle'] = [
                'anchor_message_id' => (int) $latestMessage->id,
                'reminder_sent_at' => null,
                'reminder_message_id' => null,
            ];
            $this->saveConversationMeta($conversation, $meta);
            $anchorMessageId = (int) $latestMessage->id;
            $reminderMessageId = 0;
            $reminderSentAt = '';
        }

        if ($anchorMessageId <= 0) {
            $anchorMessageId = (int) $latestMessage->id;
            $meta['idle'] = [
                'anchor_message_id' => $anchorMessageId,
                'reminder_sent_at' => null,
                'reminder_message_id' => null,
            ];
            $this->saveConversationMeta($conversation, $meta);
            $reminderMessageId = 0;
            $reminderSentAt = '';
        }

        $anchorMessage = (int) $latestMessage->id === $anchorMessageId
            ? $latestMessage
            : SupportChatMessage::query()
                ->where('conversation_id', (int) $conversation->id)
                ->whereKey($anchorMessageId)
                ->first(['id', 'sender_type', 'created_at']);

        if (! $anchorMessage instanceof SupportChatMessage) {
            $anchorMessage = $latestMessage;
            $anchorMessageId = (int) $latestMessage->id;
            $meta['idle'] = [
                'anchor_message_id' => $anchorMessageId,
                'reminder_sent_at' => null,
                'reminder_message_id' => null,
            ];
            $this->saveConversationMeta($conversation, $meta);
            $reminderMessageId = 0;
            $reminderSentAt = '';
        }

        if ($this->hasClientReplyAfter((int) $conversation->id, $anchorMessageId)) {
            $this->clearIdleMeta($conversation, $meta);

            return;
        }

        $minutesSinceAnchor = max(0, (int) ($anchorMessage->created_at?->diffInMinutes(now(), false) ?? 0));

        $reminderAfterMinutes = $this->reminderAfterMinutes();
        $closeAfterMinutes = $this->closeAfterMinutes();

        if (($reminderSentAt === '' || $reminderMessageId <= 0) && $minutesSinceAnchor >= $reminderAfterMinutes) {
            $reminder = $this->appendReminderMessage($conversation);
            if ($reminder instanceof SupportChatMessage) {
                $meta['idle'] = [
                    'anchor_message_id' => $anchorMessageId,
                    'reminder_sent_at' => now()->toIso8601String(),
                    'reminder_message_id' => (int) $reminder->id,
                ];
                $this->saveConversationMeta($conversation, $meta);
            }

            return;
        }

        if (($reminderSentAt !== '' || $reminderMessageId > 0) && $minutesSinceAnchor >= $closeAfterMinutes) {
            $this->finalizeAndDelete($conversation, 'inactivity_timeout');
        }
    }

    private function purgeExpiredClosedConversations(?int $conversationId = null): void
    {
        $query = SupportChatConversation::query()
            ->where('status', SupportChatConversation::STATUS_CLOSED)
            ->whereNotNull('closed_at')
            ->where('closed_at', '<=', now()->subMinutes($this->closeAfterMinutes()))
            ->orderBy('id');

        if (($conversationId ?? 0) > 0) {
            $query->whereKey((int) $conversationId);
        }

        $expiredConversations = $query->get(['id']);
        foreach ($expiredConversations as $conversation) {
            $this->deleteConversationNow($conversation);
        }
    }

    private function deleteConversationNow(SupportChatConversation $conversation): void
    {
        DB::transaction(function () use ($conversation): void {
            SupportChatMessage::query()
                ->where('conversation_id', (int) $conversation->id)
                ->delete();

            SupportChatConversation::query()
                ->whereKey((int) $conversation->id)
                ->delete();
        });
    }

    private function appendReminderMessage(SupportChatConversation $conversation): ?SupportChatMessage
    {
        $message = trim((string) config(
            'support_chat.inactivity.reminder_message',
            'Sigues ahi? Responde por favor para continuar con soporte.'
        ));
        if ($message === '') {
            return null;
        }

        $recentReminder = SupportChatMessage::query()
            ->where('conversation_id', (int) $conversation->id)
            ->where('sender_type', SupportChatMessage::SENDER_SYSTEM)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->latest('id')
            ->first(['id', 'sender_type', 'created_at']);
        if ($recentReminder instanceof SupportChatMessage) {
            return $recentReminder;
        }

        $attributes = [
            'conversation_id' => (int) $conversation->id,
            'sender_type' => SupportChatMessage::SENDER_SYSTEM,
            'message' => $message,
        ];

        if (Schema::hasColumn('support_chat_messages', 'sender_label')) {
            $attributes['sender_label'] = 'Sistema';
        }
        if (Schema::hasColumn('support_chat_messages', 'message_type')) {
            $attributes['message_type'] = 'event';
        }
        if (Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            $attributes['read_by_superadmin_at'] = now();
        }

        $newMessage = SupportChatMessage::query()->create($attributes);

        if (Schema::hasColumn('support_chat_conversations', 'last_message_at')) {
            $conversation->forceFill(['last_message_at' => now()])->save();
        }

        return $newMessage;
    }

    private function clearIdleMeta(SupportChatConversation $conversation, array $meta): void
    {
        if (! array_key_exists('idle', $meta)) {
            return;
        }

        unset($meta['idle']);
        $this->saveConversationMeta($conversation, $meta);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizedMeta(SupportChatConversation $conversation): array
    {
        return is_array($conversation->meta) ? $conversation->meta : [];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function saveConversationMeta(SupportChatConversation $conversation, array $meta): void
    {
        if (! Schema::hasColumn('support_chat_conversations', 'meta')) {
            return;
        }

        $conversation->forceFill(['meta' => $meta])->save();
    }

    private function hasClientReplyAfter(int $conversationId, int $messageId): bool
    {
        return SupportChatMessage::query()
            ->where('conversation_id', $conversationId)
            ->where('id', '>', $messageId)
            ->whereIn('sender_type', [
                SupportChatMessage::SENDER_VISITOR,
                SupportChatMessage::SENDER_GYM,
            ])
            ->exists();
    }

    private function isClientSenderType(string $senderType): bool
    {
        return in_array($senderType, [
            SupportChatMessage::SENDER_VISITOR,
            SupportChatMessage::SENDER_GYM,
        ], true);
    }

    private function isSupportSenderType(string $senderType): bool
    {
        return in_array($senderType, [
            SupportChatMessage::SENDER_SUPERADMIN,
            SupportChatMessage::SENDER_BOT,
            SupportChatMessage::SENDER_SYSTEM,
        ], true);
    }

    private function inactivityEnabled(): bool
    {
        return (bool) config('support_chat.inactivity.enabled', true);
    }

    private function reminderAfterMinutes(): int
    {
        $value = max(1, (int) config('support_chat.inactivity.reminder_after_minutes', 10));

        return min($this->closeAfterMinutes() - 1, $value);
    }

    private function closeAfterMinutes(): int
    {
        return max(2, (int) config('support_chat.inactivity.close_after_minutes', 15));
    }

    private function supportSchemaReady(): bool
    {
        return Schema::hasTable('support_chat_conversations')
            && Schema::hasTable('support_chat_messages');
    }
}
