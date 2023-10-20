<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <figure class="border-l border-indigo-600 pl-9">
        <blockquote class="font-semibold text-zinc-900">
            <p>"{{nl2br($content['paragraph'])}}"</p>
        </blockquote>
        <figcaption class="mt-6 flex gap-x-4">

            <div class="text-sm leading-6"><strong class="font-semibold text-zinc-900">{{ $content['name'] }}</strong> – {{ $content['subheader'] }}</div>
        </figcaption>
    </figure>
</div>
