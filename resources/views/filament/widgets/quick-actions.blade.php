<x-filament::section>
    <x-slot name="heading">{{ static::$heading }}</x-slot>

    <div class="flex flex-wrap gap-2">
        <x-filament::button tag="a" href="{{ \App\Filament\Resources\TaskResource::getUrl('create') }}">
            + New Task
        </x-filament::button>

        <x-filament::button tag="a" href="{{ \App\Filament\Resources\NewsResource::getUrl('create') }}">
            + New News
        </x-filament::button>

        <x-filament::button tag="a" color="warning" href="{{ \App\Filament\Resources\TaskResource::getUrl() }}">
            Tasks
        </x-filament::button>

        <x-filament::button tag="a" color="info" href="{{ \App\Filament\Resources\NewsResource::getUrl() }}">
            News
        </x-filament::button>
    </div>
    <div class="mt-3 text-xs opacity-70">
    </div>
</x-filament::section>
