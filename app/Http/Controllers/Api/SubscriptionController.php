<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionPlanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $subscriptions = Subscription::with(['subscriptionPlan'])
            ->where('user_id', ' =>', $user->id, 'and')
            ->latest()
            ->paginate(20);

        return $this->successResponse([
            'subscriptions' => SubscriptionResource::collection($subscriptions->items()),
            'pagination' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
            ]
        ]);
    }

    public function show(int $id)
    {
        $user = Auth::user();
        
        $subscription = Subscription::with(['subscriptionPlan'])
            ->where('user_id', ' =>', $user->id, 'and')
            ->findOrFail($id);

        return $this->successResponse(
            new SubscriptionResource($subscription)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|string|in:bank_transfer,e_wallet,credit_card,cash',
            'auto_renew' => 'boolean',
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($data['subscription_plan_id']);

        // Check if user already has active subscription
        $activeSubscription = Subscription::where('user_id', ' =>', $user->id, 'and')
            ->where('status', ' =>', 'active', 'and')
            ->where('ends_at', '>', now(), 'and')
            ->first();

        if ($activeSubscription) {
            return $this->errorResponse('User already has an active subscription', 422);
        }

        try {
            DB::beginTransaction();

            // Calculate subscription period
            $startsAt = now();
            $endsAt = match($plan->billing_cycle) {
                'monthly' => $startsAt->copy()->addMonth(),
                'yearly' => $startsAt->copy()->addYear(),
                default => $startsAt->copy()->addMonth(),
            };

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'pending', // Will be activated after payment
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'auto_renew' => $data['auto_renew'] ?? false,
                'payment_method' => $data['payment_method'],
                'amount' => $plan->price,
                'metadata' => [
                    'plan_name' => $plan->name,
                    'billing_cycle' => $plan->billing_cycle,
                    'created_via' => 'api',
                ],
            ]);

            DB::commit();

            return $this->successResponse(
                new SubscriptionResource($subscription->load('subscriptionPlan')),
                'Subscription created successfully',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create subscription: ' . $e->getMessage(), 500);
        }
    }

    public function activate(Request $request, int $id)
    {
        $user = Auth::user();
        
        $subscription = Subscription::where('user_id', ' =>', $user->id, 'and')
            ->findOrFail($id);

        if ($subscription->status !== 'pending') {
            return $this->errorResponse('Subscription cannot be activated', 422);
        }

        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
        ]);

        return $this->successResponse(
            new SubscriptionResource($subscription->load('subscriptionPlan')),
            'Subscription activated successfully'
        );
    }

    public function cancel(Request $request, int $id)
    {
        $user = Auth::user();
        
        $subscription = Subscription::where('user_id', ' =>', $user->id, 'and')
            ->findOrFail($id);

        if (!in_array($subscription->status, ['active', 'pending'])) {
            return $this->errorResponse('Subscription cannot be cancelled', 422);
        }

        $data = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false,
            'metadata' => array_merge($subscription->metadata ?? [], [
                'cancelled_at' => now(),
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
            ]),
        ]);

        return $this->successResponse(
            new SubscriptionResource($subscription->load('subscriptionPlan')),
            'Subscription cancelled successfully'
        );
    }

    public function getCurrentSubscription()
    {
        $user = Auth::user();
        
        $subscription = Subscription::with(['subscriptionPlan'])
            ->where('user_id', ' =>', $user->id, 'and')
            ->where('status', ' =>', 'active', 'and')
            ->where('ends_at', '>', now(), 'and')
            ->first();

        if (!$subscription) {
            return $this->successResponse(null, 'No active subscription found');
        }

        return $this->successResponse(
            new SubscriptionResource($subscription)
        );
    }
}