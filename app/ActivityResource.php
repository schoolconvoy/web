<?php

namespace App;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Z3d0X\FilamentLogger\Resources\ActivityResource as FilamentActivityResource;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Carbon\Carbon;
use App\Models\User;

class ActivityResource extends FilamentActivityResource
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->badge()
                    ->colors(static::getLogNameColors())
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable(),

            TextColumn::make('event')
                ->label(__('filament-logger::filament-logger.resource.label.event'))
                ->searchable()
                ->sortable(),

                TextColumn::make('description')
                    ->label(__('filament-logger::filament-logger.resource.label.description'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->wrap(),

                TextColumn::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject'))
                    ->formatStateUsing(function ($state, Model $record) {
                        /** @var Activity&ActivityModel $record */
                        if (!$state) {
                            return '-';
                        }
                        return Str::of($state)->afterLast('\\')->headline().' # '.$record->subject_id;
                    }),

                TextColumn::make('causer.firstname')
                    ->formatStateUsing(function ($state, Model $record) {
                        /** @var Activity&ActivityModel $record */
                        if (!$state) {
                            return '-';
                        }
                        return $state.' '.$record->causer->lastname;
                    })
                    ->label(__('filament-logger::filament-logger.resource.label.user'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                    ->dateTime(config('filament-logger.datetime_format', 'd/m/Y H:i:s'), config('app.timezone'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([])
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->options(static::getLogNameList()),

                SelectFilter::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject_type'))
                    ->options(static::getSubjectTypeList()),

                SelectFilter::make('event')
                    ->label(__('filament-logger::filament-logger.resource.label.event'))
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                        'forceDeleted' => 'Force Deleted',
                    ]),

                Filter::make('properties->old')
					->indicateUsing(function (array $data): ?string {
						if (!$data['old']) {
							return null;
						}

						return __('filament-logger::filament-logger.resource.label.old_attributes') . $data['old'];
					})
					->form([
						TextInput::make('old')
                            ->label(__('filament-logger::filament-logger.resource.label.old'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
					])
					->query(function (Builder $query, array $data): Builder {
						if (!$data['old']) {
							return $query;
						}

						return $query->where('properties->old', 'like', "%{$data['old']}%");
					}),

				Filter::make('properties->attributes')
					->indicateUsing(function (array $data): ?string {
						if (!$data['new']) {
							return null;
						}

						return __('filament-logger::filament-logger.resource.label.new_attributes') . $data['new'];
					})
					->form([
						TextInput::make('new')
                            ->label(__('filament-logger::filament-logger.resource.label.new'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
					])
					->query(function (Builder $query, array $data): Builder {
						if (!$data['new']) {
							return $query;
						}

						return $query->where('properties->attributes', 'like', "%{$data['new']}%");
					}),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('logged_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->displayFormat(config('filament-logger.date_format', 'd/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    }),
                // Add a custom filter for date range
                Filter::make('date_range')
                    ->form([
                        DateRangePicker::make('start_end_date')
                            ->label('Start date / End date')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['start_end_date']) && !empty($data['start_end_date'])) {
                            $split_date = explode(" - ", $data['start_end_date']);

                            if (count($split_date) === 2) {
                                $start_date = Carbon::createFromFormat('d/m/Y', trim($split_date[0]))->startOfDay();
                                $end_date = Carbon::createFromFormat('d/m/Y', trim($split_date[1]))->endOfDay();

                                return $query->whereBetween('created_at', [$start_date, $end_date]);
                            }
                        }

                        return $query;
                    }),
            ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE
        ]);
    }
}
