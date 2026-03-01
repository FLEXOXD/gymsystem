<?php

namespace App\Jobs;

use App\Models\Gym;
use App\Models\PushCampaign;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Throwable;

class SendPushCampaignJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        private readonly int $campaignId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(PushNotificationService $pushNotificationService): void
    {
        $campaign = PushCampaign::query()->find($this->campaignId);
        if (! $campaign) {
            return;
        }

        $campaign->forceFill([
            'status' => 'sending',
            'last_error' => null,
        ])->save();

        try {
            $gymIds = $this->resolveTargetGymIds($campaign);
            $roles = $this->resolveTargetRoles((string) $campaign->audience);

            $payload = [
                'title' => (string) $campaign->title,
                'body' => (string) $campaign->body,
                'url' => trim((string) ($campaign->url ?? '')) !== '' ? (string) $campaign->url : '/app',
                'tag' => trim((string) ($campaign->tag ?? '')) !== ''
                    ? (string) $campaign->tag
                    : 'push-campaign-'.(int) $campaign->id,
                'data' => [
                    'kind' => 'push_campaign',
                    'campaign_id' => (int) $campaign->id,
                    'detail_text' => trim((string) ($campaign->detail_text ?? '')),
                ],
                'renotify' => true,
            ];

            $sent = 0;
            $failed = 0;
            $skipped = 0;

            foreach ($gymIds as $gymId) {
                $result = $pushNotificationService->sendToGymUsers((int) $gymId, $payload, $roles);
                $sent += (int) ($result['sent'] ?? 0);
                $failed += (int) ($result['failed'] ?? 0);
                $skipped += (int) ($result['skipped'] ?? 0);
            }

            $totalTargets = $sent + $failed + $skipped;
            $status = match (true) {
                $totalTargets <= 0 => 'skipped',
                $sent > 0 && $failed <= 0 => 'sent',
                $sent > 0 && $failed > 0 => 'partial',
                $failed > 0 => 'failed',
                default => 'skipped',
            };

            $campaign->forceFill([
                'status' => $status,
                'total_targets' => $totalTargets,
                'sent_count' => $sent,
                'failed_count' => $failed,
                'skipped_count' => $skipped,
                'sent_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $campaign->forceFill([
                'status' => 'failed',
                'last_error' => mb_substr($exception->getMessage(), 0, 2000),
                'sent_at' => now(),
            ])->save();
        }
    }

    /**
     * @return Collection<int, int>
     */
    private function resolveTargetGymIds(PushCampaign $campaign): Collection
    {
        if ((int) ($campaign->gym_id ?? 0) > 0) {
            return collect([(int) $campaign->gym_id]);
        }

        return Gym::query()
            ->withoutDemoSessions()
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values();
    }

    /**
     * @return list<string>
     */
    private function resolveTargetRoles(string $audience): array
    {
        return match (strtolower(trim($audience))) {
            'staff' => ['owner', 'cashier'],
            'all_users' => [],
            default => ['owner'],
        };
    }
}
