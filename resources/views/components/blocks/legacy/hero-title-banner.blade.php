<div class="mt-10 ">
    <div class="mx-auto max-w-7xl rounded-lg">
        <div class="relative ">

            <div class="absolute inset-0 rounded-lg">
                <img
                    src="{{ optional(optional($content['image'])['data'])->getSize(1600) ?? '' }}"
                    class="object-cover w-full h-full rounded-lg"
                >
                {{-- <div
                    class="absolute inset-0  pointer-events-none"
                    style="mix-blend-mode: multiply;"
                ></div> --}}
            </div>

            <div class="relative px-4 my-10 sm:px-6  lg:px-8">
                <div class="py-16 md:py-20 lg:py-24">
                    <div
                        class="text-base text-lg font-medium tracking-wide text-zinc-200  focus:outline-none lg:text-center"
                    >{{ $content['text'] ?? '' }}</div>
                    <h2
                        class="mt-2 font-serif text-5xl font-extrabold leading-8 tracking-tight  text-white sm:text-5xl focus:outline-none lg:text-center"
                    >{{ $content['title'] ?? '' }}</h2>
                </div>
            </div>

        </div>
    </div>
</div>
