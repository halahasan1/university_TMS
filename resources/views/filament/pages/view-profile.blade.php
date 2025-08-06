<x-filament::page>
    <x-filament::card class="!p-8">
        <div class="flex flex-col md:flex-row gap-8 items-center">
            <!-- Profile Image Section - Improved with better hover effects -->
            <div class="w-full md:w-1/3 flex justify-center">
                <div class="relative group w-44 h-44 rounded-full p-1.5 border-4 border-gray-100 dark:border-gray-700 hover:border-primary-100 dark:hover:border-primary-500 transition-all duration-300">
                    @if($profile->image_path)
                        <div class="w-full h-full rounded-full overflow-hidden shadow-lg transition-all duration-500 ease-in-out group-hover:shadow-xl">
                            <img src="{{ asset('storage/' . $profile->image_path) }}"
                                 alt="Profile Image"
                                 class="w-full h-full object-cover transform transition-transform duration-500 ease-in-out group-hover:scale-110">
                            <div class="absolute inset-0 bg-primary-500/0 group-hover:bg-primary-500/10 rounded-full transition-all duration-500"></div>
                        </div>
                    @else
                        <div class="w-full h-full rounded-full overflow-hidden bg-gradient-to-br from-gray-100 to-gray-300 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center shadow-lg">
                            <img src="{{ asset('images/default-avatar.png') }}"
                                 alt="Default Avatar"
                                 class="w-3/4 h-3/4 object-contain opacity-80 group-hover:opacity-100 transition-opacity duration-500">
                        </div>
                    @endif

                    <!-- Pulse Ring Effect on Hover -->
                    <div class="absolute inset-0 rounded-full border-2 border-transparent group-hover:border-primary-300/30 transition-all duration-700 ease-in-out pointer-events-none"></div>
                </div>
            </div>

            <!-- Profile Details Section -->
            <div class="w-full md:w-2/3 space-y-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-primary-600 dark:text-primary-400">{{ $user->email }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($profile->phone)
                        <div class="flex items-start space-x-2">
                            <x-heroicon-s-phone class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5" />
                            <span class="text-gray-700 dark:text-gray-300">{{ $profile->phone }}</span>
                        </div>
                    @endif

                    @if($profile->address)
                        <div class="flex items-start space-x-2">
                            <x-heroicon-s-map-pin class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5" />
                            <span class="text-gray-700 dark:text-gray-300">{{ $profile->address }}</span>
                        </div>
                    @endif

                    @if($profile->department)
                        <div class="flex items-start space-x-2">
                            <x-heroicon-s-building-office-2 class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5" />
                            <span class="text-gray-700 dark:text-gray-300">{{ $profile->department->name }}</span>
                        </div>
                    @endif
                </div>

                @if($profile->bio)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">About</h3>
                        <p class="text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none">{{ $profile->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::card>
    <style>
        /* Custom Animation for Smooth Hover Effects */
        .group:hover img {
            filter: brightness(1.05);
            transition: filter 300ms ease, transform 500ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Pulse Animation for the Ring */
        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.02); }
        }

        .group:hover div:last-child {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-filament::page>


