<div @class(['w-full' => $content['width'] === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8' => $content['width'] === 'reduced', 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="owl-carousel owl-theme">
        @foreach ($content['features'] as $feature)
        <div class="bg-white overflow-hidden shadow rounded-lg border border-zinc-100">
            <div class="px-4 py-3 sm:p-5">
                <div class="flex-shrink-0 flex my-3 space-x-2 justify-center">
                    @for ($i = 0; $i < 5; $i++)
                        <div
                            class="text-yellow-500"
                            aria-hidden="true">
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5
                            w-5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M9.049 2.927c.3-.921
                            1.603-.921 1.902 0l1.07 3.292a1 1
                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                            1.81l-2.8 2.034a1 1 0 00-.364
                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                            1.118l-2.8-2.034a1 1 0 00-1.175
                            0l-2.8
                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                            1 0
                            00-.364-1.118L2.98
                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                            1 0
                            00.951-.69l1.07-3.292z" />
                            </svg>
                            </div>
                    @endfor
                </div>

                <div class="text-center">
                    <div class="">
                        <p
                            class="text-xl font-medium text-zinc-700 font-serif h-14 overflow-hidden mb-3">
                            "{{ strip_tags(substr($feature['content']['testimony'], 0, 100)) }}..."
                        </p>
                    </div>

                    <p class="text-md text-zinc-700">
                        {{ $feature['content']['author'] }}
                    </p>
                    <a href="/testimonials">
                        <p class="text-sm text-zinc-500 capitalize underline">
                            Read All Reviews
                        </p>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
