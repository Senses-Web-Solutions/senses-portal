<template>
    <canvas id="chart" @click="test"></canvas>
</template>

<script>
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';

import useEcho from '../../../../Support/useEcho';

const echo = useEcho();

export default {
    components: {
        PrimaryButton,
    },

    props: {
        data: {
            type: Object
        }
    },

    mounted() {
        var domChart = document.getElementById('chart').getContext('2d');

        var gradientStroke1 = domChart.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
        gradientStroke1.addColorStop(0, "rgba(0, 0, 0, 0)"); //purple colors

        var gradientStroke2 = domChart.createLinearGradient(0, 230, 0, 50);

        gradientStroke2.addColorStop(1, "rgba(20,23,39,0.2)");
        gradientStroke2.addColorStop(0.2, "rgba(72,72,176,0.0)");
        gradientStroke2.addColorStop(0, "rgba(20,23,39,0)"); //purple colors


        var gradientStroke3 = domChart.createLinearGradient(0, 130, 0, 20);

        gradientStroke3.addColorStop(1, "rgba(10,23,39,0.2)");
        gradientStroke3.addColorStop(0.2, "rgba(62,72,176,0.0)");
        gradientStroke3.addColorStop(0, "rgba(60,23,39,0)"); //???

        var chart = new Chart(domChart, {
            type: "line",
            data: {
                labels: ["9am", "10am", "11am", "12pm", "1pm", "2pm", "3pm", "4pm", "5pm", "6pm", "7pm", "8pm"],
                datasets: [
                    {
                        label: "Load - 1 Min",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#cb0c9f",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        fill: true,
                        data: [650, 40, 300, 220, 500, 250, 400, 230, 500, 345, 123, 678],
                        maxBarThickness: 6,
                    },
                    {
                        label: "WebsLoad - 5 Mins",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#3A416F",
                        borderWidth: 3,
                        backgroundColor: gradientStroke2,
                        fill: true,
                        data: [330, 90, 40, 140, 290, 290, 340, 230, 400],
                        maxBarThickness: 6,
                    },
                    {
                        label: "Load - 15 Mins",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#3A416F",
                        borderWidth: 3,
                        backgroundColor: gradientStroke3,
                        fill: true,
                        data: [1440, 190, 20, 190, 890, 890, 840, 1230, 1400],
                        maxBarThickness: 6,
                    },
                ],
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                animation: {

                },

                plugins: {
                    legend: {
                        display: false, // We have our own legend
                    },
                },

                interaction: {
                    intersect: false,
                    mode: "index",
                },

                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: "#b2b9bf",
                            font: {
                                size: 11,
                                style: "normal",
                                lineHeight: 2,
                            },
                        },
                    },

                    x: {
                        ticks: {
                            display: true,
                            color: "#b2b9bf",
                            padding: 20,
                            font: {
                                size: 11,
                                style: "normal",
                                lineHeight: 0,
                            },
                        },
                    },
                },
            },
        });

        echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({serverMetric}) => {
            console.log(serverMetric);

            var dataset = Chart.getChart("chart").data.datasets[0].data;

            dataset.shift();
            dataset.push(serverMetric.load_15 * 100);

            Chart.getChart("chart").data.datasets[0].data = dataset;
            Chart.getChart("chart").update();

            console.log(Chart.getChart("chart").data.datasets[0]);
        })
    },

    methods: {
        test() {
            var domChart = document.getElementById('chart').getContext('2d');

            var gradientStroke1 = domChart.createLinearGradient(0, 230, 0, 50);

            gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");

            Chart.getChart("chart").data.datasets[0] = {
                label: "Load - 1 Min",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#cb0c9f",
                borderWidth: 3,
                backgroundColor: gradientStroke1,
                fill: true,
                data: [Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000, Math.random() * 1000],
                maxBarThickness: 6,
            },

            console.log(Chart.getChart("chart").data.datasets[0]);
            Chart.getChart("chart").update();
        }
    }
};
</script>
