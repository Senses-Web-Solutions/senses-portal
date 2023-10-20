<div  @class([
    'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
    'mb-0' => $content['bottom_space'] === 'none',
    'mb-10' => $content['bottom_space'] === 'small',
    'mb-16' => $content['bottom_space'] === 'medium',
    'mb-24' => $content['bottom_space'] === 'large',
])>
    <div @class([
        'max-w-3xl mx-auto lg:max-w-7xl px-4 sm:px-6 lg:px-8 text-center',
        'lg:text-left' => $content['alignment'] === 'left',
        'lg:text-center' => $content['alignment'] === 'centre',
        'lg:text-right' => $content['alignment'] === 'right',
    ])>
        <a href="{{ $content['url'] ?? '' }}" @if (!str_contains($content['url'], env('APP_URL'))) target="_blank" @endif
            class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white transition-all bg-black hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">{{ $content['text'] ?? '' }}</a>
    </div>
</div>
