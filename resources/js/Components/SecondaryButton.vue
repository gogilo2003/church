<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
    type?: string | Object
    size?: string
    severity?: string
}>();

const classes = computed(() => {
    let sizeClasses = ''
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
        sizeClasses = "h-10 px-3 py-2 text-base gap-3"
    }
    if (size == 'lg') {
        sizeClasses = "h-12 px-3 py-2 text-lg gap-3"
    }
    if (size == 'xl') {
        sizeClasses = "h-14 px-4 py-3 text-xl gap-4"
    }
    if (size == '2xl') {
        sizeClasses = "h-16 px-4 py-3 text-2xl gap-4"
    }
    if (size == '3xl') {
        sizeClasses = "h-20 px-5 py-4 text-3xl gap-5"
    }

    let severityClasses = '';
    let severity = props.severity;
    if (!props.severity) {
        severity = 'secondary'
    }

    if (severity == 'primary') {
        severityClasses = 'border-blue-300 text-blue-700 hover:bg-blue-50 focus:ring-indigo-500 bg-white'
    }
    if (severity == 'secondary') {
        severityClasses = 'border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-indigo-500 bg-white'
    }
    if (severity == 'warning') {
        severityClasses = 'border-yellow-300 text-yellow-700 hover:bg-yellow-50 focus:ring-indigo-500 bg-white'
    }
    if (severity == 'danger') {
        severityClasses = 'border-red-300 text-red-700 hover:bg-red-50 focus:ring-indigo-500 bg-white'
    }
    if (severity == 'success') {
        severityClasses = 'border-green-300 text-green-700 hover:bg-green-50 focus:ring-indigo-500 bg-white'
    }
    if (severity == 'info') {
        severityClasses = 'border-cyan-300 text-cyan-700 hover:bg-cyan-50 focus:ring-indigo-500 bg-white'
    }

    return `${sizeClasses} ${severityClasses}`
})

const buttonType = computed(() => {
    if (props.type) {
        return props.type
    }
    return 'button';
})


</script>

<template>
    <component :is="buttonType"
        class="my-2 inline-flex items-center border rounded-full font-semibold uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        :class="classes">
        <slot />
    </component>
</template>
