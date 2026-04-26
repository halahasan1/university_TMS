<x-filament::page>
@php
    $tasks = $this->tasks;

    $groups = [
        'pending' => 'To Do',
        'in_progress' => 'In Progress',
        'in_review' => 'In Review',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    $totalTasks = $tasks->count();
    $completedCount = $tasks->where('status', 'completed')->count();
    $inProgressCount = $tasks->where('status', 'in_progress')->count();
    $reviewCount = $tasks->where('status', 'in_review')->count();

    $newTasksCount = $tasks->filter(fn ($task) => $task->created_at && $task->created_at->gt(now()->subDays(3)))->count();
    $dueSoonCount = $tasks->filter(fn ($task) =>
        $task->due_date &&
        $task->status !== 'completed' &&
        $task->status !== 'cancelled' &&
        $task->due_date->between(now(), now()->copy()->addDays(3))
    )->count();
@endphp

<div class="mytasks-wrap">
    {{-- Header --}}
    <section class="soft-card page-hero">
        <div class="hero-text">
            <div class="eyebrow">Task workspace</div>
            <h1>My Tasks</h1>
            <p>Track your assigned work, open task details when needed, and move each task through its progress stages.</p>
        </div>
    </section>

    {{-- Summary cards --}}
    <section class="summary-grid">
        <div class="soft-card summary-card">
            <div class="summary-label">Total assigned</div>
            <div class="summary-value">{{ $totalTasks }}</div>
            <div class="summary-sub">All tasks assigned to you</div>
        </div>

        <div class="soft-card summary-card">
            <div class="summary-label">In progress</div>
            <div class="summary-value">{{ $inProgressCount }}</div>
            <div class="summary-sub">Tasks currently being worked on</div>
        </div>

        <div class="soft-card summary-card">
            <div class="summary-label">In review</div>
            <div class="summary-value">{{ $reviewCount }}</div>
            <div class="summary-sub">Waiting for completion or final review</div>
        </div>

        <div class="soft-card summary-card">
            <div class="summary-label">Completed</div>
            <div class="summary-value">{{ $completedCount }}</div>
            <div class="summary-sub">Finished tasks</div>
        </div>
    </section>

    {{-- Notice cards --}}
    <section class="notice-grid">
        <div class="soft-card notice-card notice-blue">
            <div class="notice-icon">✦</div>
            <div>
                <div class="notice-title">Newly assigned tasks</div>
                <div class="notice-text">
                    You have <strong>{{ $newTasksCount }}</strong> task{{ $newTasksCount === 1 ? '' : 's' }} added recently.
                </div>
            </div>
        </div>

        <div class="soft-card notice-card notice-amber">
            <div class="notice-icon">⏳</div>
            <div>
                <div class="notice-title">Due soon</div>
                <div class="notice-text">
                    <strong>{{ $dueSoonCount }}</strong> task{{ $dueSoonCount === 1 ? '' : 's' }} due within the next 3 days.
                </div>
            </div>
        </div>
    </section>

    {{-- Accordion sections --}}
    <section class="accordion-stack">
        @foreach ($groups as $key => $label)
            @php
                $groupTasks = $tasks->where('status', $key)->values();

                $sectionStyle = match($key) {
                    'pending' => 'section-gray',
                    'in_progress' => 'section-blue',
                    'in_review' => 'section-yellow',
                    'completed' => 'section-green',
                    'cancelled' => 'section-red',
                    default => 'section-gray',
                };
            @endphp

            <div
                x-data="{ open: {{ in_array($key, ['pending', 'in_progress']) ? 'true' : 'false' }} }"
                class="soft-card accordion-card {{ $sectionStyle }}"
            >
                <button type="button" class="accordion-head" @click="open = !open">
                    <div class="accordion-head-left">
                        <div class="section-badge">{{ $label }}</div>
                        <div class="section-count">{{ $groupTasks->count() }} task{{ $groupTasks->count() === 1 ? '' : 's' }}</div>
                    </div>

                    <div class="accordion-head-right">
                        <span class="mini-status-pill">
                            {{ $groupTasks->count() }}
                        </span>

                        <svg class="accordion-arrow" :class="{ 'rotate': open }" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-collapse class="accordion-body">
                    @if($groupTasks->count())
                        <div class="tasks-grid">
                            @foreach ($groupTasks as $task)
                                @php
                                    $subtaskCount = $task->subtasks->count();
                                    $doneCount = $task->subtasks->where('done', true)->count();
                                    $progress = $subtaskCount ? round(($doneCount / $subtaskCount) * 100) : ($task->status === 'completed' ? 100 : 0);

                                    $priorityClass = match($task->priority) {
                                        'high' => 'badge-red',
                                        'medium' => 'badge-yellow',
                                        'low' => 'badge-green',
                                        default => 'badge-gray',
                                    };

                                    $statusClass = match($task->status) {
                                        'pending' => 'badge-gray',
                                        'in_progress' => 'badge-blue',
                                        'in_review' => 'badge-yellow',
                                        'completed' => 'badge-green',
                                        'cancelled' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                @endphp

                                <div class="task-modern-card">
                                    <div class="task-modern-top">
                                        <div class="task-modern-main">
                                            <h3 class="task-modern-title">{{ $task->title }}</h3>

                                            <div class="task-badges">
                                                <span class="badge-soft {{ $priorityClass }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>

                                                <span class="badge-soft {{ $statusClass }}">
                                                    {{ str($task->status)->replace('_', ' ')->title() }}
                                                </span>
                                            </div>
                                        </div>

                                        <button
                                            wire:click="toggleTaskDone({{ $task->id }})"
                                            class="task-action-btn"
                                        >
                                            @switch($task->status)
                                                @case('pending')
                                                    Start
                                                    @break
                                                @case('in_progress')
                                                    Send to Review
                                                    @break
                                                @case('in_review')
                                                    Mark Complete
                                                    @break
                                                @case('completed')
                                                    Reopen
                                                    @break
                                                @default
                                                    Update
                                            @endswitch
                                        </button>
                                    </div>

                                    <div class="task-meta-row">
                                        <div class="task-meta-item">
                                            <span class="meta-label">Due</span>
                                            <span class="meta-value">{{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}</span>
                                        </div>

                                        <div class="task-meta-item">
                                            <span class="meta-label">Progress</span>
                                            <span class="meta-value">{{ $progress }}%</span>
                                        </div>
                                    </div>

                                    <div class="task-progress-track">
                                        <div class="task-progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>

                                    @if($subtaskCount)
                                        <div x-data="{ detailsOpen: false }" class="subtasks-wrap">
                                            <button type="button" class="subtasks-toggle" @click="detailsOpen = !detailsOpen">
                                                <span>{{ $doneCount }} / {{ $subtaskCount }} subtasks completed</span>
                                                <svg class="sub-arrow" :class="{ 'rotate': detailsOpen }" width="18" height="18" viewBox="0 0 20 20" fill="none">
                                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>

                                            <div x-show="detailsOpen" x-collapse class="subtasks-list">
                                                @foreach ($task->subtasks as $sub)
                                                    <label class="subtask-item">
                                                        <div class="subtask-left">
                                                            <input
                                                                type="checkbox"
                                                                wire:click="toggleSubtask({{ $sub->id }})"
                                                                @checked($sub->done)
                                                                class="subtask-checkbox"
                                                            >
                                                            <span class="subtask-title {{ $sub->done ? 'done' : '' }}">
                                                                {{ $sub->title }}
                                                            </span>
                                                        </div>

                                                        <span class="subtask-time">
                                                            {{ $sub->updated_at->diffForHumans() }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="task-bottom-row">
                                        <a href="{{ route('filament.adminPanel.resources.tasks.view', $task) }}" class="review-link-modern">
                                            <x-heroicon-m-sparkles class="w-4 h-4" />
                                            <span>Open details</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-section-box">
                            No tasks in {{ strtolower($label) }}.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </section>
</div>

<style>
.mytasks-wrap{
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
    grid-template-columns: repeat(4, minmax(0, 1fr));
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

.notice-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.notice-card{
    padding: 1rem 1.1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
}

.notice-blue{
    background: linear-gradient(180deg, #f8fbff 0%, #f3f8ff 100%);
}

.notice-amber{
    background: linear-gradient(180deg, #fffdf7 0%, #fff9eb 100%);
}

.notice-icon{
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    font-size: 1rem;
    flex-shrink: 0;
}

.notice-title{
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
}

.notice-text{
    margin-top: 0.25rem;
    color: #6b7280;
    font-size: 0.92rem;
    line-height: 1.6;
}

.accordion-stack{
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.accordion-card{
    overflow: hidden;
}

.section-gray{ border-top: 3px solid #d1d5db; }
.section-blue{ border-top: 3px solid #93c5fd; }
.section-yellow{ border-top: 3px solid #fcd34d; }
.section-green{ border-top: 3px solid #86efac; }
.section-red{ border-top: 3px solid #fca5a5; }

.accordion-head{
    width: 100%;
    border: 0;
    background: transparent;
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    cursor: pointer;
}

.accordion-head-left{
    display: flex;
    align-items: center;
    gap: 0.8rem;
    flex-wrap: wrap;
}

.section-badge{
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

.section-count{
    font-size: 0.88rem;
    color: #6b7280;
}

.accordion-head-right{
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mini-status-pill{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 30px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #374151;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 0 0.55rem;
}

.accordion-arrow,
.sub-arrow{
    color: #6b7280;
    transition: transform 0.2s ease;
}

.accordion-arrow.rotate,
.sub-arrow.rotate{
    transform: rotate(180deg);
}

.accordion-body{
    padding: 0 1.25rem 1.25rem;
}

.tasks-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.task-modern-card{
    border: 1px solid #e9eef5;
    background: #fcfdff;
    border-radius: 18px;
    padding: 1rem;
    transition: 0.2s ease;
}

.task-modern-card:hover{
    transform: translateY(-1px);
    border-color: #d8e3ef;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
}

.task-modern-top{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
}

.task-modern-main{
    min-width: 0;
}

.task-modern-title{
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.45;
    margin: 0;
}

.task-badges{
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
.badge-green{ background: #dcfce7; color: #166534; }
.badge-red{ background: #fee2e2; color: #991b1b; }

.task-action-btn{
    border: 0;
    background: #eef4ff;
    color: #3159d1;
    border-radius: 12px;
    padding: 0.6rem 0.8rem;
    font-size: 0.83rem;
    font-weight: 700;
    line-height: 1.3;
    cursor: pointer;
    transition: 0.2s ease;
    white-space: nowrap;
}

.task-action-btn:hover{
    background: #e4eeff;
}

.task-meta-row{
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.task-meta-item{
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

.task-progress-track{
    margin-top: 0.9rem;
    width: 100%;
    height: 8px;
    border-radius: 999px;
    background: #edf2f7;
    overflow: hidden;
}

.task-progress-fill{
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
}

.subtasks-wrap{
    margin-top: 1rem;
}

.subtasks-toggle{
    width: 100%;
    border: 0;
    background: #f8fafc;
    color: #475569;
    padding: 0.75rem 0.85rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 0.88rem;
    font-weight: 600;
}

.subtasks-list{
    margin-top: 0.7rem;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.subtask-item{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: center;
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    padding: 0.75rem 0.85rem;
}

.subtask-left{
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.subtask-checkbox{
    width: 16px;
    height: 16px;
    accent-color: #3b82f6;
    flex-shrink: 0;
}

.subtask-title{
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.5;
}

.subtask-title.done{
    text-decoration: line-through;
    color: #9ca3af;
}

.subtask-time{
    font-size: 0.78rem;
    color: #94a3b8;
    white-space: nowrap;
}

.task-bottom-row{
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}

.review-link-modern{
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: #d97706;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
}

.review-link-modern:hover{
    opacity: 0.85;
}

.empty-section-box{
    border: 1px dashed #dbe2ea;
    background: #f8fafc;
    color: #94a3b8;
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    font-size: 0.95rem;
}

/* Responsive */
@media (max-width: 1100px){
    .summary-grid{
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .tasks-grid{
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px){
    .summary-grid,
    .notice-grid{
        grid-template-columns: 1fr;
    }

    .task-modern-top,
    .subtask-item{
        flex-direction: column;
        align-items: flex-start;
    }

    .task-bottom-row{
        justify-content: flex-start;
    }

    .page-hero h1{
        font-size: 1.65rem;
    }
}

/* Dark mode */
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
html.dark .task-modern-title,
html.dark .section-badge,
.fi-dark .page-hero h1,
.fi-dark .task-modern-title,
.fi-dark .section-badge,
[data-theme="dark"] .page-hero h1,
[data-theme="dark"] .task-modern-title,
[data-theme="dark"] .section-badge{
    color: #f9fafb;
}

html.dark .page-hero p,
html.dark .summary-sub,
html.dark .notice-text,
html.dark .section-count,
html.dark .meta-value,
html.dark .subtask-title,
.fi-dark .page-hero p,
.fi-dark .summary-sub,
.fi-dark .notice-text,
.fi-dark .section-count,
.fi-dark .meta-value,
.fi-dark .subtask-title,
[data-theme="dark"] .page-hero p,
[data-theme="dark"] .summary-sub,
[data-theme="dark"] .notice-text,
[data-theme="dark"] .section-count,
[data-theme="dark"] .meta-value,
[data-theme="dark"] .subtask-title{
    color: #d1d5db;
}

html.dark .summary-label,
html.dark .meta-label,
html.dark .subtask-time,
.fi-dark .summary-label,
.fi-dark .meta-label,
.fi-dark .subtask-time,
[data-theme="dark"] .summary-label,
[data-theme="dark"] .meta-label,
[data-theme="dark"] .subtask-time{
    color: #9ca3af;
}

html.dark .task-modern-card,
.fi-dark .task-modern-card,
[data-theme="dark"] .task-modern-card{
    background: #0f172a;
    border-color: #1e293b;
}

html.dark .subtasks-toggle,
.fi-dark .subtasks-toggle,
[data-theme="dark"] .subtasks-toggle{
    background: #0f172a;
    color: #cbd5e1;
}

html.dark .subtask-item,
.fi-dark .subtask-item,
[data-theme="dark"] .subtask-item{
    background: #111827;
    border-color: #1f2937;
}

html.dark .task-progress-track,
.fi-dark .task-progress-track,
[data-theme="dark"] .task-progress-track{
    background: #1f2937;
}

html.dark .mini-status-pill,
.fi-dark .mini-status-pill,
[data-theme="dark"] .mini-status-pill{
    background: #1f2937;
    color: #e5e7eb;
}

html.dark .notice-blue,
.fi-dark .notice-blue,
[data-theme="dark"] .notice-blue{
    background: #0f172a;
}

html.dark .notice-amber,
.fi-dark .notice-amber,
[data-theme="dark"] .notice-amber{
    background: #1a160b;
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
