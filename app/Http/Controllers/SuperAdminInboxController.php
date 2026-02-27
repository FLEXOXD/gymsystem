<?php

namespace App\Http\Controllers;

use App\Models\LandingContactMessage;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuperAdminInboxController extends Controller
{
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

        return back()->with('status', 'Mensaje marcado como leido.');
    }

    private function renderInbox(Request $request, ?int $selectedId): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:all,unread,read'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $status = (string) ($filters['status'] ?? 'all');
        $queryText = trim((string) ($filters['q'] ?? ''));

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
        ]);
    }
}
