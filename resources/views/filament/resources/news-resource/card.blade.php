@php
    $user = $record->user;
    $profile = $user?->profile;
    $images = is_array($record->images) ? $record->images : [];
    $isOwner = auth()->id() === $user?->id;
    $likes = $record->likes()->with(['user.profile'])->get();
@endphp

<!-- Include Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<div x-data="{ showLikesModal: false, showImageModal: false, currentImageIndex: 0 }" class="w-full max-w-2xl mx-auto my-6">
    <div class="rounded-lg border border-gray-200 bg-white shadow-md overflow-hidden">

        {{-- Post Header --}}
        <div class="p-4 flex justify-between items-center border-b border-gray-100">
            <div class="flex items-center gap-3">
                <img
                    src="{{ $profile && $profile->image_path ? asset('storage/' . $profile->image_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name) }}"
                    alt="{{ $user?->name }}"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <div>
                    <div class="font-semibold text-gray-900 text-sm">{{ $user?->name }}</div>
                    <div class="text-xs text-gray-500">{{ $record->created_at->diffForHumans() }}</div>
                </div>
            </div>

            @if($isOwner)
            <div x-data="{ open: false }" class="relative z-30">
                <button @click="open = !open" class="text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     x-cloak>
                    <a href="{{ route('filament.adminPanel.resources.news.edit', ['record' => $record]) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                       </svg>
                       Edit Post
                    </a>
                </div>
            </div>
            @endif
        </div>

        {{-- Post Content --}}
        <div class="p-4">
            <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $record->title }}</h3>
            <div class="prose max-w-none text-gray-700 mb-4">
                {!! $record->body !!}
            </div>

            {{-- Image Slider --}}
            @if(count($images) > 0)
                <div x-data="{
                    initSwiper() {
                        const hasMultipleImages = {{ count($images) }} > 1;
                        new Swiper(this.$refs.mainSwiper, {
                            loop: hasMultipleImages,
                            spaceBetween: 10,
                            pagination: {
                                el: this.$refs.mainPagination,
                                clickable: true,
                            },
                            navigation: hasMultipleImages ? {
                                nextEl: this.$refs.mainNext,
                                prevEl: this.$refs.mainPrev,
                            } : false,
                        });
                    }
                }" x-init="initSwiper()" class="w-full">
                    <div class="swiper mySwiper aspect-square rounded-lg overflow-hidden" x-ref="mainSwiper">
                        <div class="swiper-wrapper">
                            @foreach($images as $index => $img)
                                <div class="swiper-slide">
                                    <img
                                        src="{{ asset('storage/' . $img) }}"
                                        class="w-full h-full object-cover cursor-pointer"
                                        @click="showImageModal = true; currentImageIndex = {{ $index }}"
                                    />
                                </div>
                            @endforeach
                        </div>
                        @if(count($images) > 1)
                            <div class="swiper-button-next" x-ref="mainNext"></div>
                            <div class="swiper-button-prev" x-ref="mainPrev"></div>
                            <div class="swiper-pagination" x-ref="mainPagination"></div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Post Footer --}}
        <div class="px-4 py-2 border-t border-gray-100">
            <div class="flex items-center justify-between text-gray-600 text-sm">
                <div class="flex flex-col">
                    <livewire:news-like-button :news="$record" />

                    {{-- Likes count and modal trigger --}}
                    @if ($likes->count())
                        <button @click="showLikesModal = true" class="mt-2 text-blue-600 text-sm hover:underline w-fit">
                            {{ $likes->count() }} liked this post
                        </button>
                    @endif
                </div>

                @if (Route::has('filament.adminPanel.resources.news.view'))
                    <a href="{{ route('filament.adminPanel.resources.news.view', $record) }}"
                        class="text-gray-500 hover:text-gray-700">
                        {{ $record->comments->count() }} comments
                    </a>
                @else
                    <a href="{{ url('/adminPanel/news/' . $record->id) }}"
                        class="text-gray-500 hover:text-gray-700">
                        {{ $record->comments->count() }} comments
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Likes Modal --}}
    <div x-show="showLikesModal" x-cloak
         x-transition.opacity
         @click.outside="showLikesModal = false"
         @keydown.escape.window="showLikesModal = false"
         class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold">Liked by</h3>
                <button @click="showLikesModal = false" class="text-gray-600 hover:text-black text-2xl leading-none">
                    &times;
                </button>
            </div>
            <div class="overflow-y-auto p-4 space-y-3">
                @forelse($likes as $like)
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ $like->user->profile?->image_path ? asset('storage/' . $like->user->profile->image_path) : 'https://ui-avatars.com/api/?name=' . urlencode($like->user->name) }}"
                            class="h-8 w-8 rounded-full object-cover"
                            alt="{{ $like->user->name }}"
                        />
                        <span class="text-gray-800 text-sm">{{ $like->user->name }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No likes yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Image Modal with Swiper -->
    <div x-show="showImageModal" x-cloak
    x-transition.opacity
    @click.outside="showImageModal = false"
    @keydown.escape.window="showImageModal = false"
    x-init="() => {
        let modalSwiper;
        $watch('showImageModal', (val) => {
            if (val) {
                setTimeout(() => {
                    modalSwiper = new Swiper($refs.modalSlider, {
                        loop: {{ count($images) > 1 ? 'true' : 'false' }},
                        zoom: true,
                        initialSlide: currentImageIndex,
                        navigation: {
                            nextEl: $refs.modalNext,
                            prevEl: $refs.modalPrev,
                        },
                        pagination: {
                            el: $refs.modalPagination,
                            clickable: true,
                        },
                    });
                }, 50);
            }
        });
    }"
    class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center"
    >
    <!-- Fullscreen Wrapper -->
    <div class="relative w-full h-full flex items-center justify-center">

    <!-- Close Button -->
    <button class="modal-close-button" @click="showImageModal = false">
        &times;
    </button>

       <!-- Swiper Modal -->
       <div class="swiper modalSwiper w-full h-full" x-ref="modalSlider">
           <div class="swiper-wrapper">
               @foreach($images as $img)
                   <div class="swiper-slide flex items-center justify-center bg-white">
                       <div class="swiper-zoom-container w-full h-full flex items-center justify-center">
                           <img src="{{ asset('storage/' . $img) }}"
                                class="max-w-full max-h-full object-contain"
                                alt="Image preview" />
                       </div>
                   </div>
               @endforeach
           </div>

           @if(count($images) > 1)
               <div class="swiper-button-next text-white" x-ref="modalNext"></div>
               <div class="swiper-button-prev text-white" x-ref="modalPrev"></div>
               <div class="swiper-pagination text-white" x-ref="modalPagination"></div>
           @endif
       </div>
    </div>
    </div>



<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>

.modal-close-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 50;
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 9999px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    font-size: 28px;
    font-weight: bold;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
}

.modal-close-button:hover {
    background-color: #111827;
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
}


    .swiper { width: 100%; height: 100%; }

    .modalSwiper .swiper-slide {
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modalSwiper .swiper-slide img {
        object-fit: contain;
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
    }

    .mySwiper .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f9fafb;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .mySwiper .swiper-slide img {
        object-fit: cover;
        width: 100%;
        height: 100%;
        aspect-ratio: 1 / 1;
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: white;
        background: rgba(0, 0, 0, 0.4);
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        background: white;
        opacity: 0.6;
    }
    .swiper-pagination-bullet-active {
        background: #3b82f6;
        opacity: 1;
    }

    [x-cloak] { display: none !important; }
</style>

