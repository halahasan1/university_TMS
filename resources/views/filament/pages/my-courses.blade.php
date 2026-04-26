<x-filament::page>
@php
    $courses = $this->courses;

    $totalCourses = $courses->count();

    $departmentCount = $courses->pluck('department_id')->filter()->unique()->count();

    $newCoursesCount = $courses->filter(
        fn ($course) => $course->created_at && $course->created_at->gt(now()->subDays(7))
    )->count();
@endphp

<div class="mycourses-wrap">
    {{-- Header --}}
    <section class="soft-card page-hero">
        <div class="hero-text">
            <div class="eyebrow">Course workspace</div>
            <h1>My Courses</h1>
            <p>Browse the courses available to you based on your department, and submit your feedback for each course.</p>
        </div>
    </section>

    {{-- Summary cards --}}
    <section class="summary-grid">
        <div class="soft-card summary-card">
            <div class="summary-label">Total courses</div>
            <div class="summary-value">{{ $totalCourses }}</div>
            <div class="summary-sub">Courses assigned to your academic context</div>
        </div>

        <div class="soft-card summary-card">
            <div class="summary-label">Departments</div>
            <div class="summary-value">{{ $departmentCount }}</div>
            <div class="summary-sub">Departments represented in your courses</div>
        </div>

        <div class="soft-card summary-card">
            <div class="summary-label">New courses</div>
            <div class="summary-value">{{ $newCoursesCount }}</div>
            <div class="summary-sub">Recently added courses</div>
        </div>
    </section>

    {{-- Courses grid --}}
    <section class="courses-grid">
        @forelse($courses as $course)
            <div class="course-card">
                <div class="course-card-top">
                    <div>
                        <h3 class="course-title">{{ $course->name }}</h3>

                        <div class="course-meta">
                            @if(isset($course->code) && $course->code)
                                <span class="badge-soft badge-blue">{{ $course->code }}</span>
                            @endif

                            @if($course->department?->name)
                                <span class="badge-soft badge-gray">{{ $course->department->name }}</span>
                            @endif

                            @if(isset($course->academicYear) && $course->academicYear?->name)
                                <span class="badge-soft badge-yellow">{{ $course->academicYear->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($course->description) && $course->description)
                    <p class="course-description">
                        {{ \Illuminate\Support\Str::limit($course->description, 180) }}
                    </p>
                @endif

                <div class="course-info-grid">
                    @if(isset($course->department?->faculty) && $course->department?->faculty?->name)
                        <div class="course-info-item">
                            <span class="meta-label">Faculty</span>
                            <span class="meta-value">{{ $course->department?->faculty->name }}</span>
                        </div>
                    @endif

                    @if($course->department?->name)
                        <div class="course-info-item">
                            <span class="meta-label">Department</span>
                            <span class="meta-value">{{ $course->department->name }}</span>
                        </div>
                    @endif
                </div>

                <div class="course-bottom-row">
                    <button type="button" class="review-btn">
                        Write Review
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-section-box">
                No courses available for your current department yet.
            </div>
        @endforelse
    </section>
</div>

<style>
.mycourses-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 2rem;
}

.soft-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
}

.page-hero{
    padding: 1.5rem;
    margin-bottom: 1rem;
    background:
        radial-gradient(circle at top right, rgba(251, 191, 36, 0.08), transparent 25%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.07), transparent 20%),
        #ffffff;
}

.eyebrow{
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.45rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.page-hero h1{
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
}

.page-hero p{
    margin: 0.75rem 0 0;
    font-size: 1rem;
    line-height: 1.75;
    color: #6b7280;
    max-width: 760px;
}

.summary-grid{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.summary-card{
    padding: 1.1rem 1.2rem;
}

.summary-label{
    font-size: 0.8rem;
    color: #9ca3af;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.03em;
}

.summary-value{
    margin-top: 0.55rem;
    font-size: 1.8rem;
    font-weight: 700;
    color: #111827;
}

.summary-sub{
    margin-top: 0.4rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.courses-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.course-card{
    border: 1px solid #e9eef5;
    background: #fcfdff;
    border-radius: 18px;
    padding: 1rem;
    transition: 0.2s ease;
}

.course-card:hover{
    transform: translateY(-1px);
    border-color: #d8e3ef;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
}

.course-card-top{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
}

.course-title{
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.45;
    margin: 0;
}

.course-meta{
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.7rem;
}

.badge-soft{
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 0.34rem 0.7rem;
    font-size: 0.78rem;
    font-weight: 600;
}

.badge-gray{ background: #f3f4f6; color: #374151; }
.badge-blue{ background: #e0f2fe; color: #075985; }
.badge-yellow{ background: #fef3c7; color: #92400e; }

.course-description{
    margin-top: 1rem;
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.7;
}

.course-info-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.course-info-item{
    min-width: 110px;
}

.meta-label{
    display: block;
    font-size: 0.78rem;
    color: #9ca3af;
    margin-bottom: 0.2rem;
}

.meta-value{
    font-size: 0.92rem;
    font-weight: 600;
    color: #374151;
}

.course-bottom-row{
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}

.review-btn{
    border: 0;
    background: #eef4ff;
    color: #3159d1;
    border-radius: 12px;
    padding: 0.7rem 1rem;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
}

.review-btn:hover{
    background: #e4eeff;
}

.empty-section-box{
    grid-column: 1 / -1;
    border: 1px dashed #dbe2ea;
    background: #f8fafc;
    color: #94a3b8;
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    font-size: 0.95rem;
}

@media (max-width: 1100px){
    .summary-grid{
        grid-template-columns: 1fr;
    }

    .courses-grid{
        grid-template-columns: 1fr;
    }
}

html.dark .soft-card,
.fi-dark .soft-card,
[data-theme="dark"] .soft-card{
    background: #111827;
    border-color: #1f2937;
    box-shadow: none;
}

html.dark .page-hero,
.fi-dark .page-hero,
[data-theme="dark"] .page-hero{
    background:
        radial-gradient(circle at top right, rgba(251, 191, 36, 0.08), transparent 25%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 20%),
        #111827;
}

html.dark .page-hero h1,
html.dark .course-title,
.fi-dark .page-hero h1,
.fi-dark .course-title,
[data-theme="dark"] .page-hero h1,
[data-theme="dark"] .course-title{
    color: #f9fafb;
}

html.dark .page-hero p,
html.dark .summary-sub,
html.dark .course-description,
html.dark .meta-value,
.fi-dark .page-hero p,
.fi-dark .summary-sub,
.fi-dark .course-description,
.fi-dark .meta-value,
[data-theme="dark"] .page-hero p,
[data-theme="dark"] .summary-sub,
[data-theme="dark"] .course-description,
[data-theme="dark"] .meta-value{
    color: #d1d5db;
}

html.dark .summary-label,
html.dark .meta-label,
.fi-dark .summary-label,
.fi-dark .meta-label,
[data-theme="dark"] .summary-label,
[data-theme="dark"] .meta-label{
    color: #9ca3af;
}

html.dark .course-card,
.fi-dark .course-card,
[data-theme="dark"] .course-card{
    background: #0f172a;
    border-color: #1e293b;
}

html.dark .empty-section-box,
.fi-dark .empty-section-box,
[data-theme="dark"] .empty-section-box{
    background: #0f172a;
    border-color: #1f2937;
    color: #94a3b8;
}
</style>
</x-filament::page>
