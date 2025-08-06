<div>
    <button wire:click="toggleLike" class="flex items-center gap-2 text-sm text-blue-600">
        @if ($news->isLikedBy(auth()->user()))
            <x-heroicon-s-hand-thumb-up class="w-5 h-5" />
            <span>Liked</span>
        @else
            <x-heroicon-o-hand-thumb-up class="w-5 h-5" />
            <span>Like</span>
        @endif
    </button>
</div>


