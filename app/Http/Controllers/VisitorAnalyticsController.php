<?php

namespace App\Http\Controllers;

use App\Models\VisitorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorAnalyticsController extends Controller
{
    /**
     * Record a new visit (called on page load).
     */
    public function store(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id') ?? session()->getId();
        $source = $request->input('source', 'frontend');
        $path = $request->input('path');
        if (is_string($path)) {
            $path = substr($path, 0, 500);
        } else {
            $path = null;
        }

        $log = VisitorLog::create([
            'session_id' => $sessionId,
            'visited_at' => now(),
            'source' => in_array($source, ['frontend', 'admin', 'portal']) ? $source : 'frontend',
            'path' => $path,
        ]);

        return response()->json(['visit_id' => $log->id]);
    }

    /**
     * Update time spent for a visit (called on page unload / visibility hidden).
     */
    public function updateTime(Request $request, int $id): JsonResponse
    {
        $seconds = (int) $request->input('seconds', 0);
        if ($seconds <= 0 || $seconds > 86400) { // max 24h
            return response()->json(['ok' => true]);
        }

        VisitorLog::where('id', $id)->update(['time_spent_seconds' => $seconds]);

        return response()->json(['ok' => true]);
    }
}
