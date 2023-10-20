<div @class([
    'w-full' => $content['width'] == 'full',
    'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] == 'standard',
    'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] == 'reduced',
    'mb-0' => $content['bottom_space'] == 'none',
    'mb-10' => $content['bottom_space'] == 'small',
    'mb-16' => $content['bottom_space'] == 'medium',
    'mb-24' => $content['bottom_space'] == 'large',
])>
    <div class="mx-auto grid grid-cols-1 lg:grid-cols-2">
        @if ($content['alignment'] == 'right')
            <div class="col-span-1">
                <img class="h-full object-cover object-center" src="{{ $content['selectedImage'] }}" alt="">
            </div>
        @endif
        <div class="px-6 col-span-1">
            <div class="py-10 lg:py-16">
                <p class="text-base font-semibold leading-7 text-indigo-600 font-serif">{{ $content['category'] }}</p>
                <h1 class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">
                    {{ $content['title'] }}</h1>
                {{-- <p class="mt-6 text-xl leading-8 text-zinc-700">{{ $content['subtitle'] }}</p> --}}
                <div class="mt-10 max-w-xl font-sans-serif text-base leading-7 text-zinc-700 lg:max-w-none">
                    <p>{!! nl2br($content['paragraph']) !!}</p>
                </div>

                @if ($content['button_text'])
                    <a href="{{ $content['url'] ?? '' }}" @if (!str_contains($content['url'], env('APP_URL'))) target="_blank" @endif
                        class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white transition-all bg-black hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mt-10">{{ $content['button_text'] }}</a>
                @endif
            </div>
        </div>
        @if ($content['alignment'] == 'left')
            <div class="col-span-1">
                <img class="h-full object-cover object-center" src="{{ $content['selectedImage'] }}" alt="">
            </div>
        @endif
    </div>
</div>
