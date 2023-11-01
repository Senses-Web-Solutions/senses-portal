<template>
    <SensesTable
        url="/api/v2/server-metrics"
        table="server_metrics"
        setting="table-server-metrics"
        event="server-metric-updated"
        :fields="fields"
        :actions="actions"
        @row-click="rowClick"
    />
</template>

<script>

import SensesTable from '../Tables/SensesTable.vue';

export default {
    components: {
        SensesTable,
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },

				{ label: "Timestamp", key: "timestamp" },
				{ label: "Uptime", key: "uptime" },
				{ label: "Cpu Use", key: "cpu_use" },
				{ label: "Cpu Idle", key: "cpu_idle" },
				{ label: "Load 1", key: "load_1" },
				{ label: "Load 5", key: "load_5" },
				{ label: "Load 15", key: "load_15" },
				{ label: "Ram Free", key: "ram_free", filter: { type: "integer" } },
				{ label: "Ram Used", key: "ram_used", filter: { type: "integer" } },
				{ label: "Disk Free", key: "disk_free", filter: { type: "integer" } },
				{ label: "Disk Used", key: "disk_used", filter: { type: "integer" } },
				{ label: "Swap Free", key: "swap_free", filter: { type: "integer" } },
				{ label: "Swap Used", key: "swap_used", filter: { type: "integer" } },


         ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-server-metric')) {
                if (type && type === 'blank') {
                    window.open("/server-metrics/" + row.id);
                } else {
                    window.location.href = "/server-metrics/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:27


major number | minor number | device name | reads completed successfully | reads merged | sectors read | time spent reading (ms) | writes completed | writes merged | sectors written  time spent writing (ms)  I/Os currently in progress  time spent doing I/Os (ms)  weighted time spent doing I/Os (ms)  discards completed successfully  discards merged  sectors discarded  time spent discarding
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
7            | 0            | loop0       | 398                          | 0            | 2568         | 31                      | 0                | 0             | 0                0                        0                           228                         0                                    0                                0                0                  0
7            | 1            | loop1       | 1849                         | 0            | 4304         | 791                     | 0                | 0             | 0                0                        0                           456                         12                                   0                                0                0                  0
7            | 2            | loop2       | 79                           | 0            | 2194         | 26                      | 0                | 0             | 0                0                        0                           48                          0                                    0                                0                0                  0
7            | 3            | loop3       | 133                          | 0            | 2304         | 92                      | 0                | 0             | 0                0                        0                           128                         24                                   0                                0                0                  0
7            | 4            | loop4       | 28822                        | 0            | 58256        | 22651                   | 0                | 0             | 0                0                        0                           3356                        9856                                 0                                0                0                  0
7            | 5            | loop5       | 595                          | 0            | 2974         | 74                      | 0                | 0             | 0                0                        0                           456                         4                                    0                                0                0                  0
7            | 6            | loop6       | 194786                       | 0            | 391350       | 44822                   | 0                | 0             | 0                0                        0                           20980                       8120                                 0                                0                0                  0
7            | 7            | loop7       | 35886                        | 0            | 72944        | 2966                    | 0                | 0             | 0                0                        0                           3604                        152                                  0                                0                0                  0
252          | 0            | vda         | 4035400                      | 560163       | 260504862    | 3502465                 | 59689034         | 25339833      | 1979715500       429467807                0                           85505884                    329386348                            354377                           0                78234468           134758
252          | 1            | vda1        | 3997152                      | 560163       | 260440015    | 3353618                 | 58101121         | 25339833      | 1979715498       424947540                0                           81749276                    326766172                            354325                           0                72997600           134717
252          | 14           | vda14       | 246                          | 0            | 2202         | 34                      | 0                | 0             | 0                0                        0                           132                         0                                    0                                0                0                  0
252          | 15           | vda15       | 37830                        | 0            | 54726        | 148768                  | 2                | 0             | 2                2                        0                           1504                        130792                               52                               0                5236868            40
252          | 16           | vdb         | 111                          | 0            | 819          | 12                      | 0                | 0             | 0                0                        0                           60                          0                                    0                                0                0                  0
7            | 8            | loop8       | 38421                        | 0            | 78020        | 4190                    | 0                | 0             | 0                0                        0                           3740                        0                                    0                                0                0                  0
7            | 9            | loop9       | 18                           | 0            | 48           | 11                      | 0                | 0             | 0                0                        0                           32                          0                                    0                                0                0                  0
