    <div class="relative pt-20 pb-12 lg:py-20 mt-10">
        <div class="relative lg:flex lg:items-center">
            <div class="relative lg:ml-10">
                <svg
                    class="absolute top-0 left-0 transform -translate-x-8 -translate-y-24 h-36 w-36 text-indigo-200 opacity-50"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 144 144"
                    aria-hidden="true"
                >
                    <path
                        stroke-width="2"
                        d="M41.485 15C17.753 31.753 1 59.208 1 89.455c0 24.664 14.891 39.09 32.109 39.09 16.287 0 28.386-13.03 28.386-28.387 0-15.356-10.703-26.524-24.663-26.524-2.792 0-6.515.465-7.446.93 2.327-15.821 17.218-34.435 32.11-43.742L41.485 15zm80.04 0c-23.268 16.753-40.02 44.208-40.02 74.455 0 24.664 14.891 39.09 32.109 39.09 15.822 0 28.386-13.03 28.386-28.387 0-15.356-11.168-26.524-25.129-26.524-2.792 0-6.049.465-6.98.93 2.327-15.821 16.753-34.435 31.644-43.742L121.525 15z"
                    ></path>
                </svg>
                <blockquote class="relative">
                    <div class="text-2xl leading-9 font-medium text-zinc-900 italic font-serif">
                        <p>{!! nl2br($content['text'] ?? '') !!}</p>
                    </div>
                    <footer class="mt-8">
                        <div class="flex">
                            <div class="flex-shrink-0 lg:hidden">
                                <img
                                    class="h-12 w-12 rounded-full"
                                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixqx=OzKnH3gfEF&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
                                    alt=""
                                >
                            </div>
                            <div class="ml-4 lg:ml-0">
                                <div class="text-base font-medium text-zinc-900 focus:outline-none">{{ $content['byline'] ?? '' }}</div>
                                <div class="text-base  text-zinc-500 focus:outline-none">{{ $content['title'] ?? '' }}</div>
                            </div>
                        </div>
                    </footer>
                </blockquote>
            </div>
        </div>
    </div>
