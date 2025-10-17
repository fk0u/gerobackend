<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\SubscriptionPlan;
use App\Http\Resources\SubscriptionPlanResource;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = SubscriptionPlan::query();
        
        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        // Default to only active plans for public API
        if (!$request->filled('is_active')) {
            $query->where('is_active', true);
        }

        // Order by price since sort_order column doesn't exist in database
        $plans = $query->orderBy('price')->get();

        return $this->successResponse(
            SubscriptionPlanResource::collection($plans),
            'Subscription plans retrieved successfully'
        );
    }

    public function show(int $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        return $this->successResponse(
            new SubscriptionPlanResource($plan),
            'Subscription plan retrieved successfully'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_schedules_per_month' => 'nullable|integer|min:0',
            'max_pickup_locations' => 'nullable|integer|min:0',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $plan = SubscriptionPlan::create($data);

        return $this->successResponse(
            new SubscriptionPlanResource($plan),
            'Subscription plan created successfully',
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:subscription_plans,slug,' . $id,
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'billing_cycle' => 'sometimes|in:monthly,yearly',
            'features' => 'sometimes|nullable|array',
            'max_schedules_per_month' => 'sometimes|nullable|integer|min:0',
            'max_pickup_locations' => 'sometimes|nullable|integer|min:0',
            'priority_support' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|nullable|integer',
        ]);

        $plan->update($data);

        return $this->successResponse(
            new SubscriptionPlanResource($plan),
            'Subscription plan updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        // Check if plan has active subscriptions
        if ($plan->activeSubscriptions()->exists()) {
            return $this->errorResponse(
                'Cannot delete subscription plan with active subscriptions',
                422
            );
        }

        $plan->delete();

        return $this->successResponse(
            null,
            'Subscription plan deleted successfully'
        );
    }
}