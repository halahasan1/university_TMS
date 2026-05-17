<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\ExamQuestion;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExam extends ViewRecord
{
    protected static string $resource = ExamResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['questions'] = $this->record->examQuestions()
            ->with('question')
            ->get()
            ->map(function (ExamQuestion $examQuestion) {
                $question = $examQuestion->question;

                $choices = [];

                if ($question->type === 'mcq') {
                    $choices = collect($question->choices ?? [])
                        ->map(fn ($choice) => ['value' => $choice])
                        ->values()
                        ->toArray();
                }

                return [
                    'question_id' => $question->id,
                    'type' => $question->type,
                    'text' => $question->text,
                    'choices' => $choices,
                    'correct_answer' => $question->type === 'mcq'
                        ? $question->correct_answer
                        : null,
                    'true_false_answer' => $question->type === 'true_false'
                        ? $question->correct_answer
                        : null,
                    'short_answer' => $question->type === 'short_answer'
                        ? $question->correct_answer
                        : null,
                    'difficulty' => $question->difficulty,
                    'points' => $examQuestion->points,
                ];
            })
            ->toArray();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
