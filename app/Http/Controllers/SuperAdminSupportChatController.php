<?php

namespace App\Http\Controllers;

use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use App\Models\User;
use App\Services\SupportChatPresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SuperAdminSupportChatController extends Controller
{
    public function __construct(
        private readonly SupportChatPresenceService $presenceService,
    ) {
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof User || ! $user->hasRole(User::ROLE_SUPERADMIN)) {
            abort(403);
        }

        $this->presenceService->touchSuperAdmin($user);

        return response()->json([
            'ok' => true,
            'online' => true,
        ]);
    }

    public function unreadCountJson(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof User || ! $user->hasRole(User::ROLE_SUPERADMIN)) {
            abort(403);
        }

        return response()->json([
            'ok' => true,
            'unread' => $this->unreadCount(),
        ]);
    }

    public function reply(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1400'],
        ]);

        $user = $request->user();
        if (! $user instanceof User) {
            abort(403);
        }

        SupportChatMessage::query()->create([
            'conversation_id' => (int) $conversation->id,
            'sender_type' => SupportChatMessage::SENDER_SUPERADMIN,
            'sender_user_id' => (int) $user->id,
            'sender_label' => trim((string) $user->name),
            'message' => trim((string) $data['message']),
            'message_type' => 'text',
            'read_by_superadmin_at' => now(),
        ]);

        $conversation->forceFill([
            'status' => SupportChatConversation::STATUS_ACTIVE,
            'representative_requested_at' => $conversation->representative_requested_at ?? now(),
            'representative_joined_at' => $conversation->representative_joined_at ?? now(),
            'closed_at' => null,
            'last_message_at' => now(),
        ])->save();

        $this->markConversationAsRead($conversation);
        $this->presenceService->touchSuperAdmin($user);

        return back()->with('status', 'Respuesta enviada al chat de soporte.');
    }

    public function updateStatus(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:bot,waiting_agent,active,closed'],
        ]);

        $nextStatus = (string) $data['status'];
        $forceFill = [
            'status' => $nextStatus,
            'closed_at' => $nextStatus === SupportChatConversation::STATUS_CLOSED ? now() : null,
        ];

        if (in_array($nextStatus, [SupportChatConversation::STATUS_WAITING_AGENT, SupportChatConversation::STATUS_ACTIVE], true)) {
            $forceFill['representative_requested_at'] = $conversation->representative_requested_at ?? now();
        }
        if ($nextStatus === SupportChatConversation::STATUS_ACTIVE) {
            $forceFill['representative_joined_at'] = $conversation->representative_joined_at ?? now();
        }

        $conversation->forceFill($forceFill)->save();

        return back()->with('status', 'Estado de conversación actualizado.');
    }

    public function markRead(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $this->markConversationAsRead($conversation);

        return back()->with('status', 'Conversación marcada como leída.');
    }

    public function unreadCount(): int
    {
        if (! Schema::hasTable('support_chat_conversations') || ! Schema::hasTable('support_chat_messages')) {
            return 0;
        }

        return SupportChatConversation::query()
            ->whereIn('status', [
                SupportChatConversation::STATUS_BOT,
                SupportChatConversation::STATUS_WAITING_AGENT,
                SupportChatConversation::STATUS_ACTIVE,
            ])
            ->whereHas('messages', function ($query): void {
                $query
                    ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                    ->whereNull('read_by_superadmin_at');
            })
            ->count();
    }

    private function markConversationAsRead(SupportChatConversation $conversation): void
    {
        $conversation->messages()
            ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
            ->whereNull('read_by_superadmin_at')
            ->update(['read_by_superadmin_at' => now()]);
    }
}
