@php
    $user = auth()->user();
    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->implode(', ') : '';
    $hour = now()->format('H');
    $greeting = $hour < 12 ? 'صباح الخير' : ($hour < 17 ? 'نهارك سعيد' : 'مساء الخير');
    $unread = $user->unreadNotifications()->count();

    $canCreateTask = method_exists($user, 'hasRole') && $user->hasRole(['super_admin','dean','professor']);
@endphp

<div class="rounded-2xl border bg-gradient-to-br from-amber-50 to-white p-5 dark:from-gray-900 dark:to-gray-900">
    <div class="flex items-start gap-4">
        <div class="h-12 w-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold dark:bg-amber-900/30 dark:text-amber-300">
            {{ \Illuminate\Support\Str::of($user->name)->substr(0,2)->upper() }}
        </div>

        <div class="flex-1">
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $greeting }} 👋</div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white">
                أهلاً {{ $user->name }}
            </div>

            @if($roles)
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    الدور: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $roles }}</span>
                </div>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs text-gray-700 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6z" />
                        <path d="M7 13a3 3 0 006 0H7z" />
                    </svg>
                    {{ $unread }} إشعار غير مقروء
                </span>

                @if($canCreateTask)
                    <a href="{{ \App\Filament\Resources\TaskResource::getUrl('create') }}"
                       class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        + مهمة جديدة
                    </a>
                @endif

                @if(class_exists(\App\Filament\Resources\NewsResource::class))
                    <a href="{{ \App\Filament\Resources\NewsResource::getUrl('create') }}"
                       class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:bg-gray-800 dark:text-amber-300 dark:ring-amber-800">
                        + خبر جديد
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
        <span>{{ now()->translatedFormat('l d M, h:i a') }}</span>
        <span class="h-1 w-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
        <span>أهلاً بك في لوحة الإدارة</span>
    </div>
</div>
