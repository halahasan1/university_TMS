<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Exam;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyExams extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.pages.my-exams';

    protected static ?string $navigationLabel = 'My Exams';

    protected static ?string $title = 'My Exams';

    public static function canAccess(): bool
    {
        return Auth::check()
            && Auth::user()->hasAnyRole([
                'student',
                'representative_student',
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getExams()
    {
        $user = Auth::user();

        return Exam::query()
            ->with([
                'course',
                'attempts' => function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                },
            ])
            ->whereHas('course', function ($query) use ($user) {
                $query->where('department_id', $user->profile?->department_id);
            })
            ->latest()
            ->get();
    }
}
