<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\Widgets\StaffOverview;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Log;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('firstname'),
                TextInput::make('lastname'),
                DatePicker::make('dob')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->label('Date of birth'),
                TextInput::make('email'),
                Select::make('roles')
                    ->label('Assign role')
                    ->relationship('roles', 'name', function ($query) {
                        return $query->where('name', '!=', 'super-admin');
                    })
            ]);
    }

    public static function getLabel(): null|string
    {
        return "Staff";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::role(User::staff()))
            ->columns([
                TextColumn::make('firstname')
                    ->searchable(),
                TextColumn::make('lastname')
                    ->searchable(),
                TextColumn::make('dob')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => "1" === $state ? __("Active") : __("Inactive")),
                TextColumn::make('roles.name')
                    ->label('Role'),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->placeholder('All staffs')
                    ->trueLabel('Active staffs')
                    ->falseLabel('Inactive staffs')
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Impersonate::make('Impersonate')
                        ->redirectTo(route('filament.admin.pages.dashboard'))
                        ->grouped()
                        ->icon('heroicon-o-key')
                        ->label('Login as'),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->hidden(fn() => !auth()->user()->can('super-admin')),
                ]),
            ])
            ->bulkActions([
                // ...
            ])
            ->searchPlaceholder('Search staff');
    }

    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('firstname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('lastname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('email')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('status')
                    ->formatStateUsing(function (string $state): string {
                        return "1" === $state ? __("Active") : __("Inactive");
                    })
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('roles.name')
                    ->label('Role')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('teacher_class.name')
                    // TODO: Hide if the role is not teacher
                    // ->hidden(fn($record) => $record->roles->name !== 'teacher')
                    ->label('Class')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
            'view' => Pages\ViewStaff::route('/{record}/view'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StaffOverview::class
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE,
            // User::$HUMAN_RESOURCE_ROLE
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
