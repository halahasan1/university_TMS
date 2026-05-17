<x-filament-panels::page>
    <style>
        .exam-result-page {
            --orange: #f97316;
            --orange-dark: #c2410c;
            --green: #16a34a;
            --green-dark: #166534;
            --green-soft: #dcfce7;
            --red: #dc2626;
            --red-dark: #991b1b;
            --red-soft: #fee2e2;
            --blue-soft: #eff6ff;
            --ink: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
        }

        .exam-shell {
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .exam-hero {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            padding: 34px;
            background:
                radial-gradient(circle at 8% 20%, rgba(249, 115, 22, 0.20), transparent 34%),
                radial-gradient(circle at 88% 0%, rgba(59, 130, 246, 0.14), transparent 28%),
                linear-gradient(135deg, #ffffff, #fff7ed);
            border: 1px solid rgba(249, 115, 22, 0.20);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.10);
        }

        .exam-hero::after {
            content: "";
            position: absolute;
            right: -80px;
            bottom: -80px;
            width: 230px;
            height: 230px;
            border-radius: 999px;
            background: rgba(249, 115, 22, 0.13);
        }

        .exam-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: flex-start;
        }

        .exam-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 13px;
            background: rgba(255, 255, 255, 0.82);
            color: var(--orange-dark);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .exam-title {
            margin-top: 16px;
            font-size: clamp(32px, 4vw, 50px);
            line-height: 1;
            font-weight: 950;
            color: var(--ink);
            letter-spacing: -0.05em;
        }

        .exam-course {
            margin-top: 12px;
            color: #374151;
            font-size: 15px;
            font-weight: 800;
        }

        .exam-desc {
            margin-top: 12px;
            max-width: 720px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .score-panel {
            min-width: 230px;
            border-radius: 26px;
            padding: 22px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(18px);
            box-shadow: 0 20px 46px rgba(15, 23, 42, 0.10);
            text-align: center;
        }

        .score-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .score-value {
            margin-top: 8px;
            font-size: 44px;
            font-weight: 950;
            color: var(--green-dark);
            letter-spacing: -0.05em;
        }

        .score-status {
            margin-top: 8px;
            display: inline-flex;
            border-radius: 999px;
            padding: 7px 12px;
            background: var(--green-soft);
            color: var(--green-dark);
            font-size: 12px;
            font-weight: 900;
        }

        .end-time-box {
            margin-top: 18px;
            display: inline-flex;
            border-radius: 16px;
            padding: 11px 14px;
            background: #fff1f2;
            color: var(--red-dark);
            font-size: 13px;
            font-weight: 850;
            border: 1px solid #fecdd3;
        }

        .questions-list {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .question-card {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: 0 22px 52px rgba(15, 23, 42, 0.075);
        }

        .question-card.is-correct {
            background:
                linear-gradient(135deg, rgba(220, 252, 231, 0.98), rgba(255, 255, 255, 0.98));
            border: 2px solid rgba(22, 163, 74, 0.40);
            box-shadow: 0 24px 56px rgba(22, 163, 74, 0.13);
        }

        .question-card.is-wrong {
            background:
                linear-gradient(135deg, rgba(254, 226, 226, 0.98), rgba(255, 255, 255, 0.98));
            border: 2px solid rgba(220, 38, 38, 0.42);
            box-shadow: 0 24px 56px rgba(220, 38, 38, 0.14);
        }

        .question-card.is-correct::before,
        .question-card.is-wrong::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
        }

        .question-card.is-correct::before {
            background: var(--green);
        }

        .question-card.is-wrong::before {
            background: var(--red);
        }

        .question-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 20px;
        }

        .question-number-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .question-number {
            font-size: 18px;
            font-weight: 950;
            color: var(--ink);
            letter-spacing: -0.02em;
        }

        .question-status {
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 950;
        }

        .question-status.correct {
            background: #bbf7d0;
            color: var(--green-dark);
        }

        .question-status.wrong {
            background: #fecaca;
            color: var(--red-dark);
        }

        .question-text {
            margin-top: 10px;
            color: var(--ink);
            font-size: 16px;
            line-height: 1.8;
            font-weight: 800;
        }

        .points-pill {
            white-space: nowrap;
            border-radius: 999px;
            padding: 9px 13px;
            background: #ffffff;
            color: #374151;
            font-size: 13px;
            font-weight: 950;
            border: 1px solid rgba(209, 213, 219, 0.95);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
        }

        .answers-grid {
            display: grid;
            gap: 12px;
        }

        .answer-option {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 56px;
            border-radius: 18px;
            padding: 14px 16px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #111827;
            transition: 0.18s ease;
        }

        .answer-option:not(.disabled):hover {
            transform: translateY(-1px);
            border-color: rgba(249, 115, 22, 0.45);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.07);
        }

        .answer-option.correct-answer {
            background: #dcfce7 !important;
            border: 2px solid #22c55e !important;
            color: #14532d !important;
            box-shadow: 0 16px 34px rgba(22, 163, 74, 0.16);
        }

        .answer-option.wrong-answer {
            background: #fee2e2 !important;
            border: 2px solid #ef4444 !important;
            color: #7f1d1d !important;
            box-shadow: 0 16px 34px rgba(220, 38, 38, 0.15);
        }

        .answer-option.neutral-after-submit {
            background: rgba(255, 255, 255, 0.68);
            opacity: 0.65;
        }

        .answer-option input {
            width: 18px;
            height: 18px;
            accent-color: var(--orange);
        }

        .answer-text {
            font-size: 14px;
            font-weight: 850;
            line-height: 1.6;
        }

        .answer-tag {
            margin-left: auto;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 950;
            white-space: nowrap;
        }

        .answer-tag.correct {
            background: #16a34a;
            color: #ffffff;
        }

        .answer-tag.wrong {
            background: #dc2626;
            color: #ffffff;
        }

        .short-answer-box {
            width: 100%;
            min-height: 120px;
            border-radius: 20px;
            padding: 15px;
            font-size: 15px;
            font-weight: 750;
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: var(--ink);
        }

        .short-answer-box.correct {
            background: #dcfce7 !important;
            border: 2px solid #22c55e !important;
            color: #14532d !important;
        }

        .short-answer-box.wrong {
            background: #fee2e2 !important;
            border: 2px solid #ef4444 !important;
            color: #7f1d1d !important;
        }

        .result-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .result-box {
            border-radius: 18px;
            padding: 14px;
            font-size: 13px;
            font-weight: 800;
        }

        .result-box.student {
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .result-box.correct {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .result-label {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            opacity: 0.75;
        }

        .submit-bar {
            position: sticky;
            bottom: 18px;
            z-index: 20;
            display: flex;
            justify-content: flex-end;
            padding: 14px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(229, 231, 235, 0.92);
            backdrop-filter: blur(18px);
            box-shadow: 0 24px 56px rgba(15, 23, 42, 0.12);
        }

        .submit-btn {
            border: 0;
            border-radius: 18px;
            padding: 13px 22px;
            background: linear-gradient(135deg, #f97316, #d97706);
            color: #ffffff;
            font-size: 14px;
            font-weight: 950;
            box-shadow: 0 18px 32px rgba(217, 119, 6, 0.26);
            transition: 0.2s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            filter: brightness(0.98);
        }

        @media (max-width: 768px) {
            .exam-hero-content,
            .question-head {
                flex-direction: column;
            }

            .score-panel {
                width: 100%;
            }

            .result-details {
                grid-template-columns: 1fr;
            }

            .answer-tag {
                margin-left: 0;
            }

            .answer-option {
                align-items: flex-start;
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="exam-result-page exam-shell">
        <div class="exam-hero">
            <div class="exam-hero-content">
                <div>
                    <div class="exam-kicker">
                        Take Exam
                    </div>

                    <h1 class="exam-title">
                        {{ $exam->title }}
                    </h1>

                    <div class="exam-course">
                        Course: {{ $exam->course?->name }}
                    </div>

                    @if ($exam->description)
                        <div class="exam-desc">
                            {{ $exam->description }}
                        </div>
                    @endif

                    @if ($exam->end_time && ! $submitted)
                        <div class="end-time-box">
                            End time: {{ $exam->end_time->format('Y-m-d h:i A') }}
                        </div>
                    @endif
                    <a
                        href="{{ route('filament.adminPanel.pages.my-exams') }}"
                        class="inline-flex mt-4 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Back to My Exams
                    </a>
                </div>

                @if ($submitted)
                    <div class="score-panel">
                        <div class="score-label">
                            Final Score
                        </div>

                        <div class="score-value">
                            {{ $attempt->score }}%
                        </div>

                        <div class="score-status">
                            Submitted
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="submit" class="questions-list">
            @foreach ($exam->examQuestions as $index => $examQuestion)
                @php
                    $question = $examQuestion->question;

                    $studentAnswer = $answers[$question->id] ?? null;
                    $correctAnswer = $question->correct_answer;

                    $normalizedStudent = mb_strtolower(trim((string) $studentAnswer));
                    $normalizedCorrect = mb_strtolower(trim((string) $correctAnswer));

                    $isCorrect = $submitted && $normalizedStudent !== '' && $normalizedStudent === $normalizedCorrect;
                    $isWrong = $submitted && ! $isCorrect;
                @endphp

                <div class="question-card {{ $submitted && $isCorrect ? 'is-correct' : '' }} {{ $submitted && $isWrong ? 'is-wrong' : '' }}">
                    <div class="question-head">
                        <div>
                            <div class="question-number-row">
                                <div class="question-number">
                                    Question {{ $index + 1 }}
                                </div>

                                @if ($submitted && $isCorrect)
                                    <div class="question-status correct">
                                        Correct
                                    </div>
                                @elseif ($submitted && $isWrong)
                                    <div class="question-status wrong">
                                        Wrong
                                    </div>
                                @endif
                            </div>

                            <div class="question-text">
                                {{ $question->text }}
                            </div>
                        </div>

                        <div class="points-pill">
                            {{ $examQuestion->points }} points
                        </div>
                    </div>

                    @if ($question->type === 'mcq')
                        <div class="answers-grid">
                            @foreach ($question->choices ?? [] as $choice)
                                @php
                                    $choiceIsStudentAnswer = (string) $studentAnswer === (string) $choice;
                                    $choiceIsCorrectAnswer = (string) $correctAnswer === (string) $choice;

                                    $optionClass = '';

                                    if ($submitted && $choiceIsCorrectAnswer) {
                                        $optionClass = 'correct-answer disabled';
                                    } elseif ($submitted && $choiceIsStudentAnswer && ! $choiceIsCorrectAnswer) {
                                        $optionClass = 'wrong-answer disabled';
                                    } elseif ($submitted) {
                                        $optionClass = 'neutral-after-submit disabled';
                                    }
                                @endphp

                                <label class="answer-option {{ $optionClass }}">
                                    <input
                                        type="radio"
                                        wire:model="answers.{{ $question->id }}"
                                        value="{{ $choice }}"
                                        @disabled($submitted)
                                    >

                                    <span class="answer-text">
                                        {{ $choice }}
                                    </span>

                                    @if ($submitted && $choiceIsCorrectAnswer)
                                        <span class="answer-tag correct">
                                            Correct answer
                                        </span>
                                    @elseif ($submitted && $choiceIsStudentAnswer && ! $choiceIsCorrectAnswer)
                                        <span class="answer-tag wrong">
                                            Your answer
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>

                    @elseif ($question->type === 'true_false')
                        <div class="answers-grid">
                            @foreach (['true' => 'True', 'false' => 'False'] as $value => $label)
                                @php
                                    $choiceIsStudentAnswer = (string) $studentAnswer === (string) $value;
                                    $choiceIsCorrectAnswer = (string) $correctAnswer === (string) $value;

                                    $optionClass = '';

                                    if ($submitted && $choiceIsCorrectAnswer) {
                                        $optionClass = 'correct-answer disabled';
                                    } elseif ($submitted && $choiceIsStudentAnswer && ! $choiceIsCorrectAnswer) {
                                        $optionClass = 'wrong-answer disabled';
                                    } elseif ($submitted) {
                                        $optionClass = 'neutral-after-submit disabled';
                                    }
                                @endphp

                                <label class="answer-option {{ $optionClass }}">
                                    <input
                                        type="radio"
                                        wire:model="answers.{{ $question->id }}"
                                        value="{{ $value }}"
                                        @disabled($submitted)
                                    >

                                    <span class="answer-text">
                                        {{ $label }}
                                    </span>

                                    @if ($submitted && $choiceIsCorrectAnswer)
                                        <span class="answer-tag correct">
                                            Correct answer
                                        </span>
                                    @elseif ($submitted && $choiceIsStudentAnswer && ! $choiceIsCorrectAnswer)
                                        <span class="answer-tag wrong">
                                            Your answer
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>

                    @elseif ($question->type === 'short_answer')
                        <textarea
                            wire:model="answers.{{ $question->id }}"
                            class="short-answer-box {{ $submitted && $isCorrect ? 'correct' : '' }} {{ $submitted && $isWrong ? 'wrong' : '' }}"
                            rows="3"
                            @disabled($submitted)
                        ></textarea>
                    @endif

                    @if ($submitted)
                        <div class="result-details">
                            <div class="result-box student">
                                <span class="result-label">Your Answer</span>
                                {{ $studentAnswer ?: 'No answer' }}
                            </div>

                            <div class="result-box correct">
                                <span class="result-label">Correct Answer</span>
                                {{ $correctAnswer }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            @if (! $submitted)
                <div class="submit-bar">
                    <button
                        type="submit"
                        class="submit-btn"
                        onclick="return confirm('Are you sure you want to submit? You cannot edit your answers after submission.')"
                    >
                        Submit Exam
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>
