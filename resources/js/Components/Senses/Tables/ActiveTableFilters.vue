<template>
    <span class="relative z-0 inline-flex justify-end rounded-md mr-2 space-x-2"
        v-if="filteredFields.length">
        <div v-for="field in filteredFields"
                :key="field.key"
                class="-ml-px h-10 relative inline-flex items-center px-4 py-2 rounded-md border border-primary-600 bg-white text-zinc-700 hover:bg-zinc-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
            <p><span class="font-medium">{{ field.label }}</span> {{ filterSubTypeLabel(field) }} <span class="font-medium">{{ formatValue(field) }}</span></p>
            <button @click="$emit('clearFilter',field)">
            <svg
                class="text-zinc-500 group-hover:text-zinc-500 shrink-0 h-3 w-3 ml-2 border border-zinc-200 rounded-md"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
            </svg>
            </button>
        </div>
    </span>
</template>
<script>
export default {
    props:{
        filteredFields:{
            type:Array,
            required:true
        }
    },
    emits:['clearFilter'],
    methods:{
        filterSubTypeLabel(field) {
            const labels = {
                exact: 'is',
                json_title_exact: 'is',
                contains: 'contains',
                json_title_contains: 'contains',
                between: 'is between',
                greater_than: 'is greater than',
                less_than: 'is less than',
                isset_boolean: 'is'
            };

            return labels[field.filter.sub_type] ?? labels[field.filter.type] ?? labels.contains;
        },

        formatValue(field) {
            if(field.filter.label_format) {
                if(field.filter.label_format == 'boolean') {
                    return field.filter.value == 1 ? 'True' : 'False';
                }
            }
            return field.filter.value;
        }
    }
}
</script>
