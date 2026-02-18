<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Http\Requests\ReceptionCheckInRequest;
use App\Services\AttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReceptionCheckInController extends Controller
{
    /**
     * Show reception mode page.
     */
    public function index(Request $request): View
    {
        $gymId = (int) ($request->user()?->gym_id ?? 0);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $recentAttendances = Attendance::query()
            ->where('gym_id', $gymId)
            ->with(['client:id,first_name,last_name,photo_path', 'credential:id,type'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(10)
            ->get();

        return view('reception.index', [
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Unified endpoint for RFID/QR/document check-in.
     */
    public function store(
        ReceptionCheckInRequest $request,
        AttendanceService $attendanceService
    ): JsonResponse {
        $userId = (int) $request->user()->id;
        $gymId = $request->user()?->gym_id;

        if (! $gymId) {
            $payload = [
                'ok' => false,
                'message' => 'El usuario autenticado no tiene gym_id asignado.',
                'method' => null,
                'client' => null,
            ];

            Log::warning('reception.check_in.failed', [
                'gym_id' => null,
                'user_id' => $userId,
                'method' => null,
                'reason' => 'missing_gym_id',
            ]);

            return response()->json($payload, 422);
        }

        try {
            $result = $attendanceService->checkInByValue(
                (int) $gymId,
                $userId,
                $request->validated('value')
            );
        } catch (Throwable $exception) {
            report($exception);

            $payload = [
                'ok' => false,
                'message' => 'No se pudo registrar el ingreso por un error interno.',
                'method' => null,
                'client' => null,
            ];

            Log::warning('reception.check_in.failed', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => null,
                'reason' => 'internal_exception',
            ]);

            return response()->json($payload, 500);
        }

        if ($result['ok']) {
            Log::info('reception.check_in.success', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
            ]);
        } else {
            Log::warning('reception.check_in.failed', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
                'reason' => $result['message'],
            ]);
        }

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
