<div class="text-center mt-10">
    <h2 class="text-3xl tracking-tight font-medium text-zinc-900 sm:text-4xl font-serif">
        {{ $content['header'] ?? '' }}
    </h2>
    <p class="mt-3 max-w-3xl mx-auto text-lg text-zinc-500 sm:mt-4">
        {!! nl2br($content['text'] ?? '') !!}
    </p>
</div>
