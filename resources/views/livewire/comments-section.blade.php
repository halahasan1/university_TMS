@php
    // لاحقاً إذا احتجتِ صاحب الخبر، خليه متاح:
    $postOwner = $news->user;

    // تأكّدي إن التعليقات محمّلة مع user.profile لتفادي N+1
    $comments = $comments ?? $news->comments()->with('user.profile')->latest()->get();

    // دالة صغيرة تجيب صورة المستخدم (بروفايل أو ui-avatars)
    $avatar = function ($u) {
        $path = $u?->profile?->image_path ?? null;
        return $path
            ? asset('storage/' . $path)
            : 'https://ui-avatars.com/api/?background=F59E0B&color=fff&name=' . urlencode($u?->name ?? 'User');
    };
@endphp

<div class="border-t border-gray-200 pt-4">
    <div class="space-y-4">
        @foreach($comments as $comment)
            <div class="flex gap-3">
                {{-- صورة صاحب التعليق (مش صاحب البوست) --}}
                <img
                    src="{{ $avatar($comment->user) }}"
                    alt="{{ $comment->user?->name }}"
                    class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200"
                >
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-lg p-3">
                        <div class="font-semibold text-sm">{{ $comment->user?->name }}</div>
                        <p class="text-gray-800 text-sm mt-1">{{ $comment->body }}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                        {{ $comment->created_at->diffForHumans() }}
                        <livewire:comment-like-button :comment="$comment" :wire:key="'like-'.$comment->id" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @auth
        @php $me = auth()->user(); @endphp
        <div class="mt-4 flex gap-2">
            {{-- صورة المستخدم الحالي عند فورم التعليق --}}
            <img
                src="{{ $avatar($me) }}"
                alt="{{ $me?->name }}"
                class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200"
            >
            <div class="flex-1">
                <form wire:submit.prevent="addComment" class="flex items-center gap-2">
                    <textarea wire:model="body"
                              rows="1"
                              class="flex-1 resize-none border border-gray-300 rounded-lg p-2 text-sm focus:ring-0 focus:border-gray-400"
                              placeholder="add comment..."
                              style="min-height: 40px; max-height: 80px;"></textarea>

                    <button type="submit" class="send-button" title="Send">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor"
                             viewBox="0 0 24 24"
                             class="send-icon">
                            <path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2 .01 7z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endauth

    <style>
        .send-button {
            padding: 8px;
            border-radius: 9999px;
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .send-icon {
            width: 20px;
            height: 20px;
            transition: transform 0.2s ease, color 0.2s ease;
        }
        .send-button:hover .send-icon {
            transform: scale(1.3);
            color: #2563eb;
        }
    </style>
</div>
