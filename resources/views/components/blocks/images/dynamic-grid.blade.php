<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="grid grid-cols-1 sm:grid-cols-6 gap-y-2 overflow-hidden shadow-none rounded-none sm:rounded-2xl sm:shadow-lg sm:gap-x-2">
        @foreach ($content['features'] as $feature)
                <a @class([
                    'h-48 overflow-hidden object-cover relative rounded-2xl shadow-lg sm:rounded-none sm:shadow-none',
                    'col-span-2' => $feature['content']['span'] == 'third',
                    'col-span-3' => $feature['content']['span'] == 'half',
                    'col-span-6' => $feature['content']['span'] == 'full',
                ]) href="{{ $feature['content']['url'] }}">
                    <h6 class="absolute top-3 left-4 text-white font-serif text-2xl tracking-wider z-50 font-medium">
                        {{ $feature['content']['heading'] }}</h3>
                    <div class="image-roll h-full">
                        <div class="absolute top-0 left-0 h-full w-full bg-zinc-800 opacity-50"></div>
                        <img class="h-full w-full object-cover shadow-lg" src="{{ $feature['content']['image'] }}"
                            alt="">
                    </div>
                </a>
        @endforeach
    </div>
</div>
