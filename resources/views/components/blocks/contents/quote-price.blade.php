<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="mx-auto divide-y-2 divide-zinc-200 ">
        <div class="pb-2 border-b-2">
        <h2 @class(['text-3xl  text-zinc-400 font-serif font-thin',
            'text-left' => $content['justify_text'] === 'left',
            'text-center' => $content['justify_text'] === 'centre',
            'text-right' => $content['justify_text'] === 'right'])>
            £{{$content['price']}}
        </h2>
        </div>
    </div>
</div>
