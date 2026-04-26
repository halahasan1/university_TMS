<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationGroup = 'Courses & Exams';
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'Questions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('course_material_id')
                    ->relationship('material', 'title')
                    ->label('Material')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\Select::make('type')
                    ->options([
                        'mcq'          => 'Multiple Choice',
                        'tf'           => 'True / False',
                        'short_answer' => 'Short Answer',
                    ])
                    ->default('mcq')
                    ->required(),

                Forms\Components\Textarea::make('text')
                    ->label('Question Text')
                    ->required()
                    ->rows(4),

                Forms\Components\KeyValue::make('choices')
                    ->label('Choices (for MCQ)')
                    ->keyLabel('Label')
                    ->valueLabel('Text')
                    ->nullable(),

                Forms\Components\TextInput::make('correct_answer')
                    ->label('Correct Answer')
                    ->nullable(),

                Forms\Components\Select::make('difficulty')
                    ->options([
                        'easy'   => 'Easy',
                        'medium' => 'Medium',
                        'hard'   => 'Hard',
                    ])
                    ->nullable(),

                Forms\Components\Select::make('source')
                    ->options([
                        'manual' => 'Manual',
                        'ai'     => 'AI Generated',
                    ])
                    ->default('manual'),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft'    => 'Draft',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('material.title')
                    ->label('Material')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('difficulty')->sortable(),
                Tables\Columns\TextColumn::make('source')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit'   => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
