<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
    type?: String
    size?: String
}>();

const classes = computed(() => {
    let sizeClasses = '';
    let severityClasses = '';

    let size = props.size

    if (!props.size) {
        size = 'md'
    }
    if (size == 'xs') {
        sizeClasses = "h-7 px-2 py-1 text-xs gap-1"
    }
    if (size == 'sm') {
        sizeClasses = "h-8 px-2 py-1 text-sm gap-1"
    }
    if (size == 'md') {
        sizeClasses = "h-10 px-3 py-2 text-base gap-1"
    }
    if (size == 'lg') {
        sizeClasses = "h-12 px-3 py-2 text-lg gap-2"
    }
    if (size == 'xl') {
        sizeClasses = "h-14 px-4 py-3 text-xl gap-3"
    }
    if (size == '2xl') {
        sizeClasses = "h-16 px-4 py-3 text-2xl gap-3"
    }
    if (size == '3xl') {
        sizeClasses = "h-20 px-5 py-4 text-3xl gap-4"
    }

    return `${sizeClasses} ${severityClasses}`
})
</script>

<template>
    <button :type="type"
        class="inline-flex items-center bg-gray-800 border border-transparent rounded-full font-semibold text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        :class="classes">
        <slot />
    </button>
</template>
