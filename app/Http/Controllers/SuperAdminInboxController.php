<?php

namespace App\Http\Controllers;

use App\Models\LandingContactMessage;
use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use App\Services\SupportChatPresenceService;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SuperAdminInboxController extends Controller
{
    public function __construct(
        private readonly SupportChatPresenceService $presenceService,
    ) {
    }

    public function index(Request $request): View
    {
        return $this->renderInbox($request, null);
    }

    public function show(Request $request, int $message): View
    {
        return $this->renderInbox($request, $message);
    }

    public function markRead(Request $request, LandingContactMessage $message): RedirectResponse
    {
        if ($message->read_at === null) {
            $message->forceFill([
                'read_at' => now(),
                'read_by' => (int) $request->user()->id,
            ])->save();
        }

        return back()->with('status', 'Mensaje marcado como leído.');
    }

    private function renderInbox(Request $request, ?int $selectedId): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:all,unread,read'],
            'q' => ['nullable', 'string', 'max:120'],
            'support' => ['nullable', 'integer', 'min:1'],
            'support_status' => ['nullable', 'in:all,unread,bot,waiting_agent,active,closed'],
            'support_q' => ['nullable', 'string', 'max:120'],
        ]);

        $status = (string) ($filters['status'] ?? 'all');
        $queryText = trim((string) ($filters['q'] ?? ''));
        $supportStatus = (string) ($filters['support_status'] ?? 'all');
        $supportQueryText = trim((string) ($filters['support_q'] ?? ''));
        $selectedSupportId = (int) ($filters['support'] ?? 0);

        $query = LandingContactMessage::query()
            ->withinInboxWindow()
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at');

        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($queryText !== '') {
            $query->where(function ($builder) use ($queryText): void {
                $builder
                    ->where('first_name', 'like', '%'.$queryText.'%')
                    ->orWhere('last_name', 'like', '%'.$queryText.'%')
                    ->orWhere('email', 'like', '%'.$queryText.'%')
                    ->orWhere('message', 'like', '%'.$queryText.'%');
            });
        }

        $messages = $query->paginate(18)->withQueryString();

        $selectedMessage = null;
        if ($selectedId !== null) {
            $selectedMessage = LandingContactMessage::query()
                ->withinInboxWindow()
                ->find($selectedId);
        }
        if (! $selectedMessage && $messages->isNotEmpty()) {
            $selectedMessage = $messages->first();
        }

        $autoMarkRead = $selectedId !== null;
        if ($autoMarkRead && $selectedMessage && $selectedMessage->read_at === null) {
            $selectedMessage->forceFill([
                'read_at' => now(),
                'read_by' => (int) $request->user()->id,
            ])->save();
        }

        $nextUnread = LandingContactMessage::query()
            ->withinInboxWindow()
            ->whereNull('read_at')
            ->orderBy('created_at')
            ->first(['created_at']);
        $nextUnreadMinutesLeft = null;
        if ($nextUnread?->created_at instanceof CarbonInterface) {
            $nextUnreadMinutesLeft = max(0, now()->diffInMinutes(
                $nextUnread->created_at->copy()->addHours(LandingContactMessage::INBOX_RETENTION_HOURS),
                false
            ));
        }

        $supportConversations = new LengthAwarePaginator(
            collect(),
            0,
            14,
            max(1, (int) $request->query('support_page', 1)),
            ['pageName' => 'support_page', 'path' => $request->url()]
        );
        $supportConversations->appends($request->query());
        $selectedSupportConversation = null;
        $selectedSupportMessages = collect();
        $supportUnreadCount = 0;
        $supportTotalCount = 0;
        $supportLoadError = false;

        $supportSchemaReady = Schema::hasTable('support_chat_conversations')
            && Schema::hasTable('support_chat_messages');

        if ($supportSchemaReady) {
            try {
                $supportQuery = SupportChatConversation::query()
                    ->with([
                        'gym:id,name,logo_path',
                        'initiatedBy:id,name,email',
                        'latestMessage' => static function (Builder $builder): void {
                            $builder->select([
                                'support_chat_messages.id',
                                'support_chat_messages.conversation_id',
                                'support_chat_messages.message',
                                'support_chat_messages.created_at',
                                'support_chat_messages.sender_type',
                            ]);
                        },
                    ])
                    ->withCount([
                        'messages as unread_for_superadmin_count' => static function (Builder $builder): void {
                            $builder
                                ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                                ->whereNull('read_by_superadmin_at');
                        },
                    ])
                    ->orderByRaw('CASE WHEN unread_for_superadmin_count > 0 THEN 0 ELSE 1 END')
                    ->orderByDesc('last_message_at')
                    ->orderByDesc('id');

                if ($supportStatus === 'unread') {
                    $supportQuery->whereHas('messages', static function (Builder $builder): void {
                        $builder
                            ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                            ->whereNull('read_by_superadmin_at');
                    });
                } elseif (in_array($supportStatus, [
                    SupportChatConversation::STATUS_BOT,
                    SupportChatConversation::STATUS_WAITING_AGENT,
                    SupportChatConversation::STATUS_ACTIVE,
                    SupportChatConversation::STATUS_CLOSED,
                ], true)) {
                    $supportQuery->where('status', $supportStatus);
                }

                if ($supportQueryText !== '') {
                    $supportQuery->where(function (Builder $builder) use ($supportQueryText): void {
                        $builder
                            ->where('visitor_name', 'like', '%'.$supportQueryText.'%')
                            ->orWhere('visitor_email', 'like', '%'.$supportQueryText.'%')
                            ->orWhere('visitor_gym_name', 'like', '%'.$supportQueryText.'%')
                            ->orWhere('subject', 'like', '%'.$supportQueryText.'%')
                            ->orWhereHas('gym', static function (Builder $gymBuilder) use ($supportQueryText): void {
                                $gymBuilder->where('name', 'like', '%'.$supportQueryText.'%');
                            })
                            ->orWhereHas('initiatedBy', static function (Builder $userBuilder) use ($supportQueryText): void {
                                $userBuilder
                                    ->where('name', 'like', '%'.$supportQueryText.'%')
                                    ->orWhere('email', 'like', '%'.$supportQueryText.'%');
                            });
                    });
                }

                $supportConversations = $supportQuery
                    ->paginate(14, ['*'], 'support_page')
                    ->withQueryString();

                if ($selectedSupportId > 0) {
                    $selectedSupportConversation = SupportChatConversation::query()
                        ->with(['gym:id,name,logo_path', 'initiatedBy:id,name,email'])
                        ->find($selectedSupportId);
                }
                if (! $selectedSupportConversation && $supportConversations->isNotEmpty()) {
                    $selectedSupportConversation = $supportConversations->first();
                }

                if ($selectedSupportConversation instanceof SupportChatConversation) {
                    $selectedSupportConversation->load([
                        'messages' => static fn (Builder $builder) => $builder->orderBy('id')->with('senderUser:id,name'),
                    ]);

                    $selectedSupportConversation->messages()
                        ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                        ->whereNull('read_by_superadmin_at')
                        ->update(['read_by_superadmin_at' => now()]);

                    $selectedSupportMessages = $selectedSupportConversation->messages;
                }

                $supportUnreadCount = SupportChatConversation::query()
                    ->whereIn('status', [
                        SupportChatConversation::STATUS_BOT,
                        SupportChatConversation::STATUS_WAITING_AGENT,
                        SupportChatConversation::STATUS_ACTIVE,
                    ])
                    ->whereHas('messages', static function (Builder $builder): void {
                        $builder
                            ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                            ->whereNull('read_by_superadmin_at');
                    })
                    ->count();
                $supportTotalCount = SupportChatConversation::query()->count();
            } catch (Throwable $exception) {
                report($exception);
                $supportLoadError = true;
            }
        }

        $activeRepresentative = $this->presenceService->activeRepresentative();

        return view('superadmin.inbox.index', [
            'messages' => $messages,
            'selectedMessage' => $selectedMessage,
            'filters' => [
                'status' => $status,
                'q' => $queryText,
            ],
            'unreadCount' => LandingContactMessage::query()->withinInboxWindow()->whereNull('read_at')->count(),
            'totalCount' => LandingContactMessage::query()->withinInboxWindow()->count(),
            'nextUnreadMinutesLeft' => $nextUnreadMinutesLeft,
            'supportConversations' => $supportConversations,
            'selectedSupportConversation' => $selectedSupportConversation,
            'selectedSupportMessages' => $selectedSupportMessages,
            'supportFilters' => [
                'status' => $supportStatus,
                'q' => $supportQueryText,
            ],
            'supportUnreadCount' => $supportUnreadCount,
            'supportTotalCount' => $supportTotalCount,
            'supportSchemaReady' => $supportSchemaReady,
            'supportLoadError' => $supportLoadError,
            'supportRepresentativeOnline' => $activeRepresentative !== null,
            'supportRepresentativeName' => trim((string) ($activeRepresentative['name'] ?? 'SuperAdmin')),
        ]);
    }
}
