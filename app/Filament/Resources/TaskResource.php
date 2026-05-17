<?php

namespace App\Filament\Resources;

use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Navigation\NavigationItem;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\Pages\MyTasks;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Managed Tasks';
    protected static ?int $navigationSort = 2;


    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'dean',
            'professor',
        ]);
    }
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $userDepartmentId = $user->profile?->department_id;

        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(4),

                DateTimePicker::make('due_date')
                    ->label('Due Date')
                    ->required(),

                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->default('medium')
                    ->required(),

                    Select::make('assigned_to')
                    ->label('Assign To')
                    ->options(function () use ($user, $userDepartmentId) {
                        if ($user->hasRole('super_admin')) {
                            return User::where('id', '!=', $user->id)
                                ->pluck('name', 'id');
                        }

                        if ($user->hasRole('dean')) {
                            return User::whereHas('profile', function ($query) use ($userDepartmentId) {
                                    $query->where('department_id', $userDepartmentId);
                                })
                                ->role(['professor', 'student'])
                                ->where('id', '!=', $user->id)
                                ->pluck('name', 'id');
                        }

                        if ($user->hasRole('professor')) {
                            return User::whereHas('profile', function ($query) use ($userDepartmentId) {
                                    $query->where('department_id', $userDepartmentId);
                                })
                                ->role('student')
                                ->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !auth()->user()->hasRole('student'))
                    ->helperText('Users are filtered based on your role and department.'),

                FileUpload::make('file_path')
                    ->label('Attach File')
                    ->disk('public')
                    ->directory('task-files')
                    ->preserveFilenames()
                    ->visibility('public'),

                    Repeater::make('subtasks')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')
                            ->label('Subtask Title')
                            ->required(),
                        Toggle::make('done')->label('Completed')
                    ])
                    ->columns(1)
                    ->defaultItems(1)
                    ->createItemButtonLabel('Add Subtask')
                    ->label('Subtasks'),

                    Select::make('status')
                    ->default('pending')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'in_review' => 'In Review',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->visible(fn () => !auth()->user()->hasRole('student')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('priority')->badge()->color(fn ($state) => match ($state) {
                    'low' => 'gray',
                    'medium' => 'warning',
                    'high' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'pending' => 'gray',
                    'in_progress' => 'info',
                    'in_review' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                })
                ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                ->label('Department')
                ->sortable()
                ->toggleable(),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Assigned To'),

                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),

                Tables\Columns\TextColumn::make('due_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                ->label('Completed At')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'in_review' => 'In Review',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Task $record): bool =>
                        (int) Auth::id() === (int) $record->created_by || Auth::user()->hasRole('super_admin')),
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'my-tasks' => MyTasks::route('/my-tasks'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        });
    }

}
