@php
    $logo = asset('images/latakia-university-logo.png');
@endphp

<a
    href="{{ filament()->getUrl() }}"
    class="fi-brand group inline-flex items-center gap-3 rounded-xl px-1 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50"
>
    <img
        src="{{ $logo }}"
        alt="Latakia University logo"
        class="h-9 w-9 rounded-full ring-1 ring-amber-400/40 bg-white object-cover shadow-sm dark:ring-amber-300/30"
        loading="lazy"
    />

    <span class="flex items-baseline gap-1.5">
        <span class="text-[15px] font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-100">
            Latakia
        </span>
        <span class="text-[15px] font-semibold leading-none tracking-tight
                     bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent
                     dark:from-amber-400 dark:to-orange-300">
            University
        </span>
    </span>
</a>
