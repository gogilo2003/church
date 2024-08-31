<script lang="ts" setup>
import ApplicationMark from './ApplicationMark.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Define props with type safety
const props = defineProps<{
    toggle: boolean
}>();

const appName = usePage().props.appName

const toggleMenu = computed(() => props.toggle || false)
</script>

<template>
    <div class="flex items-center gap-4 text-xl w-full h-16">
        <div class="h-11 w-11 flex-none">
            <ApplicationMark />
        </div>
        <div v-show="toggleMenu" class="whitespace-nowrap flex-1">
            {{ appName }} <span class="font-semibold whitespace-nowrap">MS</span>
        </div>
    </div>
</template>
