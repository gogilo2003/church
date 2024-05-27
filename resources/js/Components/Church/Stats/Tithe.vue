<template>
    <div class="w-full">
        <canvas ref="titheChart"></canvas>
    </div>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { Chart, registerables } from 'chart.js';
import { formatCurrency, getWeekOfYear } from '../../../helpers';

const props = defineProps<{
    tithes: { week: string, total: number }[]
    offerings: { week: string, total: number }[]
    contributions: { week: string, total: number }[]
}>()

// Register the components for Chart.js
Chart.register(...registerables);

// Props for passing chart data
interface ChartData {
    labels: string[];
    datasets: {
        label: string;
        backgroundColor: string;
        borderColor: string;
        borderWidth: number;
        data: number[];
    }[];
}

// Dummy data for demonstration purposes
const chartData: ChartData = {
    labels: props.tithes.map(tithe => `Week ${getWeekOfYear(tithe.week)}`),
    datasets: [
        {
            label: 'Total Tithes',
            backgroundColor: 'rgba(75, 192, 192, 1)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            data: props.tithes.map(tithe => tithe.total)
        },
        {
            label: 'Total Offerings',
            backgroundColor: 'rgba(210, 230, 34, 1)',
            borderColor: 'rgba(210, 230, 34, 1)',
            borderWidth: 2,
            data: props.offerings.map(offering => offering.total)
        },
        {
            label: 'Total Contributions',
            backgroundColor: 'rgba(230, 34, 116, 1)',
            borderColor: 'rgba(230, 34, 116, 1)',
            borderWidth: 2,
            data: props.contributions.map(contribution => contribution.total)
        },
    ]
};

const titheChart = ref<HTMLCanvasElement | null>(null);

watchEffect(() => {
    if (titheChart.value) {
        new Chart(titheChart.value.getContext('2d')!, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value, index, ticks) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
