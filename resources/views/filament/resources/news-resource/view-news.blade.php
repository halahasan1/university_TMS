<x-filament::page>
    <div class="max-w-3xl mx-auto">
        <!-- post -->
        @include('filament.resources.news-resource.card', ['record' => $news])

        <!-- comments -->
        <div class="mt-6">
            <livewire:comments-section :news="$news" />
        </div>
    </div>
</x-filament::page>
