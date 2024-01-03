<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    size: {
        type: String,
        default: 'md'
    }
});

const classes = computed(() => {
    if (props.size == 'xs') {
        return "px-3 py-1 text-xs"
    }
    if (props.size == 'sm') {
        return "px-3 py-2 text-sm"
    }
    if (props.size == 'md') {
        return "px-4 py-3 text-base"
    }
    if (props.size == 'lg') {
        return "px-5 py-4 text-lg"
    }
    if (props.size == 'xl') {
        return "px-6 py-5 text-xl"
    }
    if (props.size == '2xl') {
        return "px-7 py-6 text-2xl"
    }
    if (props.size == '3xl') {
        return "px-8 py-7 text-3xl"
    }
})
</script>

<template>
    <button :type="type"
        class="inline-flex items-center  bg-gray-800 border border-transparent rounded-full font-semibold text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        :class="classes">
        <slot />
    </button>
</template>
