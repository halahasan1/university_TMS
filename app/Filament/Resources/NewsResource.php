<?php

// app/Filament/Resources/NewsResource.php
namespace App\Filament\Resources;

use App\Models\News;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\NewsResource\Pages;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'University News';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('audience_type')
                ->label('Audience')
                ->options([
                    'global' => 'All University',
                    'department_only' => 'My Department Only',
                ])
                ->default('global')
                ->required()
                ->visible(fn () => auth()->user()->hasAnyRole(['professor', 'dean', 'student-representative'])),

                Forms\Components\RichEditor::make('body')
                ->required()
                ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                ->label('Post Images')
                ->multiple()
                ->image()
                ->directory('news-images')
                ->reorderable()
                ->maxFiles(5)
                ->openable()
                ->enableOpen()
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('post_card')
                    ->label('')
                    ->view('filament.resources.news-resource.card', fn ($record) => [
                        'record' => $record,
                    ]),

            ])
            ->recordUrl(null)
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
            'view' => Pages\ViewNews::route('/{record}'),
        ];
    }


}
