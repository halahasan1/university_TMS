<x-filament::page>
@php
    $course = $this->record;
    $materials = \App\Models\CourseMaterial::query()
        ->where('course_id', $course->id)
        ->latest()
        ->get();

    $materialsCount = $materials->count();
    $extractedCount = $materials->filter(fn ($m) => filled($m->extracted_text))->count();
@endphp

<div class="course-view-wrap">
    <section class="course-hero">
        <div>
            <div class="eyebrow">Course Details</div>
            <h1>{{ $course->name }}</h1>

            <div class="course-badges">
                @if($course->department?->name)
                    <span>{{ $course->department->name }}</span>
                @endif

                @if($course->academicYear?->name)
                    <span>{{ $course->academicYear->name }}</span>
                @endif
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <span>Total Lectures</span>
            <strong>{{ $materialsCount }}</strong>
        </div>

        <div class="stat-card">
            <span>Ready For AI</span>
            <strong>{{ $extractedCount }}</strong>
        </div>

        <div class="stat-card">
            <span>Pending Extraction</span>
            <strong>{{ $materialsCount - $extractedCount }}</strong>
        </div>
    </section>

    <section class="lectures-card">
        <div class="section-header">
            <div>
                <h2>Course Lectures</h2>
                <p>View uploaded lectures and prepare them for summarization.</p>
            </div>
        </div>

        <div class="lectures-table">
            <table>
                <thead>
                    <tr>
                        <th>Lecture</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($materials as $material)
                        <tr>
                            <td>
                                <div class="lecture-title">{{ $material->title }}</div>
                                <div class="lecture-sub">{{ $material->uploader?->name ?? 'Unknown uploader' }}</div>
                            </td>

                            <td>
                                @if(filled($material->extracted_text))
                                    <span class="status ready">Text Extracted</span>
                                @else
                                    <span class="status pending">Needs Extraction</span>
                                @endif
                            </td>

                            <td>
                                {{ $material->created_at?->format('Y-m-d H:i') }}
                            </td>

                            <td class="actions-cell">
                                <a
                                    href="{{ \App\Filament\Resources\CourseMaterialResource::getUrl('view', ['record' => $material]) }}"
                                    class="view-btn"
                                >
                                    View Lecture
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-cell">
                                No lectures uploaded for this course yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
.course-view-wrap{
    max-width: 1200px;
    margin: 0 auto;
}

.course-hero{
    border: 1px solid #e9eef5;
    border-radius: 24px;
    padding: 1.7rem;
    background:
        radial-gradient(circle at top right, rgba(234, 121, 0, 0.12), transparent 28%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 25%),
        #ffffff;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    margin-bottom: 1rem;
}

.eyebrow{
    color: #ea7900;
    font-weight: 800;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.course-hero h1{
    margin: .4rem 0 .8rem;
    font-size: 2.2rem;
    font-weight: 800;
    color: #111827;
}

.course-badges{
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.course-badges span{
    background: #fff7ed;
    color: #9a3412;
    border-radius: 999px;
    padding: .35rem .75rem;
    font-size: .82rem;
    font-weight: 700;
}

.stats-grid{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 18px;
    padding: 1.2rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
}

.stat-card span{
    display: block;
    color: #6b7280;
    font-size: .85rem;
    font-weight: 700;
}

.stat-card strong{
    display: block;
    margin-top: .5rem;
    color: #111827;
    font-size: 2rem;
}

.lectures-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 22px;
    padding: 1.2rem;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
}

.section-header h2{
    margin: 0;
    color: #111827;
    font-size: 1.35rem;
    font-weight: 800;
}

.section-header p{
    margin: .35rem 0 1rem;
    color: #6b7280;
}

.lectures-table{
    overflow-x: auto;
}

.lectures-table table{
    width: 100%;
    border-collapse: collapse;
}

.lectures-table th{
    text-align: left;
    color: #9ca3af;
    font-size: .78rem;
    text-transform: uppercase;
    padding: .8rem;
    border-bottom: 1px solid #eef2f7;
}

.lectures-table td{
    padding: .9rem .8rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
}

.lecture-title{
    font-weight: 800;
    color: #111827;
}

.lecture-sub{
    margin-top: .25rem;
    font-size: .82rem;
    color: #9ca3af;
}

.status{
    border-radius: 999px;
    padding: .35rem .65rem;
    font-size: .78rem;
    font-weight: 800;
}

.status.ready{
    background: #dcfce7;
    color: #166534;
}

.status.pending{
    background: #fef3c7;
    color: #92400e;
}

.actions-cell{
    text-align: right;
}

.view-btn{
    text-decoration: none;
    background: #ea7900;
    color: #ffffff;
    border-radius: 12px;
    padding: .6rem .9rem;
    font-size: .85rem;
    font-weight: 800;
}

.view-btn:hover{
    background: #c76000;
    color: #ffffff;
}

.empty-cell{
    text-align: center;
    color: #9ca3af;
    padding: 2rem !important;
}

@media(max-width: 900px){
    .stats-grid{
        grid-template-columns: 1fr;
    }
}
</style>
</x-filament::page>
