<template>
    <div class="relative">
        <div class="overflow-hidden">
            <div>
                <div class="relative flex flex-col">

                    <div class="-mt-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full pt-2 align-middle sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 table-fixed">
                                    <thead>
                                        <tr>
                                            <!-- <th class="text-zinc-500 px-4 py-2 overflow-hidden text-left" style="width:50px">A</th>
                                            <th class="text-zinc-500 px-4 py-2 overflow-hidden text-left" style="width:200px">B</th>
                                            <th class="text-zinc-500 px-4 py-2 overflow-hidden text-left" style="width:600px">C</th> -->

                                            <th class="text-zinc-500" style="width:50px">
                                                <div class="px-4 py-2" style="width:50px">
                                                    <div class="overflow-hidden text-left">
                                                    Really really really long text
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="text-zinc-500" width="50px">
                                                <div class="px-4 py-2" style="width:50px">
                                                <div class="overflow-hidden text-left">
                                                 Really really really long text
                                                </div>
                                                </div>
                                            </th>
                                            <th class="text-zinc-500">
                                                <div class="px-4 py-2">
                                                <div class="overflow-hidden text-left">
                                                 Really really really long text
                                                </div>
                                                </div>
                                            </th>

                                            <!-- <th>
                                                <div style="width:50px" class="text-zinc-500 px-4 py-2">
                                                    <div class="overflow-hidden text-left">
                                                    Really really really long text
                                                    </div>
                                                </div>
                                            </th>
                                            <th>
                                                <div style="width:50px" class="text-zinc-500 px-4 py-2">
                                                    <div class="overflow-hidden text-left">
                                                    Really really really long text
                                                    </div>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="text-zinc-500 px-4 py-2">
                                                    <div class="overflow-hidden text-left">
                                                    Really really really long text
                                                    </div>
                                                </div>
                                            </th> -->

                                            <!-- <th>
                                                <div style="width:200px" class="text-zinc-500 px-4 py-2 overflow-hidden text-left">
                                                    B
                                                </div>
                                            </th>
                                            <th>
                                                <div style="width:600px" class="text-zinc-500 px-4 py-2 overflow-hidden text-left">
                                                    C
                                                </div>
                                            </th> -->
                                            <!--<th>
                                                <div style="width:600px" class="text-zinc-500 px-4 py-2 overflow-hidden text-left">
                                                    D
                                                </div>
                                            </th>
                                            <th>
                                                <div class="text-zinc-500 px-4 py-2 overflow-hidden text-left">
                                                    E
                                                </div>
                                            </th>
                                            <th>
                                                <div class="text-zinc-500 px-4 py-2 overflow-hidden text-left">
                                                    F
                                                </div>
                                            </th> -->

                                            <!-- <th :class="thClass"><div style="width:50px" class="px-4 py-2">A</div></th>      -->
                                            <!-- <th :class="thClass"><div style="width:200px" class="px-4 py-2">B</div></th> -->
                                            <!-- <th :class="thClass"><div style="width:600px" class="px-4 py-2">C</div></th> -->

                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                        <tr class="divide-x divide-zinc-200 cursor-pointer dark:divide-zinc-800 hover:bg-zinc-100">
                                            <td :class="tdClass">A</td>
                                            <td :class="tdClass">B</td>
                                            <td :class="tdClass">C</td>
                                            <!-- <td :class="tdClass">D</td>
                                            <td :class="tdClass">E auto length?</td>
                                            <td :class="tdClass">F</td>  -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    computed: {
        thClass() {
            return 'overflow-hidden text-left text-zinc-500 border-r bg-zinc-50 border-r-grey-200 first:rounded-t-lg';
        },
        tdClass() {
            return 'px-4 py-2 whitespace-nowrap min-w-8 max-w-8';
        }
    }
}
</script>
