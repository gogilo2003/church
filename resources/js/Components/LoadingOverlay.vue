<script setup lang="ts">
withDefaults(
    defineProps<{
        show?: boolean;
        message?: string;
    }>(),
    {
        show: false,
        message: 'Processing...',
    }
);
</script>

<template>
    <div v-if="show" class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center rounded-2xl transition-all">
        <div class="w-10 h-10 border-4 border-indigo-200 dark:border-indigo-900 border-t-indigo-600 dark:border-t-indigo-400 rounded-full animate-spin mb-3"></div>
        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ message }}</p>
    </div>
</template>
