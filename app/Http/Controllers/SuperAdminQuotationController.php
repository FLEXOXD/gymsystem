<?php

namespace App\Http\Controllers;

use App\Models\LandingQuoteRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuperAdminQuotationController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderQuotes($request, null);
    }

    public function show(Request $request, int $quote): View
    {
        return $this->renderQuotes($request, $quote);
    }

    public function markRead(Request $request, LandingQuoteRequest $quote): RedirectResponse
    {
        if ($quote->read_at === null) {
            $quote->forceFill([
                'read_at' => now(),
                'read_by' => (int) $request->user()->id,
            ])->save();
        }

        return back()->with('status', 'Solicitud marcada como revisada.');
    }

    private function renderQuotes(Request $request, ?int $selectedId): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:all,unread,read'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $status = (string) ($filters['status'] ?? 'all');
        $queryText = trim((string) ($filters['q'] ?? ''));

        $query = LandingQuoteRequest::query()
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
                    ->orWhere('phone_number', 'like', '%'.$queryText.'%')
                    ->orWhere('country', 'like', '%'.$queryText.'%')
                    ->orWhere('requested_plan', 'like', '%'.$queryText.'%')
                    ->orWhere('notes', 'like', '%'.$queryText.'%');
            });
        }

        $quotes = $query->paginate(18)->withQueryString();

        $selectedQuote = null;
        if ($selectedId !== null) {
            $selectedQuote = LandingQuoteRequest::query()->find($selectedId);
        }

        return view('superadmin.quotations.index', [
            'quotes' => $quotes,
            'selectedQuote' => $selectedQuote,
            'filters' => [
                'status' => $status,
                'q' => $queryText,
            ],
            'unreadCount' => LandingQuoteRequest::query()->whereNull('read_at')->count(),
            'totalCount' => LandingQuoteRequest::query()->count(),
        ]);
    }
}
