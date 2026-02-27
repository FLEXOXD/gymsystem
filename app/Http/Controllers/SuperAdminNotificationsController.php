<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\SubscriptionNotification;
use App\Services\SubscriptionNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuperAdminNotificationsController extends Controller
{
    public function __construct(
        private readonly SubscriptionNotificationService $notificationService
    ) {
    }

    /**
     * Pending notifications for a date.
     */
    public function index(Request $request): View
    {
        $data = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $selectedDate = isset($data['date'])
            ? Carbon::parse((string) $data['date'])->toDateString()
            : Carbon::today()->toDateString();

        $this->notificationService->generateDueNotifications($selectedDate);

        $notifications = SubscriptionNotification::query()
            ->with(['gym:id,name', 'subscription:id,gym_id,plan_name,ends_at'])
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->whereDate('scheduled_for', $selectedDate)
            ->where('status', 'pending')
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        return view('superadmin.notifications.index', [
            'selectedDate' => $selectedDate,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Notification history (sent/skipped).
     */
    public function history(Request $request): View
    {
        $data = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'gym_id' => ['nullable', 'integer', 'exists:gyms,id'],
        ]);

        $query = SubscriptionNotification::query()
            ->with(['gym:id,name', 'subscription:id,plan_name,ends_at', 'createdBy:id,name'])
            ->whereHas('gym', fn ($builder) => $builder->withoutDemoSessions())
            ->whereIn('status', ['sent', 'skipped'])
            ->orderByDesc('updated_at');

        if (! empty($data['gym_id'])) {
            $query->where('gym_id', (int) $data['gym_id']);
        }

        if (! empty($data['date_from'])) {
            $query->whereDate('scheduled_for', '>=', (string) $data['date_from']);
        }

        if (! empty($data['date_to'])) {
            $query->whereDate('scheduled_for', '<=', (string) $data['date_to']);
        }

        return view('superadmin.notifications.history', [
            'notifications' => $query->paginate(25)->withQueryString(),
            'gyms' => Gym::query()->withoutDemoSessions()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'date_from' => $data['date_from'] ?? '',
                'date_to' => $data['date_to'] ?? '',
                'gym_id' => isset($data['gym_id']) ? (int) $data['gym_id'] : null,
            ],
        ]);
    }

    public function markSent(Request $request, SubscriptionNotification $notification): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $this->notificationService->markAsSent((int) $notification->id, $userId);

        return back()->with('status', 'Notificación marcada como enviada.');
    }

    public function markSkipped(Request $request, SubscriptionNotification $notification): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $this->notificationService->markAsSkipped((int) $notification->id, $userId);

        return back()->with('status', 'Notificación marcada como omitida.');
    }
}
