import axios from 'axios';
import useEcho from '../../Support/useEcho';

const echo = useEcho();

export default {
    props: {
        server: {
            type: Object,
            required: true
        }
    },

    mounted() {
        if (!this.server.verified_at) {
            this.updateStatus();
            return;
        }

        echo.private(`servers.${this.server.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({serverMetric}) => {
            this.lastRecievedTimestamp = new Date().getTime() / 1000;

            this.updateStatus();

            this.previousMetric = this.metric;
            this.metric = serverMetric;

            this.metrics.unshift(serverMetric);
        })

        echo.private(`servers.${this.server.id}.deploy`).listen('Servers\\ServerDeployed', ({data}) => {
            if (data.status == 'running') {
                this.updateStatus('deploying');
            }

            else if (data.status == 'completed') {
                this.updateStatus('deploy_success');

                setTimeout(() => {
                    this.updateStatus();
                }, 10000);
            }

            else {
                this.updateStatus('danger');
            }
        })

        setInterval(() => {
            this.timeSinceLastUpdate = this.hrDuration(Math.round(new Date().getTime() / 1000 - (this.lastRecievedTimestamp ?? this.metric.timestamp)));
        }, 1000);

        this.load();
    },

    data() {
        return {
            metric: {
                load_1: 0,
                load_5: 0,
                load_15: 0,

                disk_free: 0,
                disk_used: 0,
                disk_total: 0,

                ram_free: 0,
                ram_used: 0,
                ram_total: 0,

                swap_free: 0,
                swap_used: 0,
                swap_total: 0,

                timestamp: 0,
            },

            metrics: [],
            previousMetric: null,

            timeSinceLastUpdate: 'N/A',
            lastRecievedTimestamp: null,

            status: 'normal',
        };
    },

    methods: {
        updateStatus(status=null) {
            if (!this.server.verified_at) {
                this.status = "unverified";
                console.log("Setting status to " + "unverified")
                return;
            }

            if (status) {
                this.status = status;
                console.log("Setting status to " + status)
                return;
            }

            if (this.status == 'deploying') {
                console.log("Blocked status change as server is currently deploying");
                return;
            }

            if (this.metric.disk_used / this.metric.disk_total >= 0.9) {
                this.status = 'danger';
                console.log("Setting status to " + 'danger')
            } else if (this.metric.ram_used / this.metric.ram_total >= 0.9) {
                this.status = 'danger';
                console.log("Setting status to " + 'danger')
            } else if (this.metric.swap_used / this.metric.swap_total >= 0.9) {
                this.status = 'danger';
                console.log("Setting status to " + 'danger')
            } else if (this.metric.load_15 <= this.previousMetric.load_15) {
                this.status = 'load_down';
                console.log("Setting status to " + 'load_down')
            } else if (this.metric.load_15 > this.previousMetric.load_15) {
                this.status = 'load_up';
                console.log("Setting status to " + 'load_up')
            } else {
                this.status = 'idle';
                console.log("Setting status to " + 'idle')
            }
        },

        hrDuration (seconds) {
            if (!seconds) {
                return '0 Secs';
            }

            const days = Math.floor(seconds / 86400);
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor(seconds / 60) % 60;
            const secs = seconds % 60;

            const dayString = days > 1 ? ' Days' : ' Day';
            const hourString = hours > 1 ? ' Hrs' : ' Hr';
            const minString = mins > 1 ? ' Mins' : ' Min';
            const secString = secs > 1 ? ' Secs' : ' Sec';

            let string = [];

            hours ? string.push(`${hours} ${hourString}`) : string;
            mins ? string.push(`${mins} ${minString}`) : string;
            hours ? string : string.push(`${secs} ${secString}`);

            days ? string = [`${days} ${dayString}`] : string;

            string = string.join(" ");

            return string;
        },

        load() {
            axios.get('/api/v2/servers/' + this.server.id + '/server-metrics?format=limited&limit=50').then((response) => {
                if (response.data.length > 0) {
                    this.metric = response.data[0];
                    this.metrics = response.data;

                    this.previousMetric = response.data[1] ?? null;

                    this.updateStatus();
                }
            });
        },

        getColour(load) {
            if (load <= 0.50) {
                return '#4ade80';
            }

            if (load <= 0.90) {
                return '#fbbf24';
            }

            if (load <= 1.00) {
                return '#f87171';
            }

            if (load > 1.00) {
                return '#ae81ff';
            }
        },
    },
};
