<?php

namespace App\Filament\Parent\Resources\FeeResource\Pages;

use App\Filament\Parent\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFee extends CreateRecord
{
    protected static string $resource = FeeResource::class;
}
