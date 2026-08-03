<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        title: string;
        description?: string;
        backRoute?: string;
    }>(),
    {
        description: '',
        backRoute: '',
    }
);
</script>

<template>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <Link
                    v-if="backRoute"
                    :href="backRoute"
                    class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    title="Go Back"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ title }}
                </h1>
            </div>
            <p v-if="description" class="text-xs text-gray-500 dark:text-gray-400">
                {{ description }}
            </p>
        </div>

        <div v-if="$slots.actions" class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>
