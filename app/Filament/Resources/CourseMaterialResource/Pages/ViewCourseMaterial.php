<?php

namespace App\Filament\Resources\CourseMaterialResource\Pages;

use App\Filament\Resources\CourseMaterialResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseMaterial extends ViewRecord
{
    protected static string $resource = CourseMaterialResource::class;

    protected static string $view = 'filament.pages.view-course-material';
    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_file')
                ->label('Open File')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn () => $this->record->file_path
                    ? asset('storage/' . $this->record->file_path)
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record->file_path)),

            Actions\Action::make('summarize')
                ->label('Summarize Lecture')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->action(function () {
                    if (! filled($this->record->extracted_text)) {
                        Notification::make()
                            ->title('No extracted text found')
                            ->body('Please extract the lecture text first before summarizing.')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Summarization will be connected with AI soon.')
                        ->body('This button is ready for the AI integration.')
                        ->success()
                        ->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
