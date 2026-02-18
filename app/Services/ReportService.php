<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get income and expense summary for a date range.
     *
     * @return array<string, float|int>
     */
    public function getIncomeSummary(int $gymId, Carbon $from, Carbon $to): array
    {
        $totals = CashMovement::query()
            ->where('gym_id', $gymId)
            ->whereBetween('occurred_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense")
            ->selectRaw('COUNT(*) as total_movements')
            ->first();

        $income = (float) ($totals->total_income ?? 0);
        $expense = (float) ($totals->total_expense ?? 0);

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => round($income - $expense, 2),
            'total_movements' => (int) ($totals->total_movements ?? 0),
        ];
    }

    /**
     * Get totals grouped by payment method.
     */
    public function getIncomeByMethod(int $gymId, Carbon $from, Carbon $to): Collection
    {
        return CashMovement::query()
            ->where('gym_id', $gymId)
            ->whereBetween('occurred_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('method')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) as balance")
            ->selectRaw('COUNT(*) as movements_count')
            ->groupBy('method')
            ->orderBy('method')
            ->get();
    }

    /**
     * Get attendance summary and daily attendance counts.
     *
     * @return array<string, int|Collection>
     */
    public function getAttendanceSummary(int $gymId, Carbon $from, Carbon $to): array
    {
        $byDay = Attendance::query()
            ->where('gym_id', $gymId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date')
            ->selectRaw('COUNT(*) as attendances_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_attendances' => (int) $byDay->sum('attendances_count'),
            'by_day' => $byDay,
        ];
    }

    /**
     * Get membership status summary for today.
     *
     * @return array<string, int>
     */
    public function getMembershipStatusSummary(int $gymId): array
    {
        $today = Carbon::today()->toDateString();

        $activeClientIds = Membership::query()
            ->where('gym_id', $gymId)
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', $today)
            ->distinct()
            ->pluck('client_id');

        $expiredClientIds = Membership::query()
            ->where('gym_id', $gymId)
            ->where(function ($query) use ($today): void {
                $query
                    ->where('status', 'expired')
                    ->orWhereDate('ends_at', '<', $today);
            })
            ->whereNotIn('client_id', $activeClientIds)
            ->distinct()
            ->pluck('client_id');

        return [
            'active' => $activeClientIds->count(),
            'expired' => $expiredClientIds->count(),
            'total_clients' => Client::query()->where('gym_id', $gymId)->count(),
        ];
    }

    /**
     * Get client lists for memberships report (active/expired).
     *
     * @return array<string, Collection>
     */
    public function getMembershipClientLists(int $gymId): array
    {
        $today = Carbon::today()->toDateString();

        $clients = Client::query()
            ->where('gym_id', $gymId)
            ->with([
                'memberships' => fn ($query) => $query
                    ->where('gym_id', $gymId)
                    ->orderByDesc('ends_at')
                    ->orderByDesc('id'),
            ])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $active = collect();
        $expired = collect();

        foreach ($clients as $client) {
            $latestMembership = $client->memberships->first();
            if (! $latestMembership) {
                continue;
            }

            $endsAt = $latestMembership->ends_at?->toDateString();
            $isActive = $latestMembership->status === 'active'
                && $endsAt !== null
                && $endsAt >= $today;

            $row = (object) [
                'client_id' => $client->id,
                'full_name' => $client->full_name,
                'document_number' => $client->document_number,
                'status' => $latestMembership->status,
                'starts_at' => $latestMembership->starts_at?->toDateString(),
                'ends_at' => $latestMembership->ends_at?->toDateString(),
                'plan_id' => $latestMembership->plan_id,
            ];

            if ($isActive) {
                $active->push($row);
            } else {
                $expired->push($row);
            }
        }

        return [
            'active_clients' => $active->values(),
            'expired_clients' => $expired->values(),
        ];
    }
}
