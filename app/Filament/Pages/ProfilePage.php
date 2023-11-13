<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProfilePage extends Page
{

    protected static string $view = 'filament.pages.profile-page';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Settings';

}
