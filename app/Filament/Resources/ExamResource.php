<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Models\Course;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationGroup = 'Courses & Exams';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Exams';


    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'dean',
            'professor',
        ]);
    }
    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
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
            return $query->whereHas('course', function (Builder $courseQuery) use ($user) {
                $courseQuery->where('owner_id', $user->id);
            });
        }

        if ($user->hasAnyRole(['student', 'representative_student'])) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Exam Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('course_id')
                                    ->label('Course')
                                    ->options(function () {
                                        $user = Auth::user();

                                        if (! $user) {
                                            return [];
                                        }

                                        if ($user->hasRole('super_admin')) {
                                            return Course::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        }

                                        if ($user->hasRole('professor')) {
                                            return Course::query()
                                                ->where('owner_id', $user->id)
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        }

                                        return Course::query()
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),

                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Toggle::make('is_practice')
                                    ->label('Practice Exam')
                                    ->default(true),

                                DateTimePicker::make('start_time')
                                    ->label('Start time')
                                    ->seconds(false)
                                    ->required(fn (Get $get) => ! $get('is_practice')),

                                DateTimePicker::make('end_time')
                                    ->label('End time')
                                    ->seconds(false)
                                    ->after('start_time')
                                    ->required(fn (Get $get) => ! $get('is_practice')),
                            ]),
                    ]),

                Section::make('Questions')
                    ->schema([
                        Repeater::make('questions')
                            ->label('Questions')
                            ->schema([
                                Forms\Components\Hidden::make('question_id'),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('type')
                                            ->label('Question Type')
                                            ->options([
                                                'mcq' => 'Multiple Choice',
                                                'true_false' => 'True / False',
                                                'short_answer' => 'Short Answer',
                                            ])
                                            ->default('mcq')
                                            ->required()
                                            ->live(),

                                        Select::make('difficulty')
                                            ->label('Difficulty')
                                            ->options([
                                                'easy' => 'Easy',
                                                'medium' => 'Medium',
                                                'hard' => 'Hard',
                                            ])
                                            ->default('medium')
                                            ->required(),

                                        Textarea::make('text')
                                            ->label('Question Text')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Repeater::make('choices')
                                            ->label('Choices')
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label('Choice')
                                                    ->required(),
                                            ])
                                            ->minItems(2)
                                            ->maxItems(6)
                                            ->defaultItems(4)
                                            ->visible(fn (Get $get) => $get('type') === 'mcq')
                                            ->columnSpanFull(),

                                        Select::make('correct_answer')
                                            ->label('Correct Answer')
                                            ->options(function (Get $get) {
                                                $choices = $get('choices') ?? [];

                                                return collect($choices)
                                                    ->pluck('value', 'value')
                                                    ->filter()
                                                    ->toArray();
                                            })
                                            ->required(fn (Get $get) => $get('type') === 'mcq')
                                            ->visible(fn (Get $get) => $get('type') === 'mcq'),

                                        Select::make('true_false_answer')
                                            ->label('Correct Answer')
                                            ->options([
                                                'true' => 'True',
                                                'false' => 'False',
                                            ])
                                            ->required(fn (Get $get) => $get('type') === 'true_false')
                                            ->visible(fn (Get $get) => $get('type') === 'true_false'),

                                        TextInput::make('short_answer')
                                            ->label('Correct Answer')
                                            ->required(fn (Get $get) => $get('type') === 'short_answer')
                                            ->visible(fn (Get $get) => $get('type') === 'short_answer'),

                                        TextInput::make('points')
                                            ->label('Points')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1)
                                            ->required(),
                                    ]),
                            ])
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Add Question')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_practice')
                    ->label('Practice')
                    ->boolean(),

                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('examQuestions_count')
                    ->label('Questions')
                    ->counts('examQuestions'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                ->visible(fn (Exam $record) => static::canCreate($record)),

                Tables\Actions\DeleteAction::make()
                ->visible(fn (Exam $record) => static::canCreate($record)),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasRole('super_admin')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'view'   => Pages\ViewExam::route('/{record}'),
            'edit'   => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
