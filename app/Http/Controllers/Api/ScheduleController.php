<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::query()->with(['assignedUser'])->withCount('trackings');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }

        $perPage = $request->integer('per_page', 20);
        $page = $query->latest('scheduled_at')->paginate($perPage);
        $page->getCollection()->transform(fn($s) => new ScheduleResource($s));
        return response()->json($page);
    }

    public function show(int $id)
    {
        $schedule = Schedule::with([
            'assignedUser',
            'trackings' => fn ($q) => $q->orderByDesc('recorded_at')->limit(200),
        ])->findOrFail($id);
        return response()->json(new ScheduleResource($schedule));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'nullable|string',
            'assigned_to' => 'nullable|integer',
            'scheduled_at' => 'nullable|date',
        ]);

        $schedule = Schedule::create($data);
        return response()->json(
            new ScheduleResource($schedule->fresh(['assignedUser'])->loadCount('trackings')),
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'status' => 'sometimes|string',
            'assigned_to' => 'sometimes|nullable|integer',
            'scheduled_at' => 'sometimes|nullable|date',
        ]);

        $schedule->update($data);
        return response()->json(new ScheduleResource($schedule->fresh(['assignedUser'])->loadCount('trackings')));
    }
}