<x-filament::section>
    <x-slot name="heading">{{ $this->getHeading() }}</x-slot>

    <div class="grid grid-cols-7 gap-2">
        @foreach($days as $d)
            <div
                @class([
                    'rounded-xl border p-2 text-center',
                    'ring-2 ring-primary-500' => $d['is_today'],
                ])
            >
                <div class="text-xs opacity-70">{{ $d['date']->format('D') }}</div>
                <div class="text-sm font-semibold">{{ $d['date']->format('d') }}</div>

                <div class="mt-1 flex items-center justify-center gap-1 text-[11px]">
                    <x-filament::badge color="warning">T: {{ $d['tasks'] }}</x-filament::badge>
                    <x-filament::badge color="info">N: {{ $d['news'] }}</x-filiment::badge>
                </div>
            </div>
        @endforeach
    </div>
</x-filament::section>
