<script lang="ts" setup>
import { Link } from '@inertiajs/vue3'
import Icon from '../../Components/Icons/Icon.vue'
import { computed } from 'vue';

const props = defineProps({
    link: Object,
    active: Boolean,
    toggle: {
        type: Boolean,
        default: false
    }
})

const classes = computed(() => {
    return route().current(props.link.name) || route().current().startsWith(props.link.name)
        ? 'bg-gray-100 text-gray-700 after:shadow-gray-100 before:shadow-gray-100'
        : 'before:shadow-transparent after:shadow-transparent'
})

</script>

<template>
    <Component :is="link.name? Link :'div'"
        class="relative before:pointer-events-none after:pointer-events-none w-full flex whitespace-nowrap items-center h-16 gap-4 py-0 pr-4 pl-0 rounded-l-full hover:bg-gray-100 hover:text-gray-700  before:w-[40px] before:h-[40px] before:absolute before:bg-transparent before:-top-[40px] before:right-0 before:shadow-[30px_30px_0_10px] hover:before:shadow-gray-100 before:rounded-full after:w-[40px] after:h-[40px] after:absolute after:bg-transparent after:-bottom-[40px] after:right-0 after:shadow-[30px_-30px_0_10px] hover:after:shadow-gray-100 after:rounded-full hover:before:pointer-events-none hover:after:pointer-events-none"
        :href="link.name ? route(link.name) : null" :class="classes">
        <div class="h-full w-16 flex-none">
            <Icon :type="link.icon" class="w-8 h-16 mx-4" />
        </div>
        <div class="h-full leading-[4rem] overflow-hidden flex-1 transition-all duration-500"
            :class="{ 'w-[calc(100%_-_4rem)]': toggle, 'w-0': !toggle }" v-text="link.caption">
        </div>
    </Component>
</template>
