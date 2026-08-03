<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        status?: string | boolean;
        type?: 'status' | 'system' | 'custom';
        customLabel?: string;
    }>(),
    {
        status: 'active',
        type: 'status',
    }
);

const badgeStyle = computed(() => {
    if (props.type === 'system') {
        return 'bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800';
    }

    if (props.type === 'custom') {
        return 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700';
    }

    const val = String(props.status).toLowerCase();
    if (val === 'active' || val === 'true') {
        return 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
    }

    if (val === 'suspended' || val === 'false') {
        return 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800';
    }

    return 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700';
});

const label = computed(() => {
    if (props.customLabel) return props.customLabel;
    if (props.type === 'system') return 'System Role';
    if (props.type === 'custom') return 'Custom Role';

    const val = String(props.status).toLowerCase();
    if (val === 'active') return 'Active';
    if (val === 'suspended') return 'Suspended';
    return String(props.status);
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border transition-colors',
            badgeStyle
        ]"
    >
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
        <span>{{ label }}</span>
    </span>
</template>
