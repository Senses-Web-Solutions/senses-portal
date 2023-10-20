<div class="grid max-w-lg gap-5 mx-auto mt-10 lg:grid-cols-3 lg:max-w-none">

    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['first']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
    </div>

    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['second']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
    </div>

    <div class="flex flex-col overflow-hidden rounded-lg shadow-lg">
        <div class="flex-shrink-0">
            <img
                class="object-cover w-full h-48 cursor-pointer"
                src="{{ optional(optional($content['third']['image'])['data'])->getSize(1000) ?? '' }}"
            >
        </div>
    </div>

</div>
