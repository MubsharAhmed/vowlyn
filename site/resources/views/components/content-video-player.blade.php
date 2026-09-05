@props(['film', 'hero' => false, 'number' => null])
<div x-data="contentVideo" data-video-url="{{ $film->video_url }}" data-video-title="{{ $film->title }}"
     class="relative aspect-[9/14] overflow-hidden bg-black {{ $hero ? 'rounded-[2rem] shadow-[0_40px_100px_rgba(0,0,0,.55)]' : 'rounded-[1.5rem]' }}">
    <img x-show="!started" src="{{ $film->poster_url }}" alt="" width="720" height="1120"
         loading="{{ $hero ? 'eager' : 'lazy' }}" decoding="async" class="absolute inset-0 h-full w-full object-cover">
    <video x-ref="film" preload="none" playsinline :controls="started" x-show="started" x-cloak
           @play="onPlay()" @pause="playing = false" @ended="playing = false" x-on:error="onError()"
           aria-label="{{ $film->title }}" class="absolute inset-0 h-full w-full object-contain"></video>
    <div x-show="!started" class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
    <button x-show="!started" type="button" @click="play()" :disabled="loading"
            aria-label="Play {{ $film->title }}" :aria-busy="loading"
            class="absolute inset-0 z-10 grid place-items-center text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#18d2ff]">
        <span class="grid size-16 place-items-center rounded-full border border-white/35 bg-[#0d0420]/45 transition hover:bg-[#4b0082]">
            <i x-show="!loading" data-lucide="play" class="size-5 fill-current" aria-hidden="true"></i>
            <span x-show="loading" x-cloak class="text-xs">Loading</span>
        </span>
    </button>
    @if ($hero)
        <div x-show="!started" class="absolute inset-x-0 bottom-0 z-10 pointer-events-none p-6 sm:p-8">
            <p class="text-[10px] font-mono uppercase tracking-[.22em] text-[#18d2ff] break-words">{{ $film->client }}</p>
            <p class="editorial mt-1 text-3xl break-words">{{ $film->title }}</p>
        </div>
    @else
        <div x-show="!started" class="absolute inset-x-0 top-0 z-20 flex items-center justify-between p-4 pointer-events-none">
            <span class="rounded-full bg-black/60 px-3 py-1.5 text-[9px] font-mono tracking-[.18em] text-white">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="rounded-full bg-black/60 px-3 py-1.5 text-[9px] font-mono tracking-[.18em] text-white">{{ $film->duration }}</span>
        </div>
    @endif
    <p x-show="error" x-cloak x-text="error" role="status" class="absolute inset-x-4 top-16 z-20 rounded-xl bg-black/90 p-3 text-sm text-white"></p>
    <noscript><a href="{{ $film->video_url }}" class="absolute inset-0 z-30 grid place-items-center text-white bg-black/50">Watch {{ $film->title }}</a></noscript>
</div>
