<x-filament::page>
    <x-filament::form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Save Changes
        </x-filament::button>
    </x-filament::form>
</x-filament::page>
