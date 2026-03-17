<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use App\Models\User;
use App\Services\SupportChatBotService;
use App\Services\SupportChatPresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SupportChatController extends Controller
{
    private const LANDING_SESSION_KEY = 'support_chat.landing_conversation_id';

    public function __construct(
        private readonly SupportChatBotService $botService,
        private readonly SupportChatPresenceService $presenceService,
    ) {
    }

    public function landingState(Request $request): JsonResponse
    {
        $conversation = $this->resolveLandingConversation($request, false, []);

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_LANDING, SupportChatMessage::SENDER_VISITOR)
        );
    }

    public function landingQuickReply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action_key' => ['required', 'string', 'max:80'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'gym_name' => ['nullable', 'string', 'max:150'],
        ]);

        $conversation = $this->resolveLandingConversation($request, true, $data);
        $this->applyLandingLeadData($conversation, $data);

        $selectedQuickReply = collect($this->botService->quickReplies(SupportChatConversation::CHANNEL_LANDING))
            ->firstWhere('key', trim((string) $data['action_key']));
        $userMessage = trim((string) ($selectedQuickReply['label'] ?? $data['action_key']));

        $this->appendUserMessage(
            $conversation,
            SupportChatMessage::SENDER_VISITOR,
            null,
            $this->safeLeadName($data['contact_name'] ?? null),
            $userMessage,
            'quick_reply'
        );

        $botReply = $this->botService->quickReplyResponse(SupportChatConversation::CHANNEL_LANDING, (string) $data['action_key']);
        $this->appendBotMessage($conversation, $botReply['text'], 'quick_reply');
        $this->applyEscalationState($conversation, (bool) $botReply['escalate']);

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_LANDING, SupportChatMessage::SENDER_VISITOR)
        );
    }

    public function landingSendMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1400'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'gym_name' => ['nullable', 'string', 'max:150'],
        ]);

        $conversation = $this->resolveLandingConversation($request, true, $data);
        $this->applyLandingLeadData($conversation, $data);

        $this->appendUserMessage(
            $conversation,
            SupportChatMessage::SENDER_VISITOR,
            null,
            $this->safeLeadName($data['contact_name'] ?? null),
            trim((string) $data['message']),
            'text'
        );

        $this->handleBotForFreeMessage($conversation, SupportChatConversation::CHANNEL_LANDING, trim((string) $data['message']));

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_LANDING, SupportChatMessage::SENDER_VISITOR)
        );
    }

    public function gymState(Request $request, string $contextGym): JsonResponse
    {
        $conversation = $this->resolveGymConversation($request, false);

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_GYM_PANEL, SupportChatMessage::SENDER_GYM)
        );
    }

    public function gymQuickReply(Request $request, string $contextGym): JsonResponse
    {
        $data = $request->validate([
            'action_key' => ['required', 'string', 'max:80'],
        ]);

        $conversation = $this->resolveGymConversation($request, true);
        if (! $conversation instanceof SupportChatConversation) {
            abort(404);
        }
        $actor = $request->user();

        $selectedQuickReply = collect($this->botService->quickReplies(SupportChatConversation::CHANNEL_GYM_PANEL))
            ->firstWhere('key', trim((string) $data['action_key']));
        $userMessage = trim((string) ($selectedQuickReply['label'] ?? $data['action_key']));

        $this->appendUserMessage(
            $conversation,
            SupportChatMessage::SENDER_GYM,
            $actor instanceof User ? $actor : null,
            $actor instanceof User ? trim((string) $actor->name) : 'Equipo del gimnasio',
            $userMessage,
            'quick_reply'
        );

        $botReply = $this->botService->quickReplyResponse(SupportChatConversation::CHANNEL_GYM_PANEL, (string) $data['action_key']);
        $this->appendBotMessage($conversation, $botReply['text'], 'quick_reply');
        $this->applyEscalationState($conversation, (bool) $botReply['escalate']);

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_GYM_PANEL, SupportChatMessage::SENDER_GYM)
        );
    }

    public function gymSendMessage(Request $request, string $contextGym): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1400'],
        ]);

        $conversation = $this->resolveGymConversation($request, true);
        if (! $conversation instanceof SupportChatConversation) {
            abort(404);
        }
        $actor = $request->user();
        $message = trim((string) $data['message']);

        $this->appendUserMessage(
            $conversation,
            SupportChatMessage::SENDER_GYM,
            $actor instanceof User ? $actor : null,
            $actor instanceof User ? trim((string) $actor->name) : 'Equipo del gimnasio',
            $message,
            'text'
        );

        $this->handleBotForFreeMessage($conversation, SupportChatConversation::CHANNEL_GYM_PANEL, $message);

        return response()->json(
            $this->buildStatePayload($conversation, SupportChatConversation::CHANNEL_GYM_PANEL, SupportChatMessage::SENDER_GYM)
        );
    }

    /**
     * @param  array<string, mixed>  $leadData
     */
    private function resolveLandingConversation(Request $request, bool $createIfMissing, array $leadData): ?SupportChatConversation
    {
        $conversationId = (int) $request->session()->get(self::LANDING_SESSION_KEY, 0);
        $conversation = null;
        if ($conversationId > 0) {
            $conversation = SupportChatConversation::query()
                ->where('id', $conversationId)
                ->where('channel', SupportChatConversation::CHANNEL_LANDING)
                ->first();
        }

        if ($conversation instanceof SupportChatConversation || ! $createIfMissing) {
            return $conversation;
        }

        $conversation = SupportChatConversation::query()->create([
            'channel' => SupportChatConversation::CHANNEL_LANDING,
            'requester_type' => SupportChatConversation::REQUESTER_VISITOR,
            'visitor_name' => $this->safeLeadName($leadData['contact_name'] ?? null),
            'visitor_email' => $this->safeLeadEmail($leadData['contact_email'] ?? null),
            'visitor_gym_name' => $this->safeLeadGymName($leadData['gym_name'] ?? null),
            'status' => SupportChatConversation::STATUS_BOT,
            'last_message_at' => now(),
        ]);

        $request->session()->put(self::LANDING_SESSION_KEY, (int) $conversation->id);
        $this->seedWelcomeMessage($conversation, SupportChatConversation::CHANNEL_LANDING);

        return $conversation;
    }

    private function resolveGymConversation(Request $request, bool $createIfMissing): ?SupportChatConversation
    {
        $activeGym = $request->attributes->get('active_gym');
        if (! $activeGym instanceof Gym) {
            return null;
        }

        $conversation = SupportChatConversation::query()
            ->where('channel', SupportChatConversation::CHANNEL_GYM_PANEL)
            ->where('gym_id', (int) $activeGym->id)
            ->whereIn('status', [
                SupportChatConversation::STATUS_BOT,
                SupportChatConversation::STATUS_WAITING_AGENT,
                SupportChatConversation::STATUS_ACTIVE,
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->first();

        if ($conversation instanceof SupportChatConversation || ! $createIfMissing) {
            return $conversation;
        }

        $user = $request->user();
        $conversation = SupportChatConversation::query()->create([
            'channel' => SupportChatConversation::CHANNEL_GYM_PANEL,
            'requester_type' => SupportChatConversation::REQUESTER_GYM_USER,
            'gym_id' => (int) $activeGym->id,
            'initiated_by_user_id' => $user instanceof User ? (int) $user->id : null,
            'status' => SupportChatConversation::STATUS_BOT,
            'last_message_at' => now(),
        ]);

        $this->seedWelcomeMessage($conversation, SupportChatConversation::CHANNEL_GYM_PANEL);

        return $conversation;
    }

    /**
     * @param  array<string, mixed>  $leadData
     */
    private function applyLandingLeadData(SupportChatConversation $conversation, array $leadData): void
    {
        $nextName = $this->safeLeadName($leadData['contact_name'] ?? null);
        $nextEmail = $this->safeLeadEmail($leadData['contact_email'] ?? null);
        $nextGymName = $this->safeLeadGymName($leadData['gym_name'] ?? null);

        $dirty = false;
        if ($nextName !== '' && trim((string) $conversation->visitor_name) === '') {
            $conversation->visitor_name = $nextName;
            $dirty = true;
        }
        if ($nextEmail !== '' && trim((string) $conversation->visitor_email) === '') {
            $conversation->visitor_email = $nextEmail;
            $dirty = true;
        }
        if ($nextGymName !== '' && trim((string) $conversation->visitor_gym_name) === '') {
            $conversation->visitor_gym_name = $nextGymName;
            $dirty = true;
        }
        if ($dirty) {
            $conversation->save();
        }
    }

    private function seedWelcomeMessage(SupportChatConversation $conversation, string $context): void
    {
        $profile = $this->botService->assistantProfile($context);
        $this->appendBotMessage($conversation, (string) ($profile['welcome_message'] ?? 'Hola, te ayudamos enseguida.'), 'event');
    }

    private function handleBotForFreeMessage(SupportChatConversation $conversation, string $context, string $message): void
    {
        $status = (string) $conversation->status;
        $representativeOnline = $this->presenceService->isSuperAdminOnline();
        $shouldPauseBotForLiveAgent = $representativeOnline
            && in_array($status, [SupportChatConversation::STATUS_WAITING_AGENT, SupportChatConversation::STATUS_ACTIVE], true);

        if ($shouldPauseBotForLiveAgent) {
            return;
        }

        $botReply = $this->botService->messageResponse($context, $message);
        $this->appendBotMessage($conversation, $botReply['text'], 'text');

        if ((bool) $botReply['escalate']) {
            $this->applyEscalationState($conversation, true);
            return;
        }

        if (in_array((string) $conversation->status, [SupportChatConversation::STATUS_WAITING_AGENT, SupportChatConversation::STATUS_ACTIVE], true) && ! $representativeOnline) {
            $this->appendStatusHintIfNeeded($conversation, $this->botService->agentOfflineMessage());
        }
    }

    private function applyEscalationState(SupportChatConversation $conversation, bool $requested): void
    {
        if (! $requested) {
            return;
        }

        $representativeOnline = $this->presenceService->isSuperAdminOnline();
        $conversation->forceFill([
            'status' => $representativeOnline ? SupportChatConversation::STATUS_ACTIVE : SupportChatConversation::STATUS_WAITING_AGENT,
            'representative_requested_at' => $conversation->representative_requested_at ?? now(),
            'representative_joined_at' => $representativeOnline
                ? ($conversation->representative_joined_at ?? now())
                : $conversation->representative_joined_at,
            'closed_at' => null,
        ])->save();

        $hintMessage = $representativeOnline
            ? $this->botService->agentOnlineMessage()
            : $this->botService->agentOfflineMessage();

        $this->appendStatusHintIfNeeded($conversation, $hintMessage);
    }

    private function appendStatusHintIfNeeded(SupportChatConversation $conversation, string $message): void
    {
        $normalized = trim($message);
        if ($normalized === '') {
            return;
        }

        $lastMessage = $conversation->messages()
            ->latest('id')
            ->first(['id', 'sender_type', 'message', 'created_at']);
        if (
            $lastMessage instanceof SupportChatMessage
            && in_array((string) $lastMessage->sender_type, [SupportChatMessage::SENDER_BOT, SupportChatMessage::SENDER_SYSTEM], true)
            && trim((string) $lastMessage->message) === $normalized
        ) {
            return;
        }

        $this->appendBotMessage($conversation, $normalized, 'event', SupportChatMessage::SENDER_SYSTEM);
    }

    private function appendBotMessage(
        SupportChatConversation $conversation,
        string $message,
        string $messageType = 'text',
        string $senderType = SupportChatMessage::SENDER_BOT
    ): void {
        $normalizedMessage = trim($message);
        if ($normalizedMessage === '') {
            return;
        }

        SupportChatMessage::query()->create([
            'conversation_id' => (int) $conversation->id,
            'sender_type' => $senderType,
            'sender_label' => $senderType === SupportChatMessage::SENDER_SYSTEM ? 'Sistema' : 'Bot de soporte',
            'message' => $normalizedMessage,
            'message_type' => trim($messageType) !== '' ? trim($messageType) : 'text',
            'read_by_superadmin_at' => now(),
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();
    }

    private function appendUserMessage(
        SupportChatConversation $conversation,
        string $senderType,
        ?User $senderUser,
        string $senderLabel,
        string $message,
        string $messageType = 'text'
    ): void {
        $normalizedMessage = trim($message);
        if ($normalizedMessage === '') {
            return;
        }

        SupportChatMessage::query()->create([
            'conversation_id' => (int) $conversation->id,
            'sender_type' => $senderType,
            'sender_user_id' => $senderUser?->id,
            'sender_label' => trim($senderLabel) !== '' ? trim($senderLabel) : null,
            'message' => $normalizedMessage,
            'message_type' => trim($messageType) !== '' ? trim($messageType) : 'text',
            'read_by_superadmin_at' => null,
        ]);

        if ((string) $conversation->status === SupportChatConversation::STATUS_CLOSED) {
            $conversation->forceFill([
                'status' => SupportChatConversation::STATUS_BOT,
                'closed_at' => null,
            ])->save();
        }

        $conversation->forceFill(['last_message_at' => now()])->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStatePayload(?SupportChatConversation $conversation, string $context, string $mineSenderType): array
    {
        $assistantProfile = $this->botService->assistantProfile($context);
        $quickReplies = $this->botService->quickReplies($context);
        $activeRepresentative = $this->presenceService->activeRepresentative();
        $representativeOnline = $activeRepresentative !== null;

        if (! $conversation instanceof SupportChatConversation) {
            return [
                'ok' => true,
                'context' => $context,
                'assistant' => $assistantProfile,
                'quick_replies' => $quickReplies,
                'representative_online' => $representativeOnline,
                'representative_name' => trim((string) ($activeRepresentative['name'] ?? 'SuperAdmin')),
                'conversation' => null,
                'messages' => [],
            ];
        }

        $conversation->loadMissing([
            'gym:id,name,logo_path',
            'messages' => static fn ($query) => $query->orderBy('id')->limit(120),
        ]);

        /** @var Collection<int, SupportChatMessage> $messages */
        $messages = $conversation->messages;

        return [
            'ok' => true,
            'context' => $context,
            'assistant' => $assistantProfile,
            'quick_replies' => $quickReplies,
            'representative_online' => $representativeOnline,
            'representative_name' => trim((string) ($activeRepresentative['name'] ?? 'SuperAdmin')),
            'conversation' => [
                'id' => (int) $conversation->id,
                'status' => (string) $conversation->status,
                'status_label' => $conversation->statusLabel(),
                'source_label' => $conversation->sourceLabel(),
                'requester_label' => $conversation->requesterLabel(),
                'display_name' => $conversation->displayName(),
                'gym_name' => $conversation->gym?->name ?: $conversation->visitor_gym_name,
                'contact_name' => $conversation->visitor_name,
                'contact_email' => $conversation->visitor_email,
            ],
            'messages' => $messages->map(static function (SupportChatMessage $message) use ($mineSenderType): array {
                return [
                    'id' => (int) $message->id,
                    'sender_type' => (string) $message->sender_type,
                    'sender_label' => trim((string) ($message->sender_label ?? '')),
                    'text' => (string) $message->message,
                    'message_type' => (string) ($message->message_type ?? 'text'),
                    'mine' => (string) $message->sender_type === $mineSenderType,
                    'created_at' => $message->created_at?->format('d/m H:i'),
                ];
            })->values()->all(),
        ];
    }

    private function safeLeadName(mixed $value): string
    {
        return trim((string) $value);
    }

    private function safeLeadEmail(mixed $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function safeLeadGymName(mixed $value): string
    {
        return trim((string) $value);
    }
}
