<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'full'])>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="col-span-1" style="vertical-align: top">
            @foreach($content['col1'] as $block)
                {!! view("components.blocks.{$block['name']}", ['content' => $block['content']])->render() !!}
            @endforeach
        </div>
        <div class="col-span-1" style="vertical-align: top">
            @foreach($content['col2'] as $block)
                {!! view("components.blocks.{$block['name']}", ['content' => $block['content']])->render() !!}
            @endforeach
        </div>
    </div>
</div>
