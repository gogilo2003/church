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
    attendances: { label: string, total: number }[]
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
    labels: props.attendances.map(attendance => attendance.label),
    datasets: [
        {
            label: 'Attendances',
            backgroundColor: 'rgba(75, 192, 192, 1)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            data: props.attendances.map(tithe => tithe.total)
        }
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
