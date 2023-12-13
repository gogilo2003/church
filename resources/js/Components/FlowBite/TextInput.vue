<script setup lang="ts">
import { mergeProps, onMounted, ref } from 'vue';
import InputError from '../InputError.vue'

defineOptions({
    inheritAttrs: false
})

defineProps({
    modelValue: Number | String,
    error: String,
    label: String,
    hasIcon: { type: Boolean, default: false }
});

defineEmits(['update:modelValue']);

const input = ref(null);
const inputName = ref('')

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
    <div class="relative z-0 group">
        <input v-bind="$attrs"
            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
            :class="{ 'pl-6': hasIcon }" placeholder=" " :value="modelValue"
            @input="$emit('update:modelValue', $event?.target?.value)" />
        <label :for="$attrs.id"
            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            :class="{ 'pl-6 peer-focus:pl-0': hasIcon }" v-text="label"></label>
        <InputError :message="error" />
    </div>
</template>
