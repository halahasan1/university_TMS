<x-filament::page>
@php
    $total = $record->subtasks->count();
    $done = $record->subtasks->where('done', true)->count();
    $progress = $total ? round(($done / $total) * 100) : ($record->status === 'completed' ? 100 : 0);

    $priorityClasses = [
        'low' => 'badge-soft badge-green',
        'medium' => 'badge-soft badge-yellow',
        'high' => 'badge-soft badge-red',
    ];

    $statusClasses = [
        'pending' => 'badge-soft badge-gray',
        'in_progress' => 'badge-soft badge-blue',
        'in_review' => 'badge-soft badge-yellow',
        'completed' => 'badge-soft badge-green',
        'cancelled' => 'badge-soft badge-red',
    ];

    $now = now();

    if ($record->due_date) {
        $diff = $now->diff($record->due_date, false);

        if ($diff->invert) {
            $dueText = 'Overdue';
            $dueSubtext = $diff->d . 'd ' . $diff->h . 'h late';
            $dueClass = 'text-red-600';
        } elseif ($diff->days === 0 && $diff->h === 0) {
            $dueText = 'Due now';
            $dueSubtext = 'Take action soon';
            $dueClass = 'text-amber-600';
        } else {
            $dueText = 'Upcoming';
            $dueSubtext = $diff->d . 'd ' . $diff->h . 'h left';
            $dueClass = 'text-indigo-600';
        }
    } else {
        $dueText = 'No due date';
        $dueSubtext = 'Not specified';
        $dueClass = 'text-gray-500';
    }
@endphp

<div class="task-view-wrap">
    {{-- Header --}}
    <section class="soft-card hero-card">
        <div class="hero-top">
            <div class="hero-main">
                <div class="eyebrow">Task overview</div>
                <h1 class="task-title">{{ $record->title }}</h1>

                @if($record->description)
                    <p class="task-description">{{ $record->description }}</p>
                @else
                    <p class="task-description muted">No description provided for this task.</p>
                @endif
            </div>

            <div class="hero-badges">
                <span class="{{ $statusClasses[$record->status] ?? 'badge-soft badge-gray' }}">
                    {{ str($record->status)->replace('_', ' ')->title() }}
                </span>

                <span class="{{ $priorityClasses[$record->priority] ?? 'badge-soft badge-gray' }}">
                    {{ ucfirst($record->priority) }} priority
                </span>

                <span class="badge-soft badge-dark">
                    {{ $progress }}% progress
                </span>
            </div>
        </div>

        <div class="progress-block">
            <div class="progress-meta">
                <span>Checklist progress</span>
                <span>{{ $done }} / {{ $total }} completed</span>
            </div>
            <div class="progress-track-modern">
                <div class="progress-fill-modern" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </section>

    {{-- Info grid --}}
    <section class="info-grid">
        <div class="soft-card info-card">
            <div class="info-label">Due date</div>
            <div class="info-value">
                {{ $record->due_date ? $record->due_date->format('M d, Y • h:i A') : '—' }}
            </div>
            <div class="info-sub {{ $dueClass }}">{{ $dueText }}</div>
            <div class="info-meta">{{ $dueSubtext }}</div>
        </div>

        <div class="soft-card info-card">
            <div class="info-label">Assigned to</div>
            <div class="person-row">
                <img
                    class="avatar-modern"
                    src="{{ optional($record->assignedTo->profile)->image_path
                        ? asset('storage/' . $record->assignedTo->profile->image_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(optional($record->assignedTo)->name) }}"
                    alt="{{ $record->assignedTo->name ?? 'Assigned user' }}"
                >
                <div>
                    <div class="info-value small">{{ $record->assignedTo->name ?? '-' }}</div>
                    <div class="info-meta">Task assignee</div>
                </div>
            </div>
        </div>

        <div class="soft-card info-card">
            <div class="info-label">Created by</div>
            <div class="person-row">
                <img
                    class="avatar-modern"
                    src="{{ optional($record->createdBy->profile)->image_path
                        ? asset('storage/' . $record->createdBy->profile->image_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(optional($record->createdBy)->name) }}"
                    alt="{{ $record->createdBy->name ?? 'Creator' }}"
                >
                <div>
                    <div class="info-value small">{{ $record->createdBy->name ?? '-' }}</div>
                    <div class="info-meta">Task creator</div>
                </div>
            </div>
        </div>

        <div class="soft-card info-card">
            <div class="info-label">Details</div>
            <div class="meta-list">
                <div class="meta-row">
                    <span>Department</span>
                    <strong>{{ $record->department->name ?? '-' }}</strong>
                </div>

                <div class="meta-row">
                    <span>Created</span>
                    <strong>{{ $record->created_at->format('M d, Y') }}</strong>
                </div>

                @if($record->started_at)
                    <div class="meta-row">
                        <span>Started</span>
                        <strong>{{ $record->started_at->format('M d, Y') }}</strong>
                    </div>
                @endif

                @if($record->completed_at)
                    <div class="meta-row">
                        <span>Completed</span>
                        <strong>{{ $record->completed_at->format('M d, Y') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Attachment + checklist --}}
    <section class="content-grid">
        <div class="soft-card attachment-card">
            <div class="section-head">
                <h2>Attachment</h2>
            </div>

            @if ($record->file_path)
                <a href="{{ asset('storage/' . $record->file_path) }}" class="attachment-link">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    <span>Download attached file</span>
                </a>
            @else
                <div class="empty-box">
                    No file attached to this task.
                </div>
            @endif
        </div>

        <div class="soft-card checklist-card">
            <div class="section-head">
                <h2>Checklist</h2>
                <span class="section-counter">{{ $done }} / {{ $total }}</span>
            </div>

            @if ($total)
                <div class="checklist-list">
                    @foreach ($record->subtasks as $subtask)
                        <div class="check-item-modern">
                            <div class="check-left">
                                @if (auth()->id() === (int) $record->assigned_to)
                                    <button
                                        wire:click="toggleSubtask({{ $subtask->id }})"
                                        class="check-toggle-modern"
                                    >
                                        @if ($subtask->done)
                                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                                        @endif
                                    </button>
                                @else
                                    <div class="check-toggle-modern check-toggle-modern--readonly">
                                        @if ($subtask->done)
                                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                                        @endif
                                    </div>
                                @endif

                                <div>
                                    <div class="check-title {{ $subtask->done ? 'done' : '' }}">
                                        {{ $subtask->title }}
                                    </div>
                                    <div class="check-meta">
                                        Updated {{ $subtask->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($subtask->done)
                                    <span class="mini-badge mini-green">Done</span>
                                @else
                                    <span class="mini-badge mini-gray">Pending</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    No checklist items yet.
                </div>
            @endif
        </div>
    </section>
</div>

<style>
.task-view-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 0.25rem 0 1.5rem;
}

.soft-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
}

.hero-card{
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    background:
        radial-gradient(circle at top right, rgba(251, 191, 36, 0.10), transparent 24%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 22%),
        #ffffff;
}

.hero-top{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.hero-main{
    flex: 1 1 650px;
}

.eyebrow{
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.5rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.task-title{
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
    margin: 0;
}

.task-description{
    margin-top: 0.85rem;
    font-size: 1rem;
    line-height: 1.8;
    color: #4b5563;
    max-width: 800px;
}

.task-description.muted{
    color: #9ca3af;
}

.hero-badges{
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-content: flex-start;
}

.badge-soft{
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 0.45rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-gray{
    background: #f3f4f6;
    color: #374151;
}

.badge-blue{
    background: #e0f2fe;
    color: #075985;
}

.badge-yellow{
    background: #fef3c7;
    color: #92400e;
}

.badge-green{
    background: #dcfce7;
    color: #166534;
}

.badge-red{
    background: #fee2e2;
    color: #991b1b;
}

.badge-dark{
    background: #eef2ff;
    color: #4338ca;
}

.progress-block{
    margin-top: 1.4rem;
}

.progress-meta{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 0.55rem;
}

.progress-track-modern{
    width: 100%;
    height: 10px;
    border-radius: 999px;
    background: #eef2f7;
    overflow: hidden;
}

.progress-fill-modern{
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
}

.info-grid{
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.info-card{
    padding: 1.2rem;
    min-height: 170px;
}

.info-label{
    font-size: 0.82rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 0.8rem;
}

.info-value{
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.5;
}

.info-value.small{
    font-size: 1rem;
}

.info-sub{
    margin-top: 0.65rem;
    font-size: 0.92rem;
    font-weight: 600;
}

.info-meta{
    margin-top: 0.3rem;
    color: #9ca3af;
    font-size: 0.87rem;
}

.person-row{
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.avatar-modern{
    width: 46px;
    height: 46px;
    border-radius: 999px;
    object-fit: cover;
    border: 3px solid #f8fafc;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);
}

.meta-list{
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}

.meta-row{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    font-size: 0.92rem;
    color: #6b7280;
}

.meta-row strong{
    color: #111827;
    font-weight: 600;
    text-align: right;
}

.content-grid{
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 1rem;
}

.attachment-card,
.checklist-card{
    padding: 1.2rem;
}

.section-head{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-head h2{
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
}

.section-counter{
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
}

.attachment-link{
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
    padding: 0.8rem 1rem;
    border-radius: 14px;
    background: #eff6ff;
    transition: 0.2s ease;
}

.attachment-link:hover{
    background: #dbeafe;
}

.empty-box{
    border: 1px dashed #dbe2ea;
    background: #f8fafc;
    color: #94a3b8;
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    font-size: 0.95rem;
}

.checklist-list{
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.check-item-modern{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 0.9rem 1rem;
    border: 1px solid #edf2f7;
    background: #fcfdff;
    border-radius: 16px;
    transition: 0.2s ease;
}

.check-item-modern:hover{
    background: #f8fbff;
    border-color: #dbe7f3;
}

.check-left{
    display: flex;
    align-items: center;
    gap: 0.85rem;
    min-width: 0;
}

.check-toggle-modern{
    width: 2rem;
    height: 2rem;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #d6dee8;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: 0.2s ease;
}

.check-toggle-modern:hover{
    border-color: #93c5fd;
    background: #f8fbff;
}

.check-toggle-modern--readonly{
    background: #f9fafb;
}

.check-title{
    font-size: 0.97rem;
    color: #111827;
    font-weight: 500;
    line-height: 1.5;
}

.check-title.done{
    color: #9ca3af;
    text-decoration: line-through;
}

.check-meta{
    font-size: 0.82rem;
    color: #9ca3af;
    margin-top: 0.2rem;
}

.mini-badge{
    display: inline-flex;
    align-items: center;
    padding: 0.32rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.mini-green{
    background: #dcfce7;
    color: #166534;
}

.mini-gray{
    background: #f3f4f6;
    color: #4b5563;
}

@media (max-width: 1100px){
    .info-grid{
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .content-grid{
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px){
    .info-grid{
        grid-template-columns: 1fr;
    }

    .task-title{
        font-size: 1.55rem;
    }

    .hero-card,
    .info-card,
    .attachment-card,
    .checklist-card{
        padding: 1rem;
    }

    .check-item-modern{
        flex-direction: column;
        align-items: flex-start;
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

html.dark .hero-card,
.fi-dark .hero-card,
[data-theme="dark"] .hero-card{
    background:
        radial-gradient(circle at top right, rgba(251, 191, 36, 0.08), transparent 24%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 22%),
        #111827;
}

html.dark .task-title,
html.dark .section-head h2,
html.dark .info-value,
html.dark .meta-row strong,
html.dark .check-title,
.fi-dark .task-title,
.fi-dark .section-head h2,
.fi-dark .info-value,
.fi-dark .meta-row strong,
.fi-dark .check-title,
[data-theme="dark"] .task-title,
[data-theme="dark"] .section-head h2,
[data-theme="dark"] .info-value,
[data-theme="dark"] .meta-row strong,
[data-theme="dark"] .check-title{
    color: #f9fafb;
}

html.dark .task-description,
html.dark .meta-row,
html.dark .progress-meta,
.fi-dark .task-description,
.fi-dark .meta-row,
.fi-dark .progress-meta,
[data-theme="dark"] .task-description,
[data-theme="dark"] .meta-row,
[data-theme="dark"] .progress-meta{
    color: #d1d5db;
}

html.dark .info-label,
html.dark .info-meta,
html.dark .check-meta,
.fi-dark .info-label,
.fi-dark .info-meta,
.fi-dark .check-meta,
[data-theme="dark"] .info-label,
[data-theme="dark"] .info-meta,
[data-theme="dark"] .check-meta{
    color: #9ca3af;
}

html.dark .progress-track-modern,
.fi-dark .progress-track-modern,
[data-theme="dark"] .progress-track-modern{
    background: #1f2937;
}

html.dark .check-item-modern,
.fi-dark .check-item-modern,
[data-theme="dark"] .check-item-modern{
    background: #0f172a;
    border-color: #1e293b;
}

html.dark .check-item-modern:hover,
.fi-dark .check-item-modern:hover,
[data-theme="dark"] .check-item-modern:hover{
    background: #111827;
}

html.dark .check-toggle-modern,
.fi-dark .check-toggle-modern,
[data-theme="dark"] .check-toggle-modern{
    background: #111827;
    border-color: #334155;
}

html.dark .empty-box,
.fi-dark .empty-box,
[data-theme="dark"] .empty-box{
    background: #0f172a;
    border-color: #1f2937;
    color: #94a3b8;
}
</style>
</x-filament::page>
