<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SubscriptionStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.subscription-status-widget';

    protected int | string | array $columnSpan = 'full';

    public function getTenant()
    {
        return Tenant::find(Auth::user()->school_id);
    }
}
