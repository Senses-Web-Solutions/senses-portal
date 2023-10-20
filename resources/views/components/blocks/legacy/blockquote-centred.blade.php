<blockquote class="mt-10 ">
    <div class="max-w-3xl mx-auto text-center text-2xl leading-9 font-medium text-zinc-900 font-serif italic">
        <p>
            {!! nl2br($content['text'] ?? '') !!}
        </p>
    </div>
    <footer class="mt-8">
        <div class="md:flex md:items-center md:justify-center">
            <div class="md:flex-shrink-0">
                <img class="mx-auto h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixqx=OzKnH3gfEF&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
                    alt="">
            </div>
            <div class="mt-3 text-center md:mt-0 md:ml-4 md:flex md:items-center">
                <div class="text-base font-medium text-zinc-900">{{ $content['byline'] ?? '' }}</div>

                <svg class="hidden md:block mx-1 h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 0h3L9 20H6l5-20z"></path>
                </svg>

                <div class="text-base text-zinc-500">{{ $content['title'] ?? '' }}</div>
            </div>
        </div>
    </footer>
</blockquote>
