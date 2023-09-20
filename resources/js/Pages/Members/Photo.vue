<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from '../../Components/Modal.vue';
import Swal from 'sweetalert2';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import { ref } from 'vue'
import InputError from '../../Components/InputError.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';

const props = defineProps({
    memberId: Number,
    show: { type: Boolean, default: false }
})

const emit = defineEmits(['uploading', 'uploaded', 'close'])

const form = useForm({
    id: null,
    photo: null
})

const submit = () => {
    emit('uploading')
    form.id = props.memberId
    form.post(route('members-photo'), {
        onSuccess: () => {
            Swal.fire({
                text: usePage().props?.notification?.success
            })
            emit('uploaded')
        },
        onError: () => {
            if (usePage().props?.notification?.danger) {
                Swal.fire({
                    icon: 'error',
                    text: usePage().props?.notification?.success
                })
            } else {
                if (form.errors) {
                    Swal.fire({
                        title: 'Validation Error',
                        icon: 'warning',
                        text: 'Some fields failed validation! Please check and try again'
                    })
                } else {
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        text: "An error occurred! Please try again later"
                    })
                }
            }
        }
    })
}

const previewSrc = ref()

const selectPicture = (event) => {
    form.photo = event?.target?.files[0]
    if (form.photo) {
        const reader = new FileReader();

        reader.onload = (e) => {
            previewSrc.value = e?.target?.result;
        };

        reader.readAsDataURL(form.photo);
    }
}

const close = () => {
    form.reset()
    emit('close')
}

</script>
<template>
    <Modal :show="show" max-width="lg">
        <div class="p-6">
            <form @submit.prevent="submit" class="flex flex-col gap-3 items-center">

                <div class="flex items-center justify-center w-full ">
                    <label for="memberPhotoUpload"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <img v-if="form.photo" :src="previewSrc" alt="Preview" class="w-full h-full object-contain">
                        <div v-else class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to
                                    upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                        </div>
                        <input id="memberPhotoUpload" type="file" @input="selectPicture" class="hidden" />
                    </label>
                </div>
                <InputError :message="form.errors.photo" />
                <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                    class="bg-blue-600 h-1.5 rounded-full dark:bg-blue-500 w-full">
                    {{ form.progress.percentage }}%
                </progress>
                <div class="flex gap-2">
                    <PrimaryButton type="submit">Submit</PrimaryButton>
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
