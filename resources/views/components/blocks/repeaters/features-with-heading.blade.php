@php
    $fileSrc = explode('.', $content['image']);
    $extension = end($fileSrc);
@endphp

<div @class([
    'mb-0' => $content['bottom_space'] === 'none',
    'mb-10' => $content['bottom_space'] === 'small',
    'mb-16' => $content['bottom_space'] === 'medium',
    'mb-24' => $content['bottom_space'] === 'large',
])>
    <div class="relative flex" style="height: 75vh;">

        <div class="absolute inset-0">
            @if ($extension == 'mp4' || $extension == 'm4v')
                <video class="w-full h-full object-cover" autoplay loop muted playsinline>
                    <source src="{{ $content['image'] }}"
                        type="{{ $extension == 'm4v' ? 'video/x-m4v' : 'video/' . $extension }}">
                </video>
            @else
                <img class="w-full h-full object-cover" src="{{ $content['image'] }}" alt="">
            @endif

            <div class="absolute inset-0 bg-zinc-400 bg-opacity-50 mix-blend-multiply"></div>
        </div>

        <div class="relative max-w-7xl mx-auto m-auto flex flex-col justify-center" style="height: 75vh;">
            <div class="mx-auto max-w-3xl text-center mb-16">
                <h1
                    class="text-4xl font-medium text-white sm:text-5xl md:text-4xl lg:text-5xl xl:text-7xl font-serif">
                    {{ $content['title'] }}
                </h1>

                @if (!is_null($content['subtitle']))
                    <div class="mt-4 max-w-md mx-auto text-xl text-white sm:text-xl md:mt-5 md:max-w-3xl">
                        {{ $content['subtitle'] }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="-mt-32 max-w-7xl px-4 md:px-6 lg:px-8 mx-auto">
        <div class="grid grid-cols-1 md:px-20 lg:px-0 lg:grid-cols-3 gap-8 gap-y-16">
            @foreach ($content['features'] as $feature)
                <div class="bg-white rounded-2xl shadow-lg relative hover:shadow-2xl flex flex-col justify-between transition duration-500">
                    @if ($feature['content']['open_live_chat'])
                        <a href="#" onclick="$crisp.push(['do', 'chat:open'])">
                    @else
                        <a href="{{$feature['content']['button_url']}}">
                    @endif
                        <div class="flex-1 relative pt-16 px-6 pb-8 md:px-8">
                            <div @class([
                                'absolute top-0 p-5 inline-block rounded-xl shadow-lg transform -translate-y-1/2',
                                'text-' . $feature['content']['icon_colour'],
                                'bg-' . $feature['content']['icon_bg_colour'],
                            ])>
                                <svg stroke-width="1.5" stroke="currentColor"
                                    class="w-7 h-7 text-{{ $feature['content']['icon_colour'] }}"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="{{ $feature['content']['svg']['viewBox'] }}">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="{{ $feature['content']['svg']['path'] }}">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-medium text-zinc-700 font-serif break-words">
                                {{ $feature['content']['heading'] }}</h3>
                            <p class="text-zinc-500 font-normal my-1 overflow-hidden">
                                @php
                                    if (strlen($feature['content']['paragraph']) > 200) {
                                        $stringCut = substr($feature['content']['paragraph'], 0, 200);
                                        $endPoint = strrpos($stringCut, ' ');

                                        $feature['content']['paragraph'] = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                        $feature['content']['paragraph'] .= '... ';
                                    }
                                @endphp
                                {!! nl2br($feature['content']['paragraph']) !!}
                            </p>
                        </div>
                    </a>
                    <div class="p-6 bg-zinc-50 rounded-bl-2xl rounded-br-2xl md:px-8">
                        @if ($feature['content']['open_live_chat'])
                            <button class="text-base font-medium text-indigo-700 hover:text-indigo-600 capitalize"
                                onclick="$crisp.push(['do', 'chat:open'])">{{ $feature['content']['button_text'] }}<span
                                    aria-hidden="true"> &rarr;</span></button>
                        @else
                            <a href="{{ $feature['content']['button_url'] }}"
                                class="text-base font-medium text-indigo-700 hover:text-indigo-600 capitalize">
                                {{ $feature['content']['button_text'] }}<span aria-hidden="true"> →</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
