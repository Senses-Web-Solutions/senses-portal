@php
    $fileSrc = explode('.', $content['selectedImage']);
    $extension = end($fileSrc);
@endphp

<div @class(["relative", 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="max-w-screen">
        <div class="relative sm:overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                @if ($extension === 'mp4')
                    <video v-if="extension === 'mp4'" class="w-full h-full object-cover" autoplay muted loop playsinline>
                        <source src="{{ $content['selectedImage'] }}" type="video/mp4">
                    </video>
                @else
                    <img class="w-full h-full object-cover" src="{{ $content['selectedImage'] }}" alt="">
                @endif
                <div class="absolute inset-0 bg-zinc-400 bg-opacity-50 mix-blend-multiply"></div>
            </div>
            <div class="relative px-4 py-24 sm:px-6 sm:py-60 lg:py-32 lg:px-8 max-w-7xl mx-auto">
                {{-- <div class="bg-white rounded-2xl p-8 shadow-lg mx-auto"> --}}
                    <p @class([
                        'text-3xl font-medium font-serif text-white',
                        'text-left' => $content['alignment'] === 'left',
                        'text-center' => $content['alignment'] === 'centre',
                        'text-right' => $content['alignment'] === 'right',
                    ])>
                        {{ $content['paragraph'] }}
                    </p>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>
