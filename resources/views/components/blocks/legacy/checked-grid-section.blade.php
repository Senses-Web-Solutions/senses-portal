<div class="mt-10">
    <div class="bg-none mb-5">
        <div class="max-w-7xl mx-auto pb-3 divide-y-2 divide-zinc-200 lg:pb-3">
            <h2 class="text-2xl font-large text-zinc-900 sm:text-2xl font-serif mb-5">
                {{ $content['title'] ?? '' }}
            </h2>
            <div class="mt-3">
            </div>
        </div>
    </div>

    <div class="mt-12 lg:mt-0">
        <div class="space-y-5 sm:space-y-2 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-5 lg:gap-x-8">

            @if(count($content['items'] ?? []))
                @foreach ($content['items'] as $item)
                <div class="relative">
                    <div>
                        <!-- Heroicon name: outline/check -->
                        <svg
                            class="absolute h-6 w-6 text-green-500"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                        <p class="ml-9 text-lg leading-6 font-medium text-zinc-900 font-serif font-family: ui-serif, Georgia,">{{ $item['title'] ?? '' }}</p>
                    </div>
                    <div class="mt-2 ml-9 text-base text-lg text-zinc-500">{{ $item['description'] ?? '' }}</div>
                </div>
                @endforeach
            @endif

        </div>
    </div>
</div>
