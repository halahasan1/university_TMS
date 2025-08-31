<div>
    <button wire:click="toggleLike" class="like-button {{ $liked ? 'liked' : '' }}">
        <svg class="heart-icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24"
         fill="currentColor">
        <path d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
    </svg>
</button>
<span class="text-sm text-gray-600">
    {{ $likeCount }}
</span>
<style>
    .like-button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        transition: transform 0.2s ease;
    }

    .heart-icon {
        width: 20px;
        height: 20px;
        color: #d1d5db;
        transition: color 0.3s ease, transform 0.2s ease;
    }

    .like-button:hover .heart-icon {
        transform: scale(1.2);
    }

    .like-button.liked .heart-icon {
        color: #ef4444;
    }
</style>

</div>
