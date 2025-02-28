<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SubscriptionStatus extends Component
{
    public $tenant;
    public $showDetails;
    public $remainingDays;
    public $usageStats;

    public function __construct(bool $showDetails = true)
    {
        $this->showDetails = $showDetails;
        $this->tenant = Tenant::find(Auth::user()->school_id);
        $this->remainingDays = $this->calculateRemainingDays();
        $this->usageStats = $this->getUsageStats();
    }

    protected function calculateRemainingDays()
    {
        if (!$this->tenant || !$this->tenant->subscription) {
            return 0;
        }

        $endDate = $this->tenant->subscription->end_date;
        return now()->diffInDays($endDate, false);
    }

    protected function getUsageStats()
    {
        if (!$this->tenant || !$this->tenant->plan) {
            return [];
        }

        return [
            'students' => [
                'used' => $this->tenant->users()->where('role', 'student')->count(),
                'total' => $this->tenant->plan->max_students,
            ],
            'teachers' => [
                'used' => $this->tenant->users()->where('role', 'teacher')->count(),
                'total' => $this->tenant->plan->max_teachers,
            ],
            'classes' => [
                'used' => $this->tenant->classes()->count(),
                'total' => $this->tenant->plan->max_classes,
            ],
        ];
    }

    public function render()
    {
        return view('components.subscription-status');
    }
}
