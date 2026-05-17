<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use App\Models\StudentReview;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MyCourses extends Page
{
    protected static string $resource = CourseResource::class;

    protected static string $view = 'filament.pages.my-courses';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'My Courses';

    protected static ?string $title = 'My Courses';

    public static function canAccess(array $parameters = []): bool
    {
        return Auth::check()
            && Auth::user()->hasAnyRole([
                'student',
                'representative_student',
            ]);
    }

    public function getCoursesProperty()
    {
        $user = Auth::user();
        $departmentId = $user?->profile?->department_id;

        return Course::query()
            ->with(['department.faculty', 'academicYear'])
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->latest()
            ->get();
    }

    public function writeReviewAction(): Action
    {
        return Action::make('writeReview')
            ->label('Write Review')
            ->modalHeading('Write Course Review')
            ->modalSubmitActionLabel('Submit Review')
            ->form([
                Textarea::make('review_text')
                    ->label('Your Review')
                    ->placeholder('Write your feedback about this course...')
                    ->required()
                    ->rows(6)
                    ->minLength(5),
            ])
            ->action(function (array $data, array $arguments) {
                $courseId = $arguments['course'] ?? null;

                if (! $courseId) {
                    Notification::make()
                        ->title('Course not found')
                        ->danger()
                        ->send();

                    return;
                }

                try {
                    $response = Http::timeout(30)
                        ->post(rtrim(env('AI_API_URL'), '/') . '/predict', [
                            'text' => $data['review_text'],
                        ]);

                    if (! $response->successful()) {
                        Notification::make()
                            ->title('AI API failed')
                            ->body($response->body())
                            ->danger()
                            ->send();

                        return;
                    }

                    $result = $response->json();

                    StudentReview::create([
                        'user_id' => Auth::id(),
                        'course_id' => $courseId,
                        'review_text' => $data['review_text'],
                        'predicted_label' => $result['prediction_label'] ?? 'unknown',
                        'confidence_score' => $result['confidence'] ?? null,
                    ]);

                    Notification::make()
                        ->title('Review submitted successfully')
                        ->body('Prediction: ' . ($result['prediction_label'] ?? 'Unknown'))
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Error while submitting review')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
