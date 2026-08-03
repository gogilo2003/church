<script setup lang="ts">
import { ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        debounceMs?: number;
    }>(),
    {
        modelValue: '',
        placeholder: 'Search...',
        debounceMs: 300,
    }
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'search', value: string): void;
}>();

const searchTerm = ref(props.modelValue);
let timer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => props.modelValue,
    (newVal) => {
        searchTerm.value = newVal;
    }
);

const handleInput = () => {
    emit('update:modelValue', searchTerm.value);
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => {
        emit('search', searchTerm.value);
    }, props.debounceMs);
};

const clear = () => {
    searchTerm.value = '';
    emit('update:modelValue', '');
    emit('search', '');
};
</script>

<template>
    <div class="relative w-full max-w-xs">
        <input
            v-model="searchTerm"
            @input="handleInput"
            type="text"
            :placeholder="placeholder"
            class="w-full pl-9 pr-7 py-1.5 text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 transition"
        />
        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <button
            v-if="searchTerm"
            @click="clear"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
