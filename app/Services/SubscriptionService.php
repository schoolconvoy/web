<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Create a new subscription for a tenant.
     *
     * @param Tenant $tenant
     * @param Plan $plan
     * @param string $paymentMethod
     * @param string $interval
     * @return Subscription
     */
    public function createSubscription(Tenant $tenant, Plan $plan, string $paymentMethod = 'stripe', string $interval = 'monthly'): Subscription
    {
        // Check if tenant already has an active subscription
        $activeSubscription = $tenant->subscriptions()
            ->whereNull('ends_at')
            ->first();

        if ($activeSubscription) {
            // End the current subscription
            $this->cancelSubscription($activeSubscription);
        }

        // Create a new subscription
        $subscription = new Subscription();
        $subscription->tenant_id = $tenant->id;
        $subscription->plan_id = $plan->id;
        $subscription->name = $plan->name;

        // Set trial period if applicable
        if ($plan->trial_days > 0) {
            $subscription->trial_ends_at = Carbon::now()->addDays($plan->trial_days);
        }

        // Handle different payment methods
        if ($paymentMethod === 'stripe') {
            $planId = $interval === 'yearly' ? $plan->stripe_yearly_plan_id : $plan->stripe_monthly_plan_id;
            $subscription->stripe_price = $planId;
            $subscription->stripe_status = 'active';
            // In a real implementation, you would integrate with Stripe API here
        } elseif ($paymentMethod === 'paystack') {
            $planId = $interval === 'yearly' ? $plan->paystack_yearly_plan_id : $plan->paystack_monthly_plan_id;
            $subscription->paystack_plan = $planId;
            $subscription->paystack_status = 'active';
            // In a real implementation, you would integrate with Paystack API here
        }

        $subscription->save();

        // Update tenant's plan
        $tenant->plan_id = $plan->id;
        $tenant->save();

        return $subscription;
    }

    /**
     * Cancel a subscription.
     *
     * @param Subscription $subscription
     * @return bool
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            // In a real implementation, you would cancel the subscription with the payment provider
            if ($subscription->stripe_id) {
                // Cancel with Stripe
            } elseif ($subscription->paystack_id) {
                // Cancel with Paystack
            }

            // Mark subscription as ended
            $subscription->ends_at = Carbon::now();
            $subscription->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upgrade or downgrade a subscription.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @param string $interval
     * @return Subscription
     */
    public function changePlan(Tenant $tenant, Plan $newPlan, string $interval = 'monthly'): Subscription
    {
        // Get current subscription
        $currentSubscription = $tenant->subscriptions()
            ->whereNull('ends_at')
            ->first();

        if (!$currentSubscription) {
            // If no active subscription, create a new one
            return $this->createSubscription($tenant, $newPlan, 'stripe', $interval);
        }

        // Cancel current subscription
        $this->cancelSubscription($currentSubscription);

        // Create new subscription with new plan
        return $this->createSubscription($tenant, $newPlan, 'stripe', $interval);
    }

    /**
     * Check if a tenant has reached their plan limits.
     *
     * @param Tenant $tenant
     * @param string $resourceType
     * @param int $count
     * @return bool
     */
    public function hasReachedLimit(Tenant $tenant, string $resourceType, int $count = 1): bool
    {
        if (!$tenant->plan) {
            return true; // No plan means no access
        }

        switch ($resourceType) {
            case 'schools':
                $currentCount = $tenant->schools()->count();
                return ($currentCount + $count) > $tenant->plan->max_schools;

            case 'students':
                $currentCount = $tenant->users()->role('Student')->count();
                return ($currentCount + $count) > $tenant->plan->max_students;

            case 'teachers':
                $currentCount = $tenant->users()->role('Teacher')->count();
                return ($currentCount + $count) > $tenant->plan->max_teachers;

            case 'parents':
                $currentCount = $tenant->users()->role('Parent')->count();
                return ($currentCount + $count) > $tenant->plan->max_parents;

            default:
                return false;
        }
    }
}
