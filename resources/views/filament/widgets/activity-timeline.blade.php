<x-filament::section>
    <x-slot name="heading">{{ static::$heading }}</x-slot>
    <div class="space-y-3">
        @forelse($items as $row)
            <div class="flex items-start gap-3">
                <div class="h-2.5 w-2.5 rounded-full bg-primary-500 mt-1.5"></div>
                <div>
                    <div class="text-sm">{{ $row['desc'] }}</div>
                    <div class="text-xs opacity-70">
                        {{ \Illuminate\Support\Carbon::parse($row['time'])->diffForHumans() }}
                        • {{ $row['user'] ?? '—' }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-sm opacity-60">No recent activity.</div>
        @endforelse
    </div>
</x-filament::section>
