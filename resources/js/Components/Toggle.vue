<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits<{
    (e: 'update:checked', value: (string | number)[] | boolean): void;
}>();

const props = withDefaults(defineProps<{
    checked?: (string | number)[] | boolean;
    value?: string | null;
    label?: string | null;
}>(), {
    checked: false,
    value: null,
    label: null,
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
    <label class="relative inline-flex items-center cursor-pointer">
        <input v-model="proxyChecked" type="checkbox" :value="value" class="sr-only peer">
        <div
            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
        </div>
        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300" v-text="label"></span>
    </label>
</template>
