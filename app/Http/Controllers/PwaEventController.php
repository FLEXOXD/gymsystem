<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePwaEventRequest;
use App\Models\PwaEvent;
use App\Support\ActiveGymContext;
use Illuminate\Http\JsonResponse;

class PwaEventController extends Controller
{
    public function store(StorePwaEventRequest $request): JsonResponse
    {
        $user = $request->user();
        $gymIdFromContext = ActiveGymContext::id($request);
        $gymId = $gymIdFromContext ?: (int) ($user?->gym_id ?? 0);
        $validated = $request->validated();

        $payload = $validated['payload'] ?? null;
        if (is_array($payload)) {
            // Cap metadata size to avoid bloating telemetry table.
            $encodedPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (is_string($encodedPayload) && strlen($encodedPayload) > 4000) {
                $payload = ['truncated' => true];
            }
        }

        PwaEvent::query()->create([
            'gym_id' => $gymId > 0 ? $gymId : null,
            'user_id' => $user?->id,
            'context_gym_slug' => $validated['context_gym_slug'] ?? null,
            'event_name' => (string) $validated['event_name'],
            'event_source' => (string) ($validated['event_source'] ?? 'web'),
            'mode' => $validated['mode'] ?? 'unknown',
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'payload' => $payload,
            'occurred_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }
}

