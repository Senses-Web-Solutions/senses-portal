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
    <div class="divide-y divide-zinc-900/10">
        <h2 class="text-2xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ $content['title'] }}</h2>
        <dl class="mt-10 space-y-8 divide-y divide-zinc-900/10">
            @foreach ($content['features'] as $feature)
                <div class="pt-8 lg:grid lg:grid-cols-12 lg:gap-8">
                    <dt class="text-base font-semibold leading-7 text-zinc-900 lg:col-span-5">{{$feature['content']['heading']}}</dt>
                    <dd class="mt-4 lg:col-span-7 lg:mt-0">
                        <p class="text-base leading-7 text-zinc-600">{{$feature['content']['answer']}}</p>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
