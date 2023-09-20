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
    <div class="flex items-center gap-4 text-xl w-full">
        <ApplicationMark class="h-11" />
        <div v-show="toggle" class="whitespace-nowrap">Church
            <span class="font-semibold whitespace-nowrap">MS</span>
        </div>
    </div>
</template>
