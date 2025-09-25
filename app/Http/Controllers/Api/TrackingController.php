<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tracking;
use App\Http\Resources\TrackingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
    $query = Tracking::query()->with('schedule.assignedUser');

        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', (int) $request->input('schedule_id'));
        }

        if ($request->filled('since')) {
            $since = Carbon::parse($request->input('since'));
            $query->where('recorded_at', '>=', $since);
        }

        if ($request->filled('until')) {
            $until = Carbon::parse($request->input('until'));
            $query->where('recorded_at', '<=', $until);
        }

        // pagination parameters
        $limit = min((int) $request->input('limit', 200), 1000);
        $query->orderByDesc('recorded_at')->orderByDesc('id');

        $items = $query->limit($limit)->get();
        return TrackingResource::collection($items);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'recorded_at' => 'nullable|date',
        ]);

        if (empty($data['recorded_at'])) {
            $data['recorded_at'] = now();
        }

        $tracking = Tracking::create($data);
        return response()->json(new TrackingResource($tracking->load('schedule.assignedUser')), 201);
    }

    public function historyBySchedule(int $scheduleId)
    {
        $history = Tracking::with('schedule.assignedUser')
            ->where('schedule_id', $scheduleId)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return TrackingResource::collection($history);
    }
}
