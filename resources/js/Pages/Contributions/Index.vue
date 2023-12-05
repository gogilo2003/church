<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import Icon from '../../Components/Icons/Icon.vue';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import InputError from '../../Components/InputError.vue';
import InputLabel from '../../Components/InputLabel.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Modal from '../../Components/Modal.vue';
import Container from '../../Components/Custom/Container.vue';
import Swal from 'sweetalert2';

interface Contribution {
    id: Number,
    title: String
}
const props = defineProps({
    contribution_types: Object,
    notification: Object
})
const form = useForm({
    id: null,
    description: "",
    recurrent: null,
    recurrence_value: null,
    recurrence_unit: null,
    deadline: null,
    amount: null,
    back_date: null,
})

const showDialog = ref(false)
const edit = ref(false)
const dialogTitle = ref("Add Contribution")

const closeDialog = () => {
    showDialog.value = false
}

const newContribution = () => {
    dialogTitle.value = "Add Contribution"
    showDialog.value = true
    edit.value = false
}

const editContribution = (contributionType) => {
    dialogTitle.value = "Add Contribution"
    showDialog.value = true
    edit.value = true

    form.id = contributionType.id
    form.description = contributionType.description
}
const deleteContribution = (contributionType) => {
    dialogTitle.value = "Add Contribution"
    showDialog.value = true
    edit.value = true

    form.id = contributionType.id
    form.description = contributionType.description
}

const cancel = () => {
    showDialog.value = false
    form.id = null
    form.description = ""
    form.recurrent = null
    form.recurrence_value = null
    form.recurrence_unit = null
    form.deadline = null
    form.amount = null
    form.back_date = null
}

const submit = () => {
    form.post(route('setup-contributions-store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: props?.notification?.success
            })
            cancel()
        }, onError: () => {
            Swal.fire({
                icon: 'error',
                text: 'An error occurred'
            })
        },
        only: ['errors', 'contribution_types', 'notification']
    })
}
</script>
<template>
    <Modal :show="showDialog">
        <div class="p-3 flex justify-between">
            <div class="font-semibold" v-text="dialogTitle"></div>
            <button @click="closeDialog">
                <Icon type="times" />
            </button>
        </div>
        <div class="m-4">
            <form @submit.prevent="submit">
                <div class="mb-6">
                    <TextInput id="inputDescription" label="Description" :error="form.errors.description"
                        v-model="form.description" />
                </div>
                <div class="mb-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Toggle me</span>
                    </label>
                </div>
                <div class="flex gap-3 py-4">
                    <PrimaryButton>Save</PrimaryButton>
                    <SecondaryButton @click.prevent="cancel">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
    <AppLayout title="Contributions">
        <template #header>
            <div>Contributions</div>
        </template>
        <Container>
            <div class="p-6">
                <div class="py-3">
                    <PrimaryButton @click="newContribution">New Contribution</PrimaryButton>
                </div>
                <div class="flex flex-col gap-3">
                    <div v-for="contribution in contribution_types?.data"
                        class="shadow p-3 rounded-lg border flex flex-col lg:flex-row items-start gap-2 md:justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="flex-1">
                                <div class="text-base font-semibold uppercase" v-text="contribution.title">
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1 self-start lg:self-end">
                            <SecondaryButton @click="editContribution(contribution)">
                                <div class="flex gap-1">
                                    <Icon type="edit" class="h-4 w-4" /><span class="hidden lg:inline-flex">Edit</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton class="text-red-500" @click="deleteContribution(contribution)">
                                <div class="flex gap-1">
                                    <Icon type="delete" class="h-4 w-4" /><span class="hidden lg:inline-flex">Delete</span>
                                </div>
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <pre v-text="contribution_types?.data"></pre>
                </div>
            </div>
        </Container>
    </AppLayout></template>