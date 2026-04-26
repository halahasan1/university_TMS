<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Models\Course;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\CourseResource;
use Illuminate\Support\Collection;

class MyCourses extends Page
{
    protected static string $resource = CourseResource::class;

    protected static string $view = 'filament.pages.my-courses';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'My Courses';
    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function getCoursesProperty(): Collection
    {
        $user = auth()->user();
        $profile = $user?->profile;

        if (! $profile) {
            return collect();
        }

        $query = Course::query()
        ->with([
            'department.faculty',
            'academicYear',
        ])
        ->latest();

        // حاليًا حسب القسم
        if ($profile->department_id) {
            $query->where('department_id', $profile->department_id);
        }

        // // إذا عندك faculty_id بالمستقبل وتريدين تضييق أكثر
        // if (
        //     isset($profile->faculty_id) &&
        //     $profile->faculty_id &&
        //     \Schema::hasColumn('courses', 'faculty_id')
        // ) {
        //     $query->where('faculty_id', $profile->faculty_id);
        // }

        // لاحقًا إذا صار عندك academic_year_id بالبروفايل:
        /*
        if (
            isset($profile->academic_year_id) &&
            $profile->academic_year_id &&
            \Schema::hasColumn('courses', 'academic_year_id')
        ) {
            $query->where('academic_year_id', $profile->academic_year_id);
        }
        */

        return $query->get();
    }
}
