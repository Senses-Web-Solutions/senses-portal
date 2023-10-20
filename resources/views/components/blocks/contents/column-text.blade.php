<div @class(["relative max-w-7xl mx-auto", 'mb-0' => $content['bottom_space'] === 'none', 'mb-10' => $content['bottom_space'] === 'small', 'mb-16' => $content['bottom_space'] === 'medium', 'mb-24' => $content['bottom_space'] === 'large'])>
    <div class="max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none px-8 xl:px-0">
        @foreach ($content['features'] as $col)
        <div class="flex flex-col overflow-hidden text-center">
            <div class="flex-1 flex flex-col justify-between">
                <div class="flex-1">
                    <div class="block ">
                        <p class="text-xl font-medium text-zinc-700 font-serif">
                            {{$col['content']['heading']}}
                        </p>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <p class="text-base text-zinc-500 hover:text-zinc-900">
                                    {{$col['content']['line1']}}
                                </p>
                            </li>
                            <li>
                                <p class="text-base text-zinc-500 hover:text-zinc-900">
                                    {{$col['content']['line2']}}
                                </p>
                            </li>
                            <li>
                                <p class="text-base text-zinc-500 hover:text-zinc-900">
                                    {{$col['content']['line3']}}
                                </p>
                            </li>

                            <li>
                                <p class="text-base text-zinc-500 hover:text-zinc-900">
                                    {{$col['content']['line4']}}
                                </p>
                            </li>

                            <li>
                                <p class="text-base text-zinc-500 hover:text-zinc-900">
                                    {{$col['content']['line5']}}
                                </p>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
