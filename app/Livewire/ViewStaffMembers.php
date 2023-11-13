<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Illuminate\Contracts\View\View as LaravelView;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class ViewStaffMembers extends Component implements HasForms, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('firstname')
                            ->url(fn(User $user) => route('user.edit', $user)),
                TextColumn::make('lastname'),
                TextColumn::make('dob'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('roles.name')
                            ->label('Role'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                            ->requiresConfirmation()
                    ->hidden(fn() => auth()->user()->can('super-admin')),
                ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render(): LaravelView
    {
        return view('livewire.view-staff-members');
    }
}
