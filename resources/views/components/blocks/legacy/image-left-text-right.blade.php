<div class="mt-10 space-y-5 grid  grid-cols-1 sm:grid-cols-3 sm:items-start gap-6 sm:space-y-2">
    <div class="aspect-w-3 aspect-h-2 sm:aspect-w-3 sm:aspect-h-3  order-1">
        <img class="object-cover rounded-lg shadow-lg" src="{{ optional(optional($content['image'])['data'])->getSize(1000) ?? '' }}">
    </div>
    <div class="col-span-2  order-2">
        <h2 class="mb-2 space-y-1 font-serif text-xl font-medium leading-6 text-zinc-900">
            {{ $content['header'] ?? '' }}
        </h2>
        <p class="text-base text-zinc-500 focus:outline-none cursor-text">
            {!! nl2br($content['text'] ?? '') !!}
        </p>
    </div>
</div>
