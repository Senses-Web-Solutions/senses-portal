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
    @if ($content['alignment'] == 'left')
        <div class="grid grid-cols-1 px-0 lg:grid-cols-2 mb-10">
            <div @class([
                'cursor-text col-span-1',
                'text-left' => $content['justify_text'] === 'left',
                'text-center' => $content['justify_text'] === 'centre',
                'text-right' => $content['justify_text'] === 'right',
            ])>
                <p>{!! nl2br($content['paragraph']) !!}</p>
            </div>
            <div class="col-span-1 relative h-full">
                <div @class([
                    'mt-10 lg:mt-0 flex justify-center my-auto',
                    'left-0 top-1/2' => $content['justify_button'] === 'left',
                    'left-1/2 top-1/2 -translate-x-1/2' =>
                        $content['justify_button'] === 'centre',
                    'right-0 top-1/2' => $content['justify_button'] === 'right',
                ])>
                    <a href="{{ $content['button_url'] }}"
                        class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm {{ 'bg-' . $content['button_colour'] . ' hover:bg-' . $content['button_hover_colour'] . ' text-' . $content['button_text_colour'] }} transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ $content['button_text'] }}
                    </a>
                </div>
            </div>
        </div>
    @endif
    @if ($content['alignment'] == 'right')
        <div class="grid grid-cols-1 px-0 lg:grid-cols-2">
            <div class="col-span-1 relative h-full">
                <div @class([
                    'absolute mt-10 lg:mt-0 transform -translate-y-1/2',
                    'left-0 top-1/2' => $content['justify_button'] === 'left',
                    'left-1/2 top-1/2 -translate-x-1/2' =>
                        $content['justify_button'] === 'centre',
                    'right-0 top-1/2' => $content['justify_button'] === 'right',
                ])>
                    <a href="{{ $content['button_url'] }}"
                        class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm {{ 'bg-' . $content['button_colour'] . ' hover:bg-' . $content['button_hover_colour'] . ' text-' . $content['button_text_colour'] }} transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ $content['button_text'] }}
                    </a>
                </div>
            </div>
            <div @class([
                'cursor-text col-span-1',
                'text-left' => $content['justify_text'] === 'left',
                'text-center' => $content['justify_text'] === 'centre',
                'text-right' => $content['justify_text'] === 'right',
            ])>
                <p>{!! nl2br($content['paragraph']) !!}</p>
            </div>
        </div>
    @endif
</div>
