<template>
    <Card flush>
        <template #title>Communication Log</template>
        <template #description>Recent communications received by this user</template>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-zinc-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                            <thead class="bg-zinc-50">
                            <tr>
                                <th scope="col">
                                    <div class="md:flex md:items-center md:justify-between">
                                        <div class="flex-1 min-w-0">
                                            <StrongText class="px-4 py-2 text-left">Type</StrongText>
                                        </div>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="md:flex md:items-center md:justify-between">
                                        <div class="flex-1 min-w-0">
                                            <StrongText class="px-4 py-2 text-left">Date Sent</StrongText>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-zinc-200 dark:divide-zinc-800">
                            <tr class="divide-x divide-zinc-200 dark:divide-zinc-800">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>Email</Text>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>todo</Text>
                                </td>
                            </tr>

                            <tr class="divide-x divide-zinc-200 dark:divide-zinc-800">
                                <td class="px-4 py-2 whitespace-nowrap ">
                                    <Text>Letter</Text>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap ">
                                    <Text>todo</Text>
                                </td>
                            </tr>

                            <tr class="divide-x divide-zinc-200 dark:divide-zinc-800">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>SMS</Text>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>todo</Text>
                                </td>
                            </tr>

                            <tr class="divide-x divide-zinc-200 dark:divide-zinc-800">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>Telephone call</Text>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <Text>todo</Text>
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
import Card from '../../Ui/Cards/Card.vue';
import Text from '../../Ui/Text/Text.vue';
import StrongText from '../../Ui/Text/StrongText.vue';

export default {
    components: {Card, Text, StrongText},
    props: {
        data: {
            type: Object,
            required: true
        }
    }
}
</script>
