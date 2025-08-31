<x-filament::page>
@php
    $total    = $record->subtasks->count();
    $done     = $record->subtasks->where('done', true)->count();
    $progress = $total ? round(($done / $total) * 100) : ($record->status === 'completed' ? 100 : 0);

    $priorityColors = [
        'low'     => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'medium'  => 'bg-amber-50  text-amber-700  ring-amber-200',
        'high'    => 'bg-rose-50   text-rose-700   ring-rose-200',
    ];
    $statusColors = [
        'pending'     => 'bg-gray-100  text-gray-800  ring-gray-200',
        'in_progress' => 'bg-sky-50    text-sky-700   ring-sky-200',
        'completed'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    ];

    $now  = now();
    $diff = $now->diff($record->due_date, false);
    if ($diff->invert) {
        $dueText = 'Overdue: '.($diff->d).'d '.($diff->h).'h';
        $dueChip = 'bg-rose-50 text-rose-700 ring-rose-200';
    } elseif ($diff->days === 0 && $diff->h === 0) {
        $dueText = 'Due now';
        $dueChip = 'bg-amber-50 text-amber-700 ring-amber-200';
    } else {
        $dueText = 'Due in: '.$diff->d.'d '.$diff->h.'h';
        $dueChip = 'bg-indigo-50 text-indigo-700 ring-indigo-200';
    }

    $steps       = ['pending','in_progress','completed'];
    $currentStep = array_search($record->status, $steps, true) ?: 0;
@endphp

<div class="max-w-6xl mx-auto space-y-8">

    {{-- Header / Title --}}
    <div class="card card--elev p-6">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <h2 class="text-2xl font-semibold text-gray-900 leading-snug">
                    {{ $record->title }}
                </h2>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip ring chip--soft {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800 ring-gray-200' }}">
                        Status: {{ str($record->status)->replace('_',' ')->title() }}
                    </span>
                    <span class="chip ring chip--soft {{ $priorityColors[$record->priority] ?? 'bg-gray-100 text-gray-800 ring-gray-200' }}">
                        Priority: {{ ucfirst($record->priority) }}
                    </span>
                    <span class="chip ring chip--soft {{ $dueChip }}">
                        {{ $dueText }}
                    </span>
                </div>
            </div>

            @if($record->description)
                <p class="text-gray-700 leading-relaxed">{{ $record->description }}</p>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="mt-6">
            <div class="flex items-center gap-3">
                @foreach ($steps as $i => $step)
                    <div class="flex items-center gap-3">
                        <div class="step-dot {{ $i <= $currentStep ? 'step-dot--active' : '' }}">
                            {{ $i+1 }}
                        </div>
                        <div class="text-xs font-medium {{ $i <= $currentStep ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ str($step)->replace('_',' ')->title() }}
                        </div>
                    </div>
                    @if ($i < count($steps)-1)
                        <div class="step-line {{ $i < $currentStep ? 'step-line--active' : '' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Meta / KPIs --}}
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="card card--elev p-5">
            <div class="text-sm text-gray-600">Due date</div>
            <div class="mt-1 text-base font-semibold text-gray-900">
                {{ $record->due_date->format('M d, Y H:i') }}
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Created: {{ $record->created_at->format('M d, Y H:i') }}
            </div>

            @if ($record->file_path)
                <a href="{{ asset('storage/'.$record->file_path) }}" class="mt-4 inline-flex items-center gap-2 text-amber-700 font-medium hover:opacity-80 transition">
                    <x-heroicon-o-arrow-down-circle class="w-5 h-5" />
                    Download attachment
                </a>
            @else
                <div class="mt-4 text-sm text-gray-500">No attachments</div>
            @endif
        </div>

        <div class="card card--elev p-5">
            <div class="grid grid-cols-3 gap-4">
                <div class="kpi kpi--elev">
                    <div class="text-xs text-gray-500">Subtasks</div>
                    <div class="text-xl font-semibold text-gray-900">{{ $total }}</div>
                </div>
                <div class="kpi kpi--elev">
                    <div class="text-xs text-gray-500">Done</div>
                    <div class="text-xl font-semibold text-emerald-600">{{ $done }}</div>
                </div>
                <div class="kpi kpi--elev">
                    <div class="text-xs text-gray-500">Progress</div>
                    <div class="text-xl font-semibold text-amber-600">{{ $progress }}%</div>
                </div>
            </div>

            <div class="mt-4 h-2 w-full progress-track rounded-full overflow-hidden">
                <div class="h-full progress-bar rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <div class="card card--elev p-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Assigned To</div>
                    <div class="flex items-center gap-3">
                        <img class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-avatar"
                             src="{{ optional($record->assignedTo->profile)->image_path ? asset('storage/'.$record->assignedTo->profile->image_path) : 'https://ui-avatars.com/api/?name='.urlencode(optional($record->assignedTo)->name) }}">
                        <div class="font-medium text-gray-900">{{ $record->assignedTo->name ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Created By</div>
                    <div class="flex items-center gap-3">
                        <img class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-avatar"
                             src="{{ optional($record->createdBy->profile)->image_path ? asset('storage/'.$record->createdBy->profile->image_path) : 'https://ui-avatars.com/api/?name='.urlencode(optional($record->createdBy)->name) }}">
                        <div class="font-medium text-gray-900">{{ $record->createdBy->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist --}}
    <div class="card card--elev p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Checklist</h3>
            @if ($total)
                <span class="text-sm text-gray-600">{{ $done }} / {{ $total }} done</span>
            @endif
        </div>

        @if ($total)
            <ul class="space-y-2">
                @foreach ($record->subtasks as $subtask)
                    <li class="check-item">
                        <div class="flex items-center gap-3">
                            @if (auth()->id() === (int) $record->assigned_to)
                                <button
                                    wire:click="toggleSubtask({{ $subtask->id }})"
                                    class="check-toggle"
                                >
                                    @if ($subtask->done)
                                        <x-heroicon-o-check class="h-4 w-4 text-emerald-600" />
                                    @endif
                                </button>
                            @else
                                <div class="check-toggle check-toggle--readonly">
                                    @if ($subtask->done)
                                        <x-heroicon-o-check class="h-4 w-4 text-emerald-600" />
                                    @endif
                                </div>
                            @endif

                            <span class="text-sm {{ $subtask->done ? 'line-through text-gray-500' : 'text-gray-800' }}">
                                {{ $subtask->title }}
                            </span>
                        </div>

                        <span class="text-xs text-gray-500">Updated {{ $subtask->updated_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                No checklist items
            </div>
        @endif
    </div>
</div>

{{-- Advanced styles (no Tailwind @apply needed) --}}
<style>
:root{
  --card-bg: linear-gradient(180deg,#ffffff 0%, #f8fafc 100%);
  --card-ring: rgba(148,163,184,.28); /* slate-300/28 */
  --shadow-1: 0 6px 18px -8px rgba(17,24,39,.20);
  --shadow-2: 0 18px 55px -20px rgba(17,24,39,.25);
  --shadow-3: 0 2px 0 0 rgba(255,255,255,.8) inset;
  --soft-grad: linear-gradient(135deg, #fff 0%, #f1f5f9 100%);
  --accent-amber: #f59e0b;
  --accent-emerald: #10b981;
  --accent-sky: #0ea5e9;
  --line-muted: #e5e7eb; /* gray-200 */
}

.card{
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--card-ring);
  box-shadow: var(--shadow-1), var(--shadow-2);
  transition: box-shadow .25s ease, transform .25s ease, border-color .25s ease;
}
.card--elev:hover{
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -10px rgba(17,24,39,.25), 0 40px 80px -20px rgba(17,24,39,.28);
  border-color: rgba(148,163,184,.45);
}

.chip{
  display:inline-flex;align-items:center;gap:.375rem;
  padding:.25rem .625rem;border-radius:9999px;font-size:.75rem;font-weight:600;
  box-shadow: var(--shadow-3);
  backdrop-filter: saturate(1.1) blur(4px);
}

.kpi{
  text-align:center;border-radius:12px;padding:.75rem;
  background: var(--soft-grad);
  border: 1px solid var(--card-ring);
  box-shadow: var(--shadow-1);
}

.progress-track{
  background:#e5e7eb;
}
.progress-bar{
  background: linear-gradient(90deg, var(--accent-amber) 0%, var(--accent-emerald) 100%);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.6);
}

.step-dot{
  height:2rem;width:2rem;border-radius:9999px;
  display:flex;align-items:center;justify-content:center;
  font-size:.75rem;font-weight:700;color:#475569; /* slate-600 */
  background:#e5e7eb; /* gray-200 */
  border:1px solid #e2e8f0; /* slate-200 */
  box-shadow: var(--shadow-3);
}
.step-dot--active{
  color:white;
  background: radial-gradient(120% 120% at 30% 20%, var(--accent-amber) 0%, #f97316 40%, #fb923c 100%);
  border-color: transparent;
  box-shadow: 0 8px 18px -8px rgba(249,115,22,.6), inset 0 0 0 1px rgba(255,255,255,.35);
}

.step-line{
  flex:1;height:2px;border-radius:9999px;background: var(--line-muted);
}
.step-line--active{
  background: linear-gradient(90deg, var(--accent-amber), var(--accent-emerald));
  box-shadow: 0 0 0 1px rgba(255,255,255,.25) inset;
}

.check-item{
  display:flex;align-items:center;justify-content:space-between;
  border-radius:14px;padding:.5rem .75rem;
  background:#ffffff;border:1px solid var(--card-ring);
  box-shadow: 0 8px 20px -14px rgba(15,23,42,.35);
  transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}
.check-item:hover{
  transform: translateY(-1px);
  box-shadow: 0 16px 35px -18px rgba(15,23,42,.40);
  border-color: rgba(148,163,184,.45);
}

.check-toggle{
  height:1.5rem;width:1.5rem;border-radius:.5rem;
  display:flex;align-items:center;justify-content:center;
  background:#fff;border:1px solid #cbd5e1; /* slate-300 */
  box-shadow: var(--shadow-3);
  transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}
.check-toggle:hover{ border-color:#94a3b8; } /* slate-400 */
.check-toggle:focus-visible{ outline:2px solid rgba(245,158,11,.55); outline-offset:2px; }
.check-toggle--readonly{ background:#f8fafc; border-color:#e2e8f0; }

.shadow-avatar{
  box-shadow: 0 10px 25px -10px rgba(15,23,42,.35);
  border-radius:9999px;
}


html.dark .card,
.fi-dark .card,
[data-theme="dark"] .card {
  background: linear-gradient(180deg,#0b1220,#0e1526);
  border-color: rgba(148,163,184,.20);
}

html.dark .kpi,
.fi-dark .kpi,
[data-theme="dark"] .kpi {
  background: linear-gradient(135deg,#0b1220,#0e1526);
  border-color: rgba(148,163,184,.20);
}

html.dark .check-item,
.fi-dark .check-item,
[data-theme="dark"] .check-item {
  background:#0b1220;
  border-color: rgba(148,163,184,.20);
}

html.dark .chip,
.fi-dark .chip,
[data-theme="dark"] .chip {
  background: rgba(255,255,255,.04);
}

html.dark .step-dot,
.fi-dark .step-dot,
[data-theme="dark"] .step-dot {
  background:#1f2937;
  border-color:#0b1220;
  color:#cbd5e1;
}

html.dark .progress-track,
.fi-dark .progress-track,
[data-theme="dark"] .progress-track {
  background:#1f2937;
}

</style>
</x-filament::page>
