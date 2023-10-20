<div @class([
    'w-full' => $content['width'] === 'full',
    'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' =>
        $content['width'] === 'standard',
    'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced',
    'mb-0' => $content['bottom_space'] === 'none',
    'mb-10' => $content['bottom_space'] === 'small',
    'mb-16' => $content['bottom_space'] === 'medium',
    'mb-24' => $content['bottom_space'] === 'large',
])>
    <div class="">
        <div class="mx-auto max-w-2xl sm:text-center">
            <h2 class="text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ $content['title'] }}
            </h2>
            <p class="mt-2 text-lg leading-8 text-zinc-600">{{ $content['subtitle'] }}</p>
        </div>
        @foreach ($content['features'] as $feature)
            <div class="mx-auto mt-20 max-w-5xl space-y-16">
                <div class="grid grid-cols-6 lg:grid-cols-12">
                    <div class="col-span-1">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="{{ $feature['content']['svg'] }}" />
                            </svg>
                        </div>
                    </div>
                    <div class="col-span-5 lg:col-span-11">
                        <h3 class="text-base font-semibold leading-7 text-zinc-900 font-serif">
                            {{ $feature['content']['heading'] }}</h3>
                        <p class="mt-2 leading-7 text-zinc-600">{{ $feature['content']['text'] }}</p>
                        <p class="mt-4">
                            <a href="{{ $feature['content']['url'] }}"
                                class="text-sm font-semibold leading-6 text-indigo-600">{{ $feature['content']['link_text'] }}
                                <span aria-hidden="true">&rarr;</span>
                    </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
