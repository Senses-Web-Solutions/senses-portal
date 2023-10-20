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
    @php
        if ($content['showLatest']) {
            $blogs = App\Models\Blog::orderBy('id', 'desc')
                ->take(sizeof($content['features']))
                ->get();
        }
    @endphp

    <ul class="max-w-lg mx-auto grid grid-cols-1 gap-8 md:px-20 lg:px-0 lg:grid-cols-3 lg:max-w-none xl:px-0">
        @if ($content['showLatest'])
            @foreach ($blogs as $blog)
                <li class="cursor-pointer min-h-400">
                    <a href="{{ '/destination_blogs/' . $blog->slug }}">
                        <div
                            class="relative flex flex-col overflow-hidden transition duration-500 rounded-lg shadow-lg hover:shadow-2xl">
                            <div class="flex-shrink-0 overflow-hidden transition duration-700 ease-in-out">
                                <img class="object-cover w-full h-60 image-roll" src="{{ $blog->featuredImage->getSize(300) }}"
                                    alt="">
                            </div>
                            <div class="flex flex-col justify-between p-6 mb-5 bg-white h-55 max-h-60">
                                <div class="flex-1">
                                    <div class="overflow-hidden h-22 max-h-22">
                                        <p class="font-serif text-2xl font-medium text-zinc-700 break-words">
                                            {{ $blog->title }}
                                        </p>
                                        <p class="mt-1 text-sm">by {{ $blog->author->first_name }}
                                            {{ $blog->author->last_name }}
                                        </p>
                                    </div>

                                    <div class="text-md text-zinc-500 my-1 overflow-hidden" style="height: 150px">
                                        {!! nl2br($blog->snippet(50, 18, '...')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 mt-3 rounded-bl-lg rounded-br-lg bg-zinc-50 md:px-8">
                                <a href="{{ '/destination_blogs/' . $blog->slug }}"
                                    class="text-base font-medium text-indigo-900 capitalize hover:text-indigo-600">
                                    Read More <span aria-hidden="true"> →</span>
                                </a>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        @elseif (!$content['showLatest'])
            @foreach ($content['features'] as $blog)
                <li class="cursor-pointer min-h-400">
                    <a href="{{ $blog['content']['url'] }}">
                        <div
                            class="relative flex flex-col overflow-hidden transition duration-500 rounded-lg shadow-lg hover:shadow-2xl">
                            <div class="flex-shrink-0 overflow-hidden transition duration-700 ease-in-out">
                                <img class="object-cover w-full h-60 image-roll" src="{{ $blog['content']['image'] }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between p-6 mb-5 bg-white h-55 max-h-60">
                                <div class="flex-1">
                                    <div class="overflow-hidden h-22 max-h-22">
                                        <p class="font-serif text-2xl font-medium text-zinc-700">
                                            {{ $blog['content']['heading'] }}
                                        </p>
                                        <p class="mt-1 text-sm">{{ $blog['content']['author'] }}</p>
                                    </div>
                                    <div class="font-normal text-zinc-500 my-1 overflow-hidden" style="height: 150px">
                                        @php
                                            if (strlen($blog['content']['paragraph']) > 200) {
                                                $stringCut = substr($blog['content']['paragraph'], 0, 200);
                                                $endPoint = strrpos($stringCut, ' ');

                                                $blog['content']['paragraph'] = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                                $blog['content']['paragraph'] .= '... ';
                                            }
                                        @endphp
                                        {!! nl2br($blog['content']['paragraph']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 mt-3 rounded-bl-lg rounded-br-lg bg-zinc-50 md:px-8">
                                <div class="text-base font-medium text-indigo-900 capitalize hover:text-indigo-600">
                                    {{ $blog['content']['button_text'] }}<span aria-hidden="true"> →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>
