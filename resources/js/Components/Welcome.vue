<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import { iStat } from '../types';

defineProps<{
    stats: iStat[]
}>()
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900" v-text="`Welcome to ${usePage().props.appName}`"></h1>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4 gap-6 lg:gap-8 p-6 lg:p-8">
            <div class="p-6 rounded-xl border shadow-lg" v-for="{ name, value } in stats">
                <div class="text-lg font-light uppercase" v-text="name"></div>
                <div class="text-sm font-medium text-gray-800" v-text="value"></div>
            </div>
        </div>
    </div>
</template>
