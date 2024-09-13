<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use App\Models\Result;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ViewResult extends ViewRecord
{
    protected static string $resource = ResultResource::class;
    protected static string $view = 'filament.pages.result.pages.view-result';
}
