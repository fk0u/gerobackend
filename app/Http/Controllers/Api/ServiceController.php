<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request) {
        $query = Service::query();
        if (! $request->boolean('all')) {
            $query->where('is_active', true);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }
        $services = $query->orderBy('name')->get();
        return ServiceResource::collection($services);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_points' => 'nullable|integer',
            'base_price' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $service = Service::create($data);
        return response()->json(new ServiceResource($service), 201);
    }

    public function update(Request $request, int $id) {
        $service = Service::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'base_points' => 'sometimes|integer',
            'base_price' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);
        $service->update($data);
        return response()->json(new ServiceResource($service));
    }
}
