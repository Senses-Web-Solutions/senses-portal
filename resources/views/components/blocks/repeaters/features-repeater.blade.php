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
    <div class="mt-8">
        <div class="grid grid-cols-1 md:px-20 lg:px-0 lg:grid-cols-3 gap-8 gap-y-16">
            @foreach ($content['features'] as $feature)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl">
                    @if ($feature['content']['open_live_chat'])
                        <a href="#" onclick="$crisp.push(['do', 'chat:open'])">
                    @else
                        <a href="{{$feature['content']['button_url']}}">
                    @endif
                        <div class="flex-1 relative pt-16 px-6 mb-8 md:px-8">
                            <div
                                class="absolute top-0 p-5 inline-block rounded-xl shadow-lg transform -translate-y-1/2 bg-{{ $feature['content']['icon_bg_colour'] }}">
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

                            <div class="mt-4 overview-hidden break-words" style="height: 148px">
                                <p class="font-normal text-zinc-500 my-1 overflow-hidden">
                                    @php
                                        if (strlen($feature['content']['paragraph']) > 200) {
                                            // truncate string
                                            $stringCut = substr($feature['content']['paragraph'], 0, 200);
                                            $endPoint = strrpos($stringCut, ' ');

                                            //if the string doesn't contain any space then it will cut without word basis.
                                            $feature['content']['paragraph'] = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                            $feature['content']['paragraph'] .= '... ';
                                        }
                                    @endphp

                                    {!! nl2br($feature['content']['paragraph']) !!}
                                </p>
                            </div>
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
