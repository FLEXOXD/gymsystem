<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDailySummary;
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
            ->forGym($gymId)
            ->betweenOccurredAt($from, $to)
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
            ->forGym($gymId)
            ->betweenOccurredAt($from, $to)
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
        $rawByDay = Attendance::query()
            ->forGym($gymId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date')
            ->selectRaw('COUNT(*) as attendances_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $archivedByDay = AttendanceDailySummary::query()
            ->forGym($gymId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->select(['date', 'attendances_count'])
            ->orderBy('date')
            ->get();

        $countsByDate = [];

        foreach ($archivedByDay as $row) {
            $dateKey = $row->date?->toDateString() ?? (string) $row->date;
            $countsByDate[$dateKey] = (int) ($countsByDate[$dateKey] ?? 0) + (int) $row->attendances_count;
        }

        foreach ($rawByDay as $row) {
            $dateKey = $row->date instanceof Carbon ? $row->date->toDateString() : (string) $row->date;
            $countsByDate[$dateKey] = (int) ($countsByDate[$dateKey] ?? 0) + (int) $row->attendances_count;
        }

        ksort($countsByDate);

        $byDay = collect($countsByDate)->map(function (int $count, string $date) {
            return (object) [
                'date' => $date,
                'attendances_count' => $count,
            ];
        })->values();

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
            ->forGym($gymId)
            ->status('active')
            ->whereDate('ends_at', '>=', $today)
            ->distinct()
            ->pluck('client_id');

        $expiredClientIds = Membership::query()
            ->forGym($gymId)
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
            'total_clients' => Client::query()->forGym($gymId)->count(),
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
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'first_name', 'last_name', 'document_number'])
            ->with([
                'memberships' => fn ($query) => $query
                    ->forGym($gymId)
                    ->select(['id', 'gym_id', 'client_id', 'plan_id', 'starts_at', 'ends_at', 'status'])
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
