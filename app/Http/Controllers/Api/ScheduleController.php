<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $query = Schedule::query()
            ->with(['user', 'mitra', 'trackings', 'additionalWastes'])
            ->withCount('trackings');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        // Filter by mitra (for mitra role)
        if ($request->filled('mitra_id')) {
            $query->where('mitra_id', $request->integer('mitra_id'));
        }

        // Filter by user (for end_user role)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date('date_to'));
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->string('service_type'));
        }

        $perPage = $request->integer('per_page', 20);
        $schedules = $query->latest('scheduled_at')->paginate($perPage);
        
        $schedules->getCollection()->transform(fn($schedule) => new ScheduleResource($schedule));
        
        return $this->paginatedResponse($schedules, 'Schedules retrieved successfully');
    }

    public function show(int $id)
    {
        $schedule = Schedule::with([
            'user',
            'mitra',
            'additionalWastes',
            'trackings' => fn ($q) => $q->orderByDesc('recorded_at')->limit(200),
        ])->findOrFail($id);
        
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule retrieved successfully'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type' => 'required|string|max:100',
            'pickup_address' => 'required|string|max:500',
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'scheduled_at' => 'required|date|after:now',
            'estimated_duration' => 'nullable|integer|min:15|max:480',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => ['nullable', Rule::in(['cash', 'transfer', 'wallet'])],
            'price' => 'nullable|numeric|min:0',
            'frequency' => ['nullable', Rule::in(['once', 'daily', 'weekly', 'biweekly', 'monthly'])],
            'waste_type' => 'nullable|string|max:50',
            'estimated_weight' => 'nullable|numeric|min:0',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_paid' => 'nullable|boolean',
            'amount' => 'nullable|numeric|min:0',
            'additional_wastes' => 'nullable|array',
            'additional_wastes.*.waste_type' => 'required|string|max:50',
            'additional_wastes.*.estimated_weight' => 'nullable|numeric|min:0',
            'additional_wastes.*.notes' => 'nullable|string|max:500',
        ]);

        // Auto-assign user if authenticated
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending';
        $data['frequency'] = $data['frequency'] ?? 'once';

        // Extract additional wastes
        $additionalWastes = $data['additional_wastes'] ?? [];
        unset($data['additional_wastes']);

        // Create schedule
        $schedule = Schedule::create($data);

        // Create additional wastes
        if (!empty($additionalWastes)) {
            foreach ($additionalWastes as $waste) {
                $schedule->additionalWastes()->create($waste);
            }
        }

        $schedule->load(['user', 'mitra', 'additionalWastes']);

        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule created successfully',
            201
        );
    }

    public function storeMobileFormat(Request $request)
    {
        // Accept mobile app format and convert to backend format
        $data = $request->validate([
            'alamat' => 'required|string|max:500',
            'tanggal' => 'required|date|after:now',
            'waktu' => 'required|string', // e.g., "08:00"
            'catatan' => 'nullable|string|max:1000',
            'koordinat.lat' => 'required|numeric|between:-90,90',
            'koordinat.lng' => 'required|numeric|between:-180,180',
            'jenis_layanan' => 'required|string|max:100',
            'metode_pembayaran' => ['nullable', Rule::in(['cash', 'transfer', 'wallet'])],
        ]);

        // Convert mobile format to backend format
        $scheduledAt = $data['tanggal'] . ' ' . $data['waktu'];
        
        $backendData = [
            'service_type' => $data['jenis_layanan'],
            'pickup_address' => $data['alamat'],
            'pickup_latitude' => $data['koordinat']['lat'],
            'pickup_longitude' => $data['koordinat']['lng'],
            'scheduled_at' => $scheduledAt,
            'notes' => $data['catatan'] ?? null,
            'payment_method' => $data['metode_pembayaran'] ?? 'cash',
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ];

        $schedule = Schedule::create($backendData);
        $schedule->load(['user', 'mitra']);

        return $this->successResponse(
            new ScheduleResource($schedule),
            'Jadwal berhasil dibuat',
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);

        $data = $request->validate([
            'service_type' => 'sometimes|string|max:100',
            'pickup_address' => 'sometimes|string|max:500',
            'pickup_latitude' => 'sometimes|numeric|between:-90,90',
            'pickup_longitude' => 'sometimes|numeric|between:-180,180',
            'scheduled_at' => 'sometimes|date|after:now',
            'estimated_duration' => 'sometimes|nullable|integer|min:15|max:480',
            'notes' => 'sometimes|nullable|string|max:1000',
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
            'mitra_id' => 'sometimes|nullable|exists:users,id',
            'payment_method' => ['sometimes', 'nullable', Rule::in(['cash', 'transfer', 'wallet'])],
            'price' => 'sometimes|nullable|numeric|min:0',
        ]);

        $schedule->update($data);
        $schedule->load(['user', 'mitra']);
        
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule updated successfully'
        );
    }

    public function complete(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Check if schedule can be completed
        if (!in_array($schedule->status, ['confirmed', 'in_progress'])) {
            return $this->errorResponse('Cannot complete schedule in current status', 422);
        }
        
        // Validate completion data
        $data = $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
            'actual_duration' => 'nullable|integer|min:1',
        ]);
        
        $schedule->update([
            'status' => 'completed',
            'notes' => ($schedule->notes ?? '') . '\n\nCompletion: ' . ($data['completion_notes'] ?? 'Completed'),
            'completed_at' => now(),
        ]);
        
        $schedule->load(['user', 'mitra']);
        
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule completed successfully'
        );
    }

    public function cancel(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Check if schedule can be cancelled
        if (in_array($schedule->status, ['completed', 'cancelled'])) {
            return $this->errorResponse('Cannot cancel schedule in current status', 422);
        }
        
        // Validate cancellation data
        $data = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);
        
        $schedule->update([
            'status' => 'cancelled',
            'notes' => ($schedule->notes ?? '') . '\n\nCancelled: ' . $data['cancellation_reason'],
            'cancelled_at' => now(),
        ]);
        
        $schedule->load(['user', 'mitra']);
        
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule cancelled successfully'
        );
    }
}