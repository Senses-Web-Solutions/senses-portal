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
    <div class="">
        <div class="grid grid-cols-1 md:px-20 lg:px-0 lg:grid-cols-3 gap-8 gap-y-16">
            @foreach ($content['features'] as $feature)
                <div class="cursor-pointer">
                    <div class="transition duration-500 flex flex-col rounded-lg shadow-lg overflow-hidden hover:shadow-2xl relative"
                        style="max-height:610px; min-height:610px;">
                        <div class="transition duration-700 ease-in-out flex-shrink-0 overflow-hidden relative">
                            @if($feature['content']['label'])
                            <div class="absolute top-5 right-0">
                                <div class="bg-{{$feature['content']['label_colour']}} px-3 py-2 rounded-l-lg">
                                    <p class="text-white font-semibold">{{ $feature['content']['label'] }}</p>
                                </div>
                            </div>
                            @endif
                            <img class="h-60 w-full object-cover image-roll" src="{{ $feature['content']['image'] }}"
                                alt="" loading="lazy">
                        </div>
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between min-h-48">
                            <div class="flex-1 items-center">
                                <div class="text-base text-zinc-500 inline-flex space-between items-center">
                                    <div class="flex-shrink-0 flex mb-3 space-x-2">
                                        @for ($i = 0; $i < $feature['content']['rating']; $i++)
                                            <div v-for="rating in parseInt(feature.content.rating)"
                                                class="text-yellow-500" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1
                                                        0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                                                        1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175
                                                        0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                                                        00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                                                        00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endfor
                                        <div class="text-yellow-500 opacity-0" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1
                                                0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                                                1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175
                                                0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                                                00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                                                00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class=" text-xs text-zinc-400 flex">
                                    {{-- <p class="mb-1 underline">{{$feature['content']['location']}}</p> --}}
                                </div>
                                <div class="h-16 max-h-16 overflow-hidden">
                                    <p class="text-2xl font-medium text-zinc-700 font-serif ">
                                        {{ $feature['content']['heading'] }}
                                    </p>
                                </div>


                                <div class="font-normal text-zinc-500 my-1 overflow-hidden" style="min-height: 150px;">
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
                                </div>
                            </div>

                        </div>
                        @if ($feature['content']['url'])
                        <div class="p-6 bg-zinc-50 rounded-bl-lg rounded-br-lg md:px-8 mt-2 absolute"
                            style="bottom: 0; right: 0; left: 0;">
                            <a href="{{ $feature['content']['url'] }}"
                                class="text-base font-medium text-indigo-900 hover:text-indigo-600 capitalize">Read More<span aria-hidden="true"> →</span></a>
                        </div>
                        @endif
                    </div>


                </div>
            @endforeach
        </div>
    </div>
</div>
