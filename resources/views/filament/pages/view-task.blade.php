<x-filament::page>
    <div
        x-data="{ showCelebration: false }"
        x-init="() => {
            const allDone = {{ $record->subtasks->count() > 0 && $record->subtasks->where('done', false)->count() === 0 ? 'true' : 'false' }};
            if (allDone) {
                showCelebration = true;
                setTimeout(() => showCelebration = false, 5000);
            }
        }"
        class="relative space-y-6"
    >
        <!-- 🎉 Lottie Confetti -->
        <template x-if="showCelebration">
            <div class="absolute top-0 left-0 w-full h-full z-50 pointer-events-none">
                <lottie-player
                    src="https://lottie.host/58fd0e65-f798-4402-a865-114cfbdc117e/yAosz0htac.json"
                    background="transparent"
                    speed="1"
                    autoplay
                    style="width: 100%; height: 100%">
                </lottie-player>
            </div>
        </template>

        <!-- ✅ Script for Lottie -->
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

        <!-- 🧾 معلومات التاسك -->
        <div class="bg-white p-6 rounded-lg shadow-md relative z-10 hover:shadow-2xl transition duration-300">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 hover:text-blue-600 transition">📋 Task: {{ $record->title }}</h2>

            <div class="grid sm:grid-cols-2 gap-4 text-sm text-gray-700">
                <div>
                    <p><strong>Description:</strong> {{ $record->description }}</p>
                    <p><strong>Priority:</strong> {{ ucfirst($record->priority) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($record->status) }}</p>
                    <p><strong>Due Date:</strong> {{ $record->due_date->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <p><strong>Assigned To:</strong> {{ $record->assignedTo->name ?? '-' }}</p>
                    <p><strong>Created By:</strong> {{ $record->createdBy->name ?? '-' }}</p>
                    @if($record->file_path)
                        <p><strong>Attachment:</strong>
                            <a href="{{ asset('storage/' . $record->file_path) }}" class="text-blue-500 underline hover:text-blue-700 transition">
                                Download File
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ✅ قسم Subtasks --}}
        @if ($record->subtasks && $record->subtasks->count())
            <div class="bg-white p-6 rounded-lg shadow-md relative z-10 hover:shadow-2xl transition duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 hover:text-green-600 transition">Sub Tasks</h3>

                <ul class="space-y-3">
                    @foreach ($record->subtasks as $subtask)
                        <li class="flex items-center gap-3 group transition">
                            @if (auth()->id() === $record->assigned_to)
                                <button wire:click="toggleSubtask({{ $subtask->id }})"
                                    class="flex items-center gap-2 hover:scale-105 transition">
                                    <input type="checkbox" {{ $subtask->done ? 'checked' : '' }} class="h-5 w-5 accent-green-600">
                                    <span class="{{ $subtask->done ? 'line-through text-gray-500' : '' }}">
                                        {{ $subtask->title }}
                                    </span>
                                </button>
                            @else
                                <input type="checkbox" disabled {{ $subtask->done ? 'checked' : '' }} class="h-5 w-5 accent-gray-400">
                                <span class="{{ $subtask->done ? 'line-through text-gray-500' : '' }}">
                                    {{ $subtask->title }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-white p-4 rounded shadow text-gray-500 italic relative z-10">
                No checklist items
            </div>
        @endif
    </div>

    <style>
        button:hover span {
            color: #10b981; /* Tailwind emerald-500 */
        }

        button:hover input {
            transform: scale(1.1);
        }

        button:focus {
            outline: none;
        }
    </style>
</x-filament::page>
