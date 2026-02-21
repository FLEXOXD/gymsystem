<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSuggestionRequest;
use App\Models\ContactSuggestion;
use App\Models\Gym;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSuggestionController extends Controller
{
    public function store(StoreContactSuggestionRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || $user->gym_id === null, 403, __('messages.user_without_gym'));

        ContactSuggestion::query()->create([
            'gym_id' => (int) $user->gym_id,
            'user_id' => (int) $user->id,
            'subject' => (string) $request->validated('subject'),
            'message' => (string) $request->validated('message'),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        return back()->with('status', __('messages.suggestion_sent'));
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_if(! $user || $user->gym_id !== null, 403, __('messages.user_not_authenticated'));

        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,reviewed,all'],
            'gym_id' => ['nullable', 'integer', 'exists:gyms,id'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $status = (string) ($filters['status'] ?? 'pending');
        $queryText = trim((string) ($filters['q'] ?? ''));

        $query = ContactSuggestion::query()
            ->with(['gym:id,name', 'sender:id,name,email', 'reviewedBy:id,name'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($filters['gym_id'])) {
            $query->where('gym_id', (int) $filters['gym_id']);
        }

        if ($queryText !== '') {
            $query->where(function ($builder) use ($queryText): void {
                $builder
                    ->where('subject', 'like', '%'.$queryText.'%')
                    ->orWhere('message', 'like', '%'.$queryText.'%')
                    ->orWhereHas('gym', function ($gymQuery) use ($queryText): void {
                        $gymQuery->where('name', 'like', '%'.$queryText.'%');
                    })
                    ->orWhereHas('sender', function ($userQuery) use ($queryText): void {
                        $userQuery
                            ->where('name', 'like', '%'.$queryText.'%')
                            ->orWhere('email', 'like', '%'.$queryText.'%');
                    });
            });
        }

        return view('superadmin.suggestions.index', [
            'suggestions' => $query->paginate(20)->withQueryString(),
            'gyms' => Gym::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'status' => $status,
                'gym_id' => isset($filters['gym_id']) ? (int) $filters['gym_id'] : null,
                'q' => $queryText,
            ],
        ]);
    }

    public function markReviewed(Request $request, ContactSuggestion $suggestion): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || $user->gym_id !== null, 403, __('messages.user_not_authenticated'));

        if ($suggestion->status !== 'reviewed') {
            $suggestion->forceFill([
                'status' => 'reviewed',
                'reviewed_at' => now(),
                'reviewed_by' => (int) $user->id,
            ])->save();
        }

        return back()->with('status', __('messages.suggestion_marked_reviewed'));
    }
}
