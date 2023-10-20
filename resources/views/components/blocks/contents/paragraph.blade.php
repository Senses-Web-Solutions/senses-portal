<div @class(['w-full' => $content['width'] === 'w-full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="first:mt-0">
        <p  @class(['cursor-text',
        'text-left' => $content['justify'] === 'left',
        'text-center' => $content['justify'] === 'centre',
        'text-right' => $content['justify'] === 'right'])>
            {!! nl2br($content['paragraph']) !!}
        </p>
    </div>
</div>
