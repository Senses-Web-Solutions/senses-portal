<template>
    <Card flush>
        <template #title>Planner</template>
        <template #actions>
            <SecondaryButton :disabled="true">Sep 2021</SecondaryButton>
            <SecondaryButton :disabled="true"
                ><ChevronLeftIcon class="h-4 w-4"
            /></SecondaryButton>
            <!-- //todo make these pills -->
            <SecondaryButton :disabled="true"
                ><ChevronRightIcon class="h-4 w-4"
            /></SecondaryButton>
        </template>
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                >
                    <div
                        class="overflow-hidden border-b border-zinc-200 shadow sm:rounded-b-lg"
                    >
                        <table
                            class="relative min-w-full divide-y divide-zinc-200"
                        >
                            <thead
                                class="bg-zinc-white divide-x divide-zinc-200"
                            >
                                <tr>
                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Mon
                                                </p>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Tue
                                                </p>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Wed
                                                </p>
                                            </div>
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Thu
                                                </p>
                                            </div>
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Fri
                                                </p>
                                            </div>
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Sat
                                                </p>
                                            </div>
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div
                                            class="md:flex md:items-center md:justify-between"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="px-4 py-2 text-left font-medium text-zinc-500"
                                                >
                                                    Sun
                                                </p>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-zinc-200 bg-white"
                            >
                                <tr
                                    class="divide-x divide-zinc-200"
                                >
                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-50 px-2 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-2 mb-1 text-sm font-bold text-zinc-300"
                                        >
                                            30
                                        </p>
                                        <div
                                            class="mt-3 flex inline-flex truncate"
                                        >
                                            <div>
                                                <p
                                                    class="w-20 truncate text-zinc-500"
                                                >
                                                    Summer Bank Holiday
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-center text-sm font-bold text-zinc-300"
                                        >
                                            31
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-center bg-repeat-x px-4 py-2 text-zinc-700"
                                        style="
                                            background-image: url(/images/angle-3.png);
                                        "
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            1
                                        </p>
                                        <p
                                            class="text-sm-old mx-auto bg-zinc-50 text-center font-medium text-zinc-400"
                                        >
                                            Day off
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            2
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            3
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    2 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    6 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            4
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            5
                                        </p>
                                    </td>
                                </tr>

                                <tr
                                    class="divide-x divide-zinc-200"
                                >
                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 px-4 py-2 text-zinc-700 opacity-50"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            6
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-center text-sm font-bold text-zinc-400"
                                        >
                                            7
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            8
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            9
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1text-zinc-700 truncate"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            10
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 ml-2 truncate text-zinc-700"
                                                >
                                                    2 Tasks
                                                </p>
                                                <p
                                                    class="ml-2 truncate text-sm text-zinc-500"
                                                >
                                                    6 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            11
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            12
                                        </p>
                                    </td>
                                </tr>

                                <tr
                                    class="divide-x divide-zinc-200"
                                >
                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            13
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-red-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-center text-sm font-bold text-zinc-400"
                                        >
                                            14
                                        </p>

                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    6 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            15
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            16
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            17
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    2 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    6 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            18
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            19
                                        </p>
                                    </td>
                                </tr>

                                <tr
                                    class="divide-x divide-zinc-200"
                                >
                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            20
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-center text-sm font-bold text-zinc-400"
                                        >
                                            21
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            22
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            23
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            24
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    2 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    6 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            25
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            26
                                        </p>
                                    </td>
                                </tr>

                                <tr
                                    class="divide-x divide-zinc-200"
                                >
                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            27
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-blue-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-center text-sm font-bold text-zinc-400"
                                        >
                                            28
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1 truncate text-zinc-700"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            29
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-yellow-100 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-400"
                                        >
                                            30
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <div>
                                                <p
                                                    class="mt-1text-zinc-700 truncate"
                                                >
                                                    4 Tasks
                                                </p>
                                                <p
                                                    class="truncate text-sm text-zinc-500"
                                                >
                                                    8 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-50 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-300"
                                        >
                                            1
                                        </p>
                                        <div class="mt-3 flex inline-flex">
                                            <span
                                                class="mt-2.5 h-1.5 w-1.5 rounded-full bg-zinc-300"
                                                aria-hidden="true"
                                            ></span>
                                            <div>
                                                <p
                                                    class="mt-1 ml-2 truncate text-zinc-400"
                                                >
                                                    2 Tasks
                                                </p>
                                                <p
                                                    class="ml-2 truncate text-sm text-zinc-300"
                                                >
                                                    6 hrs
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-300"
                                        >
                                            2
                                        </p>
                                    </td>

                                    <td
                                        class="relative h-20 w-28 truncate whitespace-nowrap bg-zinc-50 bg-opacity-75 px-4 py-2 text-zinc-700"
                                    >
                                        <p
                                            class="absolute top-2 left-4 mb-1 text-sm font-bold text-zinc-300"
                                        >
                                            3
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
<script>
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import Card from '../../Ui/Cards/Card.vue';
import { ChevronRightIcon, ChevronLeftIcon } from '@heroicons/vue/outline';

export default {
    components: { SecondaryButton, Card, ChevronRightIcon, ChevronLeftIcon },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
};
</script>
