@php
    $fileSrc = explode('.', $content['image']);
    $extension = end($fileSrc);
@endphp

<div @class([
    'relative',
    'mb-0' => $content['bottom_space'] === 'none',
    'mb-10' => $content['bottom_space'] === 'small',
    'mb-16' => $content['bottom_space'] === 'medium',
    'mb-24' => $content['bottom_space'] === 'large',
])>
    <div class="max-w-screen">
        <div class="relative sm:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if ($extension === 'mp4')
                    <video v-if="extension === 'mp4'" class="w-full h-full object-cover" autoplay muted loop playsinline>
                        <source src="{{ $content['image'] }}" type="video/mp4">
                    </video>
                @else
                    <img class="w-full h-full object-cover" src="{{ $content['image'] }}" alt="">
                @endif

                <div class="absolute inset-0 bg-zinc-900 bg-opacity-50 mix-blend-multiply"></div>
            </div>
            <div class="relative px-4 py-16 sm:px-6 sm:py-24 lg:py-32 lg:px-8 max-w-7xl mx-auto">
                <div class="grid grid-cols-1 px-10 md:px-20 lg:px-0 lg:grid-cols-3 gap-8">
                    <div class="col-span-1 bg-white rounded-2xl p-8">
                        <svg class="flex-shrink-0 w-8 h-8 text-indigo-600 mx-auto mb-5"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="{{ $content['col_1_icon']['viewBox'] }}" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $content['col_1_icon']['path'] }}"></path>
                        </svg>
                        <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                            {{ $content['col_1_title'] }}
                        </p>

                        <p class="text-base text-zinc-700 leading-relaxed text-center">
                            {{ $content['col_1_text'] }}
                        </p>
                    </div>

                    <div class="col-span-1 bg-white rounded-2xl p-8">
                        <svg class="flex-shrink-0 w-8 h-8 text-indigo-600 mx-auto mb-5"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="{{ $content['col_2_icon']['viewBox'] }}" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $content['col_2_icon']['path'] }}"></path>
                        </svg>
                        <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                            {{ $content['col_2_title'] }}
                        </p>

                        <p class="text-base text-zinc-700 leading-relaxed text-center">
                            {{ $content['col_2_text'] }}
                        </p>
                    </div>

                    <div class="col-span-1 bg-white rounded-2xl p-8">
                        <svg class="flex-shrink-0 w-8 h-8 text-indigo-600 mx-auto mb-5"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="{{ $content['col_3_icon']['viewBox'] }}" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $content['col_3_icon']['path'] }}"></path>
                        </svg>
                        <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                            {{ $content['col_3_title'] }}
                        </p>

                        <p class="text-base text-zinc-700 leading-relaxed text-center">
                            {{ $content['col_3_text'] }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
