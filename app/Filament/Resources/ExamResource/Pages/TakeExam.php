<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class TakeExam extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static string $view = 'filament.pages.take-exam';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'take-exam/{exam}';

    public Exam $exam;

    public ?ExamAttempt $attempt = null;

    public array $answers = [];

    public bool $submitted = false;

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load('course', 'examQuestions.question');

        $now = now();

        if ($this->exam->start_time && $now->lt($this->exam->start_time)) {
            abort(403, 'This exam has not started yet.');
        }

        if ($this->exam->end_time && $now->gt($this->exam->end_time)) {
            abort(403, 'This exam has ended.');
        }

        $this->attempt = ExamAttempt::firstOrCreate(
            [
                'exam_id' => $this->exam->id,
                'student_id' => Auth::id(),
            ],
            [
                'started_at' => now(),
            ]
        );

        if ($this->attempt->finished_at) {
            $this->submitted = true;

            $this->answers = $this->attempt->answers()
                ->pluck('answer', 'question_id')
                ->toArray();

            return;
        }

        foreach ($this->exam->examQuestions as $examQuestion) {
            $this->answers[$examQuestion->question_id] = null;
        }
    }

    public function submit(): void
    {
        if ($this->attempt->finished_at) {
            return;
        }

        if ($this->exam->end_time && now()->gt($this->exam->end_time)) {
            Notification::make()
                ->title('Exam time has ended.')
                ->danger()
                ->send();

            return;
        }

        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($this->exam->examQuestions as $examQuestion) {
            $question = $examQuestion->question;

            $studentAnswer = $this->answers[$question->id] ?? null;

            $isCorrect = $this->isAnswerCorrect($studentAnswer, $question->correct_answer);

            $points = (float) $examQuestion->points;

            $totalPoints += $points;

            if ($isCorrect) {
                $earnedPoints += $points;
            }

            ExamAnswer::updateOrCreate(
                [
                    'exam_attempt_id' => $this->attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer' => $studentAnswer,
                    'is_correct' => $isCorrect,
                ]
            );
        }

        $score = $totalPoints > 0
            ? round(($earnedPoints / $totalPoints) * 100, 2)
            : 0;

        $this->attempt->update([
            'finished_at' => now(),
            'score' => $score,
        ]);

        $this->submitted = true;

        Notification::make()
            ->title('Exam submitted successfully.')
            ->body('Your score is ' . $score . '%')
            ->success()
            ->send();
    }

    private function isAnswerCorrect(?string $studentAnswer, ?string $correctAnswer): bool
    {
        if ($studentAnswer === null || $correctAnswer === null) {
            return false;
        }

        return mb_strtolower(trim($studentAnswer)) === mb_strtolower(trim($correctAnswer));
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
