<?php

namespace App\Filament\Resources;

use App\Models\Department;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\DepartmentResource\Pages;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $modelLabel = 'Department';
    protected static ?string $pluralModelLabel = 'Departments';
    protected static ?string $navigationGroup = 'Academic Management';


    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage-departments');
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Department Name')
                ->required()
                ->maxLength(100),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('profiles_count')
                ->label('Users')
                ->counts('profiles'),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
