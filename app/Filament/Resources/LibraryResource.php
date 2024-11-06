<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibraryResource\Pages;
use App\Filament\Resources\LibraryResource\RelationManagers;
use App\Models\Library;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\LibraryCategory;

class LibraryResource extends Resource
{
    protected static ?string $model = Library::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Book';

    protected static ?string $navigationLabel = 'Library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->autofocus()
                    ->required(),
                Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required(),
                            Textarea::make('description')
                        ])
                        ->required(),
                Select::make('subcategory_id')
                        ->relationship('subcategory', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('description'),
                        ]),
                Textarea::make('description'),
                FileUpload::make('cover_image')
                            ->acceptedFileTypes(['.jpg', '.jpeg', '.png'])
                            ->image()
                            ->imageCropAspectRatio('1:1'),
                FileUpload::make('file')
                        ->label('Upload the book (optional)')
                        ->multiple(true)
                        ->acceptedFileTypes(['application/pdf'])
                        ->helperText('Upload a PDF file if this is a digital book'),
                Radio::make('type')
                            ->label('Book type')
                            ->required()
                            ->options([
                                'hard copy',
                                'digital'
                            ]),
                TextInput::make('author')
                            ->helperText('It would help if you wrote the Author of the book'),
                TextInput::make('publisher')
                            ->helperText('This may sometimes be different from the author'),
                TextInput::make('year')
                            ->label('Published year')
                            ->numeric()
                            ->helperText('What year was this book published?'),
                TextInput::make('edition')
                            ->helperText('What edition is this book?'),
                TextInput::make('isbn'),
                TextInput::make('pages')
                            ->numeric()
                            ->helperText('How many pages does this book have?'),
                TextInput::make('language')
                            ->default('English')
                            ->disabled(),
                TextInput::make('count')
                            ->helperText('How many copies of this book are in the library?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('author'),
                TextColumn::make('category.name'),
                TextColumn::make('type')->formatStateUsing(fn($state) => $state == 1 ? "Digital" : "Hard copy"),
                TextColumn::make('pages'),
                TextColumn::make('edition'),
                TextColumn::make('count')->formatStateUsing(fn($state) => $state . ' copies'),
            ])
            ->filters([
                SelectFilter::make('category.name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLibraries::route('/'),
            'create' => Pages\CreateLibrary::route('/create'),
            'edit' => Pages\EditLibrary::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
