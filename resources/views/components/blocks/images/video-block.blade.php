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
    <div @class([
        'w-full flex',
        'justify-left' => $content['alignment'] === 'left',
        'justify-center' => $content['alignment'] === 'centre',
        'justify-right' => $content['alignment'] === 'right',
    ])>
        <div class="w-full h-full">
            <iframe class="mx-auto w-full lg:w-2/3" width="{{ $content['frame_width'] }}" height="{{ $content['frame_height'] }}" src="{{'https://www.youtube.com/embed/' .  $content['videolink'] }}"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen>
            </iframe>
        </div>
    </div>
</div>
