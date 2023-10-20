<div class="mb-3 mt-10 first:mt-0">
    <h2 class="text-3xl tracking-tight font-medium text-zinc-700 sm:text-4xl font-serif">
        {{ $content['header'] ?? '' }}
    </h2>
    <p class="mt-3 text-zinc-500 sm:mt-4">
        {!! nl2br($content['text'] ?? '') !!}
    </p>
</div>
