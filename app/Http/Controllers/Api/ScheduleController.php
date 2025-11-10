<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection as SupportCollection;

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
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Allow privileged roles to attach a customer; otherwise default to the requester
        $data['user_id'] = $data['user_id'] ?? $request->user()->id;
        $data['status'] = 'pending';
        $data['frequency'] = $data['frequency'] ?? 'once';

        // Legacy columns still require values in schema, so mirror modern fields to avoid 500s
        $data['title'] = $data['service_type'] ?? 'Pickup Service';
        $data['description'] = $data['notes'] ?? $data['pickup_address'];
        $data['latitude'] = $data['pickup_latitude'];
        $data['longitude'] = $data['pickup_longitude'];

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
            'frequency' => 'once',
            'title' => $data['jenis_layanan'],
            'description' => $data['catatan'] ?? $data['alamat'],
            'latitude' => $data['koordinat']['lat'],
            'longitude' => $data['koordinat']['lng'],
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
        $schedule = Schedule::with('additionalWastes')->findOrFail($id);

        $data = $request->validate([
            'service_type' => 'sometimes|string|max:100',
            'pickup_address' => 'sometimes|string|max:500',
            'pickup_latitude' => 'sometimes|numeric|between:-90,90',
            'pickup_longitude' => 'sometimes|numeric|between:-180,180',
            'scheduled_at' => 'sometimes|date|after_or_equal:now',
            'estimated_duration' => 'sometimes|nullable|integer|min:15|max:480',
            'notes' => 'sometimes|nullable|string|max:1000',
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
            'mitra_id' => 'sometimes|nullable|exists:users,id',
            'payment_method' => ['sometimes', 'nullable', Rule::in(['cash', 'transfer', 'wallet'])],
            'price' => 'sometimes|nullable|numeric|min:0',
            'frequency' => ['sometimes', 'nullable', Rule::in(['once', 'daily', 'weekly', 'biweekly', 'monthly'])],
            'waste_type' => 'sometimes|nullable|string|max:50',
            'estimated_weight' => 'sometimes|nullable|numeric|min:0',
            'contact_name' => 'sometimes|nullable|string|max:255',
            'contact_phone' => 'sometimes|nullable|string|max:20',
            'is_paid' => 'sometimes|boolean',
            'amount' => 'sometimes|nullable|numeric|min:0',
            'additional_wastes' => 'sometimes|array',
            'additional_wastes.*.id' => 'sometimes|integer|exists:additional_wastes,id',
            'additional_wastes.*.waste_type' => 'nullable|string|max:50',
            'additional_wastes.*.estimated_weight' => 'nullable|numeric|min:0',
            'additional_wastes.*.notes' => 'nullable|string|max:500',
            'additional_wastes.*._delete' => 'sometimes|boolean',
        ]);

        $additionalWastes = collect($data['additional_wastes'] ?? []);
        unset($data['additional_wastes']);

        $payload = $this->mirrorLegacyFields(
            $this->normalizePayload($data)
        );

        DB::transaction(function () use ($schedule, $payload, $additionalWastes) {
            $schedule->fill($payload);
            $schedule->save();

            if ($additionalWastes->isNotEmpty()) {
                $this->syncAdditionalWastes($schedule, $additionalWastes);
            }
        });

        $schedule->refresh()->load(['user', 'mitra', 'additionalWastes']);

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
        
        $notes = $this->appendAuditNote($schedule->notes, 'Completion', $data['completion_notes'] ?? 'Completed');

        $payload = $this->mirrorLegacyFields([
            'status' => 'completed',
            'notes' => $notes,
            'completion_notes' => $data['completion_notes'] ?? null,
            'actual_duration' => $data['actual_duration'] ?? null,
            'completed_at' => now(),
        ]);

        $schedule->update($payload);
        
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
        
        $notes = $this->appendAuditNote($schedule->notes, 'Cancelled', $data['cancellation_reason']);

        $payload = $this->mirrorLegacyFields([
            'status' => 'cancelled',
            'notes' => $notes,
            'cancelled_at' => now(),
            'rejection_reason' => $data['cancellation_reason'],
        ]);

        $schedule->update($payload);
        
        $schedule->load(['user', 'mitra']);
        
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Schedule cancelled successfully'
        );
    }

    private function normalizePayload(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                $data[$key] = $trimmed === '' ? null : $trimmed;
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->normalizePayload($value);
            }
        }

        return $data;
    }

    private function mirrorLegacyFields(array $data): array
    {
        if (array_key_exists('service_type', $data)) {
            $data['title'] = $data['service_type'];
        }

        if (array_key_exists('notes', $data)) {
            $data['description'] = $data['notes'];
        }

        if (array_key_exists('pickup_latitude', $data)) {
            $data['latitude'] = $data['pickup_latitude'];
        }

        if (array_key_exists('pickup_longitude', $data)) {
            $data['longitude'] = $data['pickup_longitude'];
        }

        return $data;
    }

    private function appendAuditNote(?string $existingNotes, string $label, string $message): string
    {
        $trimmedExisting = trim((string) $existingNotes);
        $entry = sprintf('%s: %s', $label, $message);

        return $trimmedExisting === ''
            ? $entry
            : $trimmedExisting . PHP_EOL . PHP_EOL . $entry;
    }

    private function syncAdditionalWastes(Schedule $schedule, SupportCollection $items): void
    {
        $allowed = ['waste_type', 'estimated_weight', 'notes'];

        $items->each(function (array $payload) use ($schedule, $allowed) {
            $payload = $this->normalizePayload($payload);
            $delete = (bool) ($payload['_delete'] ?? false);
            $id = $payload['id'] ?? null;

            unset($payload['_delete'], $payload['id']);

            $filtered = array_intersect_key($payload, array_flip($allowed));

            if ($id !== null) {
                $waste = $schedule->additionalWastes()->find($id);

                if (!$waste) {
                    return;
                }

                if ($delete) {
                    $waste->delete();
                    return;
                }

                $waste->update($filtered);
                return;
            }

            if ($delete || empty($filtered['waste_type'])) {
                return;
            }

            $schedule->additionalWastes()->create($filtered);
        });
    }

    public function destroy(Request $request, int $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Authorization: Only mitra who owns it or admin can delete
        $user = $request->user();
        if ($user->role !== 'admin' && $schedule->mitra_id !== $user->id) {
            return $this->errorResponse('Forbidden: You can only delete your own schedules', 403);
        }
        
        // Check if schedule can be deleted (business logic)
        if (in_array($schedule->status, ['in_progress', 'completed'])) {
            return $this->errorResponse('Cannot delete schedule in current status. Only pending or cancelled schedules can be deleted.', 422);
        }
        
        $schedule->delete();
        
        return $this->successResponse(null, 'Schedule deleted successfully', 200);
    }
}