<script setup lang="ts">
import { mergeProps, onMounted, ref } from 'vue';
import InputError from '../InputError.vue'

defineOptions({
    inheritAttrs: false
})

defineProps<{
    modelValue: string | number,
    error: string,
    label: string,
    options: any[]
}>();

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
    <!-- <input ref="input" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue" @input="$emit('update:modelValue', $event?.target?.value)"> -->
    <div class="relative z-0  mb-6 group">
        <!-- <input v-bind="$attrs"
            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
            placeholder=" " :value="modelValue" @input="$emit('update:modelValue', $event?.target?.value)" /> -->


        <!-- <label for="underline_select" class="sr-only">Underline select</label> -->
        <select v-bind="$attrs" id="underline_select"
            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
            :value="modelValue" @input="$emit('update:modelValue', $event?.target?.value)">
            <option v-for="(option, index) in options" :value="option?.value ?? index" v-text="option?.text ?? option"
                :selected="option?.selected ?? index == 0">
            </option>
        </select>
        <label :for="$attrs.id"
            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            v-text="label"></label>
        <InputError :message="error" />
    </div>
</template>
