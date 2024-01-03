<script lang="ts" setup>
import ApplicationMark from './ApplicationMark.vue';
defineProps({
    toggle: {
        type: Boolean,
        default: true
    }
})
</script>
<template>
    <div class="flex items-center gap-4 text-xl w-full h-16">
        <div class="h-11 w-11 flex-none">
            <ApplicationMark />
        </div>
        <div v-show="toggle" class="whitespace-nowrap flex-1">Church
            <span class="font-semibold whitespace-nowrap">MS</span>
        </div>
    </div>
</template>
