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
        <div class="grid lg:grid-cols-6 md:grid-cols-3 grid-cols-2  gap-x-10 gap-y-10">
            @foreach ($content['features'] as $feature)
                <div class=" col-span-1 lg:w-44 lg:h-44 xs:w-32 xs:h-32 object-cover transition duration-500 ">
                    @if ($feature['content']['span'] === 'double')
                    <div class="col-span-1">
                        <a class='h-48 overflow-hidden object-cover relative' href="{{ $feature['content']['url'] }}">
                            <img class="lg:w-44 lg:h-20 xs:w-32 xs:h-32 object-cover mb-4 transition duration-500 shadow-lg hover:shadow-2xl"
                                src="{{$feature['content']['image1']}}">
                        </a>
                        <a class='h-48 overflow-hidden object-cover relative' href="{{ $feature['content']['url'] }}">
                            <img class="lg:w-44 lg:h-20 xs:w-32 xs:h-32 object-cover transition duration-500 shadow-lg hover:shadow-2xl"
                            src="{{$feature['content']['image2']}}">
                        </a>
                    </div>
                    @endif
                    @if ($feature['content']['span'] === 'single')
                    <a class='h-48 overflow-hidden object-cover relative' href="{{ $feature['content']['url'] }}">
                        <img class=" col-span-1 lg:w-44 lg:h-44 xs:w-32 xs:h-32 object-cover transition duration-500 shadow-lg hover:shadow-2xl"
                            src="{{$feature['content']['image1']}}">
                    </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
