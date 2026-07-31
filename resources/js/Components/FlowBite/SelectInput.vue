<script setup lang="ts">
import { mergeProps, onMounted, ref } from 'vue';
import InputError from '../InputError.vue'

defineOptions({
    inheritAttrs: false
})

export interface SelectOption {
    value?: string | number;
    text?: string;
    selected?: boolean;
}

defineProps<{
    modelValue: string | number | null;
    error?: string;
    label?: string;
    options: Array<SelectOption | string | number>;
}>();

defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const input = ref<HTMLSelectElement | null>(null);
const inputName = ref('')

const getOptionValue = (option: SelectOption | string | number, index: number) => {
    if (typeof option === 'object' && option !== null) {
        return option.value ?? index;
    }
    return option;
};

const getOptionText = (option: SelectOption | string | number) => {
    if (typeof option === 'object' && option !== null) {
        return option.text ?? '';
    }
    return option;
};

const isOptionSelected = (option: SelectOption | string | number, index: number) => {
    if (typeof option === 'object' && option !== null) {
        return option.selected ?? index === 0;
    }
    return index === 0;
};

onMounted(() => {
    if (!mergeProps.name) {
        let inputBoxes = document.querySelectorAll('input[type="text"]')
        inputName.value = `text_${inputBoxes.length}`
        inputBoxes.forEach((box) => {
            console.log("Input Boxes:", box.getAttribute('name'))
        })
    }
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div class="relative z-0 mb-6 group">
        <select v-bind="$attrs" id="underline_select" ref="input"
            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
            :value="modelValue ?? undefined" @input="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)">
            <option v-for="(option, index) in options" :key="index" :value="getOptionValue(option, index)" v-text="getOptionText(option)"
                :selected="isOptionSelected(option, index)">
            </option>
        </select>
        <label :for="($attrs.id as string | undefined)"
            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            v-text="label"></label>
        <InputError :message="error" />
    </div>
</template>
