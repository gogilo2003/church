<script setup lang="ts">
</script>

<template>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 p-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md shadow-sm mb-6">
        <div class="flex items-center gap-3 flex-1">
            <slot name="search" />
            <slot name="filters" />
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-3 self-end sm:self-auto">
            <slot name="actions" />
        </div>
    </div>
</template>
