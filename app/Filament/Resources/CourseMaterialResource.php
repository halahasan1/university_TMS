<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseMaterialResource\Pages;
use App\Models\CourseMaterial;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CourseMaterialResource extends Resource
{
    protected static ?string $model = CourseMaterial::class;

    protected static ?string $navigationGroup = 'Courses & Exams';
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Course Materials';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->directory('course-materials')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->downloadable()
                    ->openable()
                    ->nullable(),

                Actions::make([
                    Action::make('extract_text')
                        ->label('Extract Text')
                        ->icon('heroicon-m-document-text')
                        ->color('primary')
                        ->action(function (Forms\Get $get, Forms\Set $set) {
                            $fileState = $get('file_path');

                            if (empty($fileState)) {
                                Notification::make()
                                    ->title('Please upload a file first.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $fullPath = null;
                            $originalName = 'document.pdf';

                            try {
                                if (is_array($fileState)) {
                                    $fileState = reset($fileState);
                                }

                                if ($fileState instanceof TemporaryUploadedFile) {
                                    $fullPath = $fileState->getRealPath();
                                    $originalName = $fileState->getClientOriginalName();
                                } elseif (is_string($fileState) && filled($fileState)) {
                                    $fullPath = Storage::disk('public')->path($fileState);
                                    $originalName = basename($fileState);
                                }

                                if (! $fullPath || ! file_exists($fullPath)) {
                                    Notification::make()
                                        ->title('File not found or invalid file state.')
                                        ->body('State type: ' . (is_object($fileState) ? get_class($fileState) : gettype($fileState)))
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $response = Http::attach(
                                    'file',
                                    file_get_contents($fullPath),
                                    $originalName
                                )->post(rtrim(env('AI_API_URL'), '/') . '/extract');

                                if (! $response->successful()) {
                                    Notification::make()
                                        ->title('AI extraction failed.')
                                        ->body($response->body())
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $data = $response->json();
                                $text = $data['text'] ?? '';

                                if (! filled($text)) {
                                    Notification::make()
                                        ->title('No text extracted.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                $set('extracted_text', $text);

                                Notification::make()
                                    ->title('Text extracted successfully.')
                                    ->body('Characters: ' . strlen($text))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Error during extraction')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])->columnSpanFull(),

                Forms\Components\Textarea::make('extracted_text')
                    ->label('Extracted Text (for AI / RAG)')
                    ->rows(10)
                    ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded by')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index'  => Pages\ListCourseMaterials::route('/'),
            'create' => Pages\CreateCourseMaterial::route('/create'),
            'edit'   => Pages\EditCourseMaterial::route('/{record}/edit'),
        ];
    }
}
