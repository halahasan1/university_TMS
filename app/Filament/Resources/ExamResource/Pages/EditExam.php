<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\ExamQuestion;
use App\Models\Question;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected array $questionsData = [];

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->questionsData = $data['questions'] ?? [];

        unset($data['questions']);

        return $data;
    }

    protected function afterSave(): void
    {
        DB::transaction(function () {
            $keptQuestionIds = [];

            foreach ($this->questionsData as $questionData) {
                $preparedQuestionData = $this->prepareQuestionData($questionData);

                if (! empty($questionData['question_id'])) {
                    $question = Question::find($questionData['question_id']);

                    if ($question) {
                        $question->update($preparedQuestionData);
                    }
                } else {
                    $question = Question::create($preparedQuestionData);
                }

                $keptQuestionIds[] = $question->id;

                ExamQuestion::updateOrCreate(
                    [
                        'exam_id' => $this->record->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'points' => $questionData['points'] ?? 1,
                    ]
                );
            }

            $removedExamQuestions = $this->record->examQuestions()
                ->whereNotIn('question_id', $keptQuestionIds)
                ->get();

            foreach ($removedExamQuestions as $removedExamQuestion) {
                $question = $removedExamQuestion->question;

                $removedExamQuestion->delete();

                if ($question) {
                    $question->delete();
                }
            }
        });
    }

    private function prepareQuestionData(array $questionData): array
    {
        $type = $questionData['type'];

        $correctAnswer = null;

        if ($type === 'mcq') {
            $correctAnswer = $questionData['correct_answer'] ?? null;
        }

        if ($type === 'true_false') {
            $correctAnswer = $questionData['true_false_answer'] ?? null;
        }

        if ($type === 'short_answer') {
            $correctAnswer = $questionData['short_answer'] ?? null;
        }

        $choices = null;

        if ($type === 'mcq') {
            $choices = collect($questionData['choices'] ?? [])
                ->pluck('value')
                ->filter()
                ->values()
                ->toArray();
        }

        if ($type === 'true_false') {
            $choices = ['true', 'false'];
        }

        return [
            'course_id' => $this->record->course_id,
            'course_material_id' => null,
            'type' => $type,
            'text' => $questionData['text'],
            'choices' => $choices,
            'correct_answer' => $correctAnswer,
            'difficulty' => $questionData['difficulty'] ?? 'medium',
            'source' => 'manual',
            'status' => 'approved',
            'created_by' => Auth::id(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
