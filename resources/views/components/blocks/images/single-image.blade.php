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
    @if ($content['shape'] == 'banner')
        <div class="">
            <div class="h-44">
                <a @class([
                    'h-48 overflow-hidden object-cover relative',
                ]) href="{{ $content['url'] }}">
                    <img @class([
                        'w-full h-full object-cover rounded-lg shadow-lg object-bottom',
                        'object-top' => $content['focus'] === 'top',
                        'object-center' => $content['focus'] === 'centre',
                        'object-bottom' => $content['focus'] === 'bottom',
                    ]) src="{{ $content['image'] }}" alt="">
                </a>
            </div>
        </div>
    @elseif ($content['shape'] == 'original')
        <div class="">
            <div>
                <a @class([
                    'h-48 overflow-hidden object-cover relative',
                ]) href="{{ $content['url'] }}">
                    <img @class([
                        'object-cover rounded-lg shadow-lg mx-auto',
                        'object-top' => $content['focus'] === 'top',
                        'object-center' => $content['focus'] === 'centre',
                        'object-bottom' => $content['focus'] === 'bottom',
                    ]) src="{{ $content['image'] }}" alt="">
                </a>
            </div>
        </div>
    @endif
</div>
