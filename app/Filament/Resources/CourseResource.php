<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationGroup = 'Courses & Exams';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Courses';

    // public static function canAccess(): bool
    // {
    //     $user = Auth::user();

    //     return $user && $user->hasAnyRole([
    //         'super_admin',
    //         'professor',
    //     ]);
    // }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    // public static function canView($record): bool
    // {
    //     $user = Auth::user();

    //     if (! $user) {
    //         return false;
    //     }

    //     if ($user->hasRole('super_admin')) {
    //         return true;
    //     }

    //     if ($user->hasRole('professor')) {
    //         return $record->owner_id === $user->id;
    //     }

    //     return false;
    // }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'dean',
            'professor',
        ]);
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('professor')) {
            return $query->where('owner_id', $user->id);
        }

        if ($user->hasAnyRole(['student', 'representative_student','dean'])) {
            $departmentId = $user->profile?->department_id;

            return $query->when(
                $departmentId,
                fn ($q) => $q->where('department_id', $departmentId),
                fn ($q) => $q->whereRaw('1 = 0')
            );
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'label')
                    ->label('Academic Year')
                    ->required()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->maxLength(50)
                    ->placeholder('RTS401'),

                Forms\Components\Textarea::make('description')
                    ->rows(3),

                Forms\Components\Select::make('owner_id')
                    ->relationship(
                        name: 'owner',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->role('professor')
                    )
                    ->label('Owner (Professor)')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.faculty.name')
                    ->label('Faculty')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academicYear.label')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('materials_count')
                    ->label('Materials')
                    ->counts('materials')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Course')
                    ->icon('heroicon-o-eye'),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListCourses::route('/'),
            'create'     => Pages\CreateCourse::route('/create'),
            'edit'       => Pages\EditCourse::route('/{record}/edit'),
            'my-courses' => Pages\MyCourses::route('/my-courses'),
            'view'       => Pages\ViewCourse::route('/{record}'),
        ];
    }
}
