<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ([
            'pending' => 'To Do',
            'in_progress' => 'In Progress',
            'completed' => 'Completed'
        ] as $key => $label)
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl shadow-md p-4 min-h-[400px] border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4 tracking-wide uppercase">{{ $label }}</h2>

                @foreach ($this->tasks->where('status', $key) as $task)
                @php
                $priorityClass = match ($task->priority) {
                    'high' => 'priority-high',
                    'medium' => 'priority-medium',
                    'low' => '',
                    default => '',
                };
                @endphp

            <div
                class="hover-card task-card
                    {{ $task->status === 'completed' ? 'task-completed' : $priorityClass }}"
                @if($task->status === 'completed') title="Task completed" @endif
                >
                        <div class="flex justify-between items-start">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                                {{ $task->title }}
                            </h3>

                            @if($task->status === 'pending')
                                <button wire:click="toggleTaskDone({{ $task->id }})"
                                        class="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded hover:bg-blue-200 transition">
                                    Start
                                </button>
                            @else
                                <input type="checkbox" wire:click="toggleTaskDone({{ $task->id }})"
                                       @checked($task->status === 'completed')
                                       class="mt-1">
                            @endif
                        </div>

                        {{-- Subtasks --}}
                        @if($task->subtasks->count())
                            <ul class="list-disc pl-5 text-sm mt-3 space-y-1">
                                @foreach ($task->subtasks as $sub)
                                    <li class="flex items-center space-x-2">
                                        <input type="checkbox"
                                               wire:click="toggleSubtask({{ $sub->id }})"
                                               @checked($sub->done)
                                               class="rounded border-gray-300 accent-primary-600 dark:accent-primary-400">
                                        <span class="{{ $sub->done ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $sub->title }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            @if($task->status === 'completed')
                                <div class="text-xs text-green-600 mt-2">
                                    ✅ All subtasks completed
                                </div>
                            @endif
                        @endif

                        <div class="mt-4 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                            <span>Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}</span>

                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium
                                {{ match($task->priority) {
                                    'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                } }}">
                                {{ ucfirst($task->priority ?? 'normal') }}
                            </span>
                        </div>

                        {{-- Review --}}
                        <div class="mt-3 text-right">
                            <x-filament::link icon="heroicon-m-sparkles" :href="route('filament.adminPanel.resources.tasks.view', $task)">
                                review
                            </x-filament::link>
                        </div>

                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .task-card {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            background-color: white;
            border: 1px solid #e5e7eb;
        }

        .dark .task-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        .task-completed {
            background-color: #ecfdf5 !important;
            border-color: #6ee7b7 !important;
        }

        .dark .task-completed {
            background-color: #066d7a !important;
            border-color: #2cf6ba !important;
        }

        .priority-high {
            background-color: #fef2f2;
            border-color: #fca5a5;
        }

        .dark .priority-high {
            background-color: #ed5353;
            border-color: #f87171;
        }

        .priority-medium {
            background-color: #eff6ff;
            border-color: #93c5fd;
        }

        .dark .priority-medium {
            background-color: #0e1d45;
            border-color: #00377a;
        }
    </style>

</x-filament::page>
