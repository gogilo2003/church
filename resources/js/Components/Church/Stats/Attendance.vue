<template>
    <div class="w-full">
        <canvas ref="titheChart"></canvas>
    </div>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { Chart, registerables } from 'chart.js';

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
        tension: number
    }[];
}

// Dummy data for demonstration purposes
const chartData: ChartData = {
    labels: props.attendances.map(attendance => attendance.label),
    datasets: [
        {
            label: 'Attendances',
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            data: props.attendances.map(tithe => tithe.total),
            tension: 0.4
        }
    ]
};

const titheChart = ref<HTMLCanvasElement | null>(null);

watchEffect(() => {
    if (titheChart.value) {
        new Chart(titheChart.value.getContext('2d')!, {
            type: 'bar',
            data: chartData,
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Attendances'
                    }
                },
                responsive: true,
                scales: {
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'People'
                        },
                        beginAtZero: true,
                        ticks: {
                            major: { enabled: true },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
