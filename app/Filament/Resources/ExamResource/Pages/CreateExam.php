<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\ExamQuestion;
use App\Models\Question;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected array $questionsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->questionsData = $data['questions'] ?? [];

        unset($data['questions']);

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->questionsData as $questionData) {
            $correctAnswer = null;

            if ($questionData['type'] === 'mcq') {
                $correctAnswer = $questionData['correct_answer'] ?? null;
            }

            if ($questionData['type'] === 'true_false') {
                $correctAnswer = $questionData['true_false_answer'] ?? null;
            }

            if ($questionData['type'] === 'short_answer') {
                $correctAnswer = $questionData['short_answer'] ?? null;
            }

            $choices = null;

            if ($questionData['type'] === 'mcq') {
                $choices = collect($questionData['choices'] ?? [])
                    ->pluck('value')
                    ->filter()
                    ->values()
                    ->toArray();
            }

            if ($questionData['type'] === 'true_false') {
                $choices = ['true', 'false'];
            }

            $question = Question::create([
                'course_id' => $this->record->course_id,
                'course_material_id' => null,
                'type' => $questionData['type'],
                'text' => $questionData['text'],
                'choices' => $choices,
                'correct_answer' => $correctAnswer,
                'difficulty' => $questionData['difficulty'] ?? 'medium',
                'source' => 'manual',
                'status' => 'approved',
                'created_by' => Auth::id(),
            ]);

            ExamQuestion::create([
                'exam_id' => $this->record->id,
                'question_id' => $question->id,
                'points' => $questionData['points'] ?? 1,
            ]);
        }
    }
}
