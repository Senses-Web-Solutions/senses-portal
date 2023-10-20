<div class="grid grid-cols-2 gap-8 my-12">
    <div>
        <h3 class="text-xl font-medium text-zinc-900">Travel Information</h3>
        <div class="flow-root mt-5">
            <ul class="-mb-8">
                @foreach($content['travelInformation'] as $index => $travelInformation)
                <li>
                    <div class="relative pb-8">
                        @if($index !== (count($content['travelInformation']) - 1))
                        <span
                            v-if="index !== content.travelInformation.length - 1"
                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200"
                            aria-hidden="true"
                        ></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>

                                <span class="h-8 w-8 rounded-full bg-white flex items-center justify-center ring-8 ring-white stroke-current text-purple-600">
                                    <svg
                                        class="text-green-450 flex-shrink-0 h-6 w-6 text-center items-center"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >{!! $travelInformation['icon'] ?? '' !!}</svg>
                                </span>

                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div class="w-full">
                                    <p class="text-base text-zinc-900 focus:outline-none">{{ $travelInformation['title'] }}</p>
                                    <div class="text-sm text-zinc-700 mt-3 focus:outline-none cursor-text">{!! $travelInformation['text'] !!}</div>
                                </div>
                                <div class="text-right text-sm whitespace-nowrap text-zinc-500 focus:outline-none">
                                    {{ strip_tags($travelInformation['date']) ?? '' }}
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div>
        <p class="text-xl font-medium text-zinc-900 mb-5 font-serif font-family: ui-serif, Georgia">Additional Information</p>
        <div>

            @foreach($content['additionalInformation'] as $index => $additionalInformation)

            <div class="mb-5 relative">
                <p class="text-base text-zinc-900 focus:ouline-none cursor-text">{{ $additionalInformation['title'] }}</p>
                <div class="text-sm text-zinc-700 mt-3 focus:ouline-none cursor-text">{!! $additionalInformation['text'] !!}</div>
            </div>

            @endforeach

        </div>
    </div>
</div>
