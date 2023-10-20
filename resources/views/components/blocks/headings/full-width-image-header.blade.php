@php
    $fileSrc = explode('.', $content['image']);
    $extension = end($fileSrc);
@endphp

<div class="relative flex" style="height: calc(100vh - 5rem);">

    <div class="absolute inset-0">
        @if ($extension === 'mp4')
        <video v-if="extension === 'mp4'" class="w-full h-full object-cover" autoplay muted loop playsinline>
            <source src="{{$content['image']}}" type="video/mp4">
        </video>
        @else
            <img class="w-full h-full object-cover" src="{{$content['image']}}" alt="">
        @endif

        <div class="absolute inset-0 bg-zinc-400" style="mix-blend-mode: multiply;" aria-hidden="true"></div>
    </div>

    <div class="relative max-w-7xl mx-auto  px-4 m-auto sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center mb-16">
            {{-- <div class="bg-white rounded-2xl p-8 shadow-lg mx-auto mb-48"> --}}
                <h1
                    class="text-4xl font-medium text-white sm:text-5xl md:text-4xl lg:text-5xl xl:text-8xl font-serif">
                    {{ $content['title'] }}
                </h1>

                @if (!is_null($content['subtitle']))
                    <div class="mt-4 max-w-md mx-auto text-xl text-white sm:text-xl md:mt-5 md:max-w-3xl">
                        {{ $content['subtitle'] }}
                    </div>
                @endif
            {{-- </div> --}}

        </div>

    </div>

</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <nav class="flex mt-8 mb-14" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="#" class="text-sm font-medium text-zinc-500 hover:text-zinc-700" aria-current="page">
                        Home
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <!-- Heroicon name: solid/chevron-right -->
                    <svg class="flex-shrink-0 h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>

                    <p
                        class="ml-4 text-sm font-medium text-zinc-500 hover:text-zinc-700 overflow-hidden whitespace-nowrap overflow_ellipsis">{{$content['title']}}</p>
                </div>
            </li>
        </ol>
    </nav>
</div>
