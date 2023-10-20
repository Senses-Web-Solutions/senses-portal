<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="mx-auto max-w-2xl text-center">
        <p class="text-base font-semibold leading-7 font-serif text-indigo-600">{{$content['subtitle']}}</p>
        <h2 class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ $content['heading'] }}</h2>
        <p class="mt-6 text-lg leading-8 text-zinc-600">{{ $content['text'] }}
        </p>
    </div>
</div>
