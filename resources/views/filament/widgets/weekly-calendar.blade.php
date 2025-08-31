<x-filament::section>
    <x-slot name="heading">{{ static::$heading }}</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
        @foreach($days as $d)
            <div class="rounded-2xl border bg-white dark:bg-gray-900 p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="font-semibold">{{ $d['date']->format('D') }}</div>
                    <div class="text-sm opacity-70">{{ $d['date']->format('M d') }}</div>
                </div>

                <div class="space-y-1">
                    @forelse($d['tasks'] as $t)
                        <div class="text-sm flex items-center gap-2">
                            <x-filament::badge :color="match($t->status){
                                'completed' => 'success',
                                'in_progress' => 'info',
                                default => 'warning',
                            }">
                                {{ str($t->status)->replace('_',' ')->headline() }}
                            </x-filament::badge>
                            <span class="truncate">{{ $t->title }}</span>
                            @if($t->assignedTo)
                                <span class="text-xs opacity-70">• {{ $t->assignedTo->name }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs opacity-60">No tasks</div>
                    @endforelse
                </div>

                <div class="border-t pt-2 space-y-1">
                    @forelse($d['news'] as $n)
                        <div class="text-sm">📰 <span class="truncate">{{ $n->title }}</span>
                            @if($n->user) <span class="text-xs opacity-70">• {{ $n->user->name }}</span> @endif
                        </div>
                    @empty
                        <div class="text-xs opacity-60">No news</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament::section>
