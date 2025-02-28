<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of available plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::active()->get();
        $tenant = $this->getCurrentTenant();
        $currentSubscription = $tenant ? $tenant->subscriptions()->whereNull('ends_at')->first() : null;

        return view('subscriptions.index', compact('plans', 'tenant', 'currentSubscription'));
    }

    /**
     * Show the form for subscribing to a plan.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function showSubscriptionForm(Plan $plan)
    {
        $tenant = $this->getCurrentTenant();

        return view('subscriptions.subscribe', compact('plan', 'tenant'));
    }

    /**
     * Process a subscription to a plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paystack',
            'interval' => 'required|in:monthly,yearly',
        ]);

        $tenant = $this->getCurrentTenant();

        if (!$tenant) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'You need to create a tenant first.');
        }

        try {
            $subscription = $this->subscriptionService->createSubscription(
                $tenant,
                $plan,
                $request->payment_method,
                $request->interval
            );

            return redirect()->route('subscriptions.show', $subscription->id)
                ->with('success', 'Successfully subscribed to ' . $plan->name . ' plan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process subscription: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        $tenant = $this->getCurrentTenant();

        if ($subscription->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function cancel(Subscription $subscription)
    {
        $tenant = $this->getCurrentTenant();

        if ($subscription->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->subscriptionService->cancelSubscription($subscription);

        if ($result) {
            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', 'Failed to cancel subscription.');
    }

    /**
     * Show the form for changing to a different plan.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function showChangePlanForm(Plan $plan)
    {
        $tenant = $this->getCurrentTenant();
        $currentSubscription = $tenant->subscriptions()->whereNull('ends_at')->first();

        if (!$currentSubscription) {
            return redirect()->route('subscriptions.show-subscription-form', $plan->id);
        }

        return view('subscriptions.change-plan', compact('plan', 'tenant', 'currentSubscription'));
    }

    /**
     * Change to a different plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function changePlan(Request $request, Plan $plan)
    {
        $request->validate([
            'interval' => 'required|in:monthly,yearly',
        ]);

        $tenant = $this->getCurrentTenant();

        try {
            $subscription = $this->subscriptionService->changePlan(
                $tenant,
                $plan,
                $request->interval
            );

            return redirect()->route('subscriptions.show', $subscription->id)
                ->with('success', 'Successfully changed to ' . $plan->name . ' plan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to change plan: ' . $e->getMessage());
        }
    }

    /**
     * Get the current tenant for the authenticated user.
     *
     * @return \App\Models\Tenant|null
     */
    protected function getCurrentTenant()
    {
        if (SpatieTenant::checkCurrent()) {
            return Tenant::find(SpatieTenant::current()->id);
        }

        // For super admins or when not in a tenant context
        $user = Auth::user();
        if ($user && $user->hasRole('super-admin')) {
            // Super admin might be managing subscriptions for a specific tenant
            // You could add logic here to determine which tenant they're managing
            return null;
        }

        return $user ? $user->tenant : null;
    }
}
