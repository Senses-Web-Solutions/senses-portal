<div @class([
    'relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    'mb-0' => $content['bottom_space'] == 'none',
    'mb-10' => $content['bottom_space'] == 'small',
    'mb-16' => $content['bottom_space'] == 'medium',
    'mb-24' => $content['bottom_space'] == 'large',
])>
    <div>
        @if ($content['subheading'])
            <p class="text-base font-semibold font-serif leading-7 text-indigo-600">{{ $content['subheading'] }}</p>
        @endif
        @if ($content['heading'])
        <h1 class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">
            {{ $content['heading'] }}</h1>
        @endif
        <div
            class="mt-10 grid max-w-xl grid-cols-1 gap-8 text-base leading-7 text-zinc-700 lg:max-w-none lg:grid-cols-2">
            <div>
                {{ nl2br($content['col_1_text']) }}
            </div>
            <div>
                {{ nl2br($content['col_2_text']) }}
            </div>
        </div>
        <div class="mt-10 flex">
            <a href="{{ $content['button_url'] }}"
                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ $content['button_text'] }}</a>
        </div>
    </div>
</div>
