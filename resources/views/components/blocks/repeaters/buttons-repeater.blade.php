<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 gap-y-16">
            @foreach ($content['features'] as $feature)
                <div class="col-span-1 max-w-3xl mx-auto lg:max-w-7xl px-4 sm:px-6 lg:px-8" >
                    <a href="{{$feature['content']['url']}}" class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-700 hover:bg-blue-500 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{$feature['content']['heading']}}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
