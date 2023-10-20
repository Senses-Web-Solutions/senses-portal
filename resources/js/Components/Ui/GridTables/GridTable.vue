<template>
    <div class="overflow-x-auto w-72">
        <div class="grid" :style="styles">
            <div v-for="field in fields" :key="field.key" class="p-1 overflow-hidden truncate">
                {{ field.label }}
            </div>
            <template v-for="row in data">
                <div v-for="field in fields" :key="field.key + row[field.key]" class="p-1 overflow-hidden truncate">
                    {{ row[field.key] }}
                </div>
            </template>
        </div>
    </div>
</template>
<script>
import genKey from "../../../Support/genKey"

export default {
    data () {
        return {
            fields: [
                { label: "ID", key: "id", width: '500px' },
                { label: "First Name", key: "first_name", },
                { label: "Last Name", key: "last_name", },
                { label: "Email", key: "email", },
                { label: "Colour", key: "colour", }
            ],
            data: [
                {
                    id: 1,
                    first_name: 'Harry',
                    last_name: 'Bayliss',
                    email: 'harry@senses.co.uk',
                    colour: '#00ff00'
                },
                {
                    id: 2,
                    first_name: 'Ross',
                    last_name: 'Dexter',
                    email: 'ross@senses.co.uk',
                    colour: '#00ff00'
                },
                {
                    id: 3,
                    first_name: 'Rob',
                    last_name: 'Lees',
                    email: 'rob@senses.co.uk',
                    colour: '#00ff00'
                },
            ],
            tableKey: genKey()
        }
    },
    computed: {
        gridDeclarations() {
            return this.fields.map((field) => field.width ? field.width : 'minmax(150px, 1fr)')
        },
        styles() {
            return {
                'grid-template-columns': this.gridDeclarations.join(' ')
            }
        }
    }
}
</script>
<style>

</style>