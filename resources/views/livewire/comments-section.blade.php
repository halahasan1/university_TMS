<div class="border-t border-gray-200 pt-4">
    <div class="space-y-4">
        @foreach($comments as $comment)
            <div class="flex gap-3">
                <img src="{{ $comment->user->profile_photo_path ? asset('storage/'.$comment->user->profile_photo_path) : asset('default-profile.png') }}"
                     class="w-8 h-8 rounded-full object-cover">
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-lg p-3">
                        <div class="font-semibold text-sm">{{ $comment->user->name }}</div>
                        <p class="text-gray-800 text-sm mt-1">{{ $comment->content }}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @auth
    <div class="mt-4 flex gap-2">
        <img src="{{ auth()->user()->profile_photo_path ? asset('storage/'.auth()->user()->profile_photo_path) : asset('default-profile.png') }}"
             class="w-8 h-8 rounded-full object-cover">
        <div class="flex-1">
            <form wire:submit.prevent="addComment">
                <textarea wire:model="commentBody"
                          class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-0 focus:border-gray-400"
                          placeholder="add comment..."></textarea>
                <button type="submit"
                        class="mt-2 bg-blue-600 text-white px-4 py-1 rounded-lg text-sm hover:bg-blue-700">
                    send
                </button>
            </form>
        </div>
    </div>
    @endauth
</div>
