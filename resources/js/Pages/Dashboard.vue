<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Welcome from '../Components/Welcome.vue';
import Container from '../Components/Custom/Container.vue';
import { iStat } from '../types';
import TitheChart from '../Components/Church/Stats/Tithe.vue'
import AttendanceChart from '../Components/Church/Stats/Attendance.vue'

const props = defineProps<{
    stats: iStat[],
    tithes: { week: string, total: number }[]
    offerings: { week: string, total: number }[]
    contributions: { week: string, total: number }[]
    attendances: { week: string, total: number }[]
}>()
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            Dashboards
        </template>

        <Container>
            <Welcome :stats="stats" />
            <div class="grid gap-6 grid-cols-1 m-8 md:grid-cols-2">
                <div class="shadow rounded-xl bg-white p-6">
                    <TitheChart :offerings="offerings" :tithes="tithes" :contributions="contributions" />
                </div>
                <div class="shadow rounded-xl bg-white p-6">
                    <AttendanceChart :attendances="attendances" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
