<div class="grid max-w-lg gap-5 mx-auto mt-10 lg:grid-cols-3 lg:max-w-none">



    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['first']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
        <div class="flex flex-col justify-between flex-1 p-6 bg-white">
            <div class="flex-1">
                <p class="text-sm font-medium text-indigo-600">
                    <a href="#" class="hover:underline focus:outline-none">
                        {{ $content['first']['subtitle'] ?? '' }}
                    </a>
                </p>
                <div class="block mt-2">
                    <p class="text-xl font-medium text-zinc-900 focus:outline-none font-serif font-family: ui-serif, Georgia ">
                        {{ $content['first']['title'] ?? '' }}
                    </p>
                    <p class="mt-3 text-base text-lg text-zinc-500 focus:outline-none">
                        {!! nl2br($content['first']['text'] ?? '') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['second']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
        <div class="flex flex-col justify-between flex-1 p-6 bg-white">
            <div class="flex-1">
                <p class="text-sm font-medium text-indigo-600">
                    <a href="#" class="hover:underline focus:outline-none">
                        {{ $content['second']['subtitle'] ?? '' }}
                    </a>
                </p>
                <div class="block mt-2">
                    <p class="text-xl font-medium text-zinc-900 focus:outline-none font-serif font-family: ui-serif, Georgia">
                        {{ $content['second']['title'] ?? '' }}
                    </p>
                    <p class="mt-3 text-base text-lg text-zinc-500 focus:outline-none">
                        {!! nl2br($content['second']['text'] ?? '') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['third']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
        <div class="flex flex-col justify-between flex-1 p-6 bg-white">
            <div class="flex-1">
                <p class="text-sm font-medium text-indigo-600">
                    <a href="#" class="hover:underline focus:outline-none">
                        {{ $content['third']['subtitle'] ?? '' }}
                    </a>
                </p>
                <div class="block mt-2">
                    <p class="text-xl font-medium text-zinc-900 focus:outline-none font-serif font-family: ui-serif, Georgia">
                        {{ $content['third']['title'] ?? '' }}
                    </p>
                    <p class="mt-3 text-base text-lg text-zinc-500 focus:outline-none">
                        {!! nl2br($content['third']['text'] ?? '') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>



</div>
