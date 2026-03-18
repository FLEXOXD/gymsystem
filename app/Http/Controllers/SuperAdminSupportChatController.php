<?php

namespace App\Http\Controllers;

use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use App\Models\User;
use App\Services\SupportChatLifecycleService;
use App\Services\SupportChatPresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SuperAdminSupportChatController extends Controller
{
    public function __construct(
        private readonly SupportChatPresenceService $presenceService,
        private readonly SupportChatLifecycleService $lifecycleService,
    ) {
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $this->lifecycleService->runInactivitySweep();

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
        $this->lifecycleService->runInactivitySweep();

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
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1400'],
        ]);

        $user = $request->user();
        if (! $user instanceof User) {
            abort(403);
        }

        $attributes = [
            'conversation_id' => (int) $conversation->id,
            'sender_type' => SupportChatMessage::SENDER_SUPERADMIN,
            'sender_user_id' => (int) $user->id,
            'message' => trim((string) $data['message']),
        ];

        if (Schema::hasColumn('support_chat_messages', 'sender_label')) {
            $attributes['sender_label'] = trim((string) $user->name);
        }
        if (Schema::hasColumn('support_chat_messages', 'message_type')) {
            $attributes['message_type'] = 'text';
        }
        if (Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            $attributes['read_by_superadmin_at'] = now();
        }

        SupportChatMessage::query()->create($attributes);

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
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);

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

        return back()->with('status', 'Estado de conversacion actualizado.');
    }

    public function markRead(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);
        $this->markConversationAsRead($conversation);

        return back()->with('status', 'Conversacion marcada como leida.');
    }

    public function finalize(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $this->lifecycleService->finalizeAndDelete($conversation, 'manual_superadmin');

        $query = $request->only(['status', 'q', 'page', 'support_status', 'support_q', 'support_page']);
        unset($query['support']);

        return redirect()
            ->route('superadmin.inbox.index', $query)
            ->with('status', 'Conversacion finalizada y eliminada.');
    }

    public function unreadCount(): int
    {
        if (! Schema::hasTable('support_chat_conversations') || ! Schema::hasTable('support_chat_messages')) {
            return 0;
        }

        if (! Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            return 0;
        }

        try {
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
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    private function markConversationAsRead(SupportChatConversation $conversation): void
    {
        if (! Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            return;
        }

        $conversation->messages()
            ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
            ->whereNull('read_by_superadmin_at')
            ->update(['read_by_superadmin_at' => now()]);
    }
}

