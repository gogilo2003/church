<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import Icon from '../../Components/Icons/Icon.vue';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Modal from '../../Components/Modal.vue';
import Container from '../../Components/Custom/Container.vue';
import Swal from 'sweetalert2';
import InputError from '../../Components/InputError.vue';
import ContributionType from '../../Components/Church/ContributionType.vue';

const props = defineProps({
    contribution_types: Object,
    notification: Object
})

const form = useForm({
    id: null,
    description: "",
    recurrent: false,
    recurrence_value: 0,
    recurrence_unit: "",
    deadline: "",
    amount: 0,
    back_date: false,
    autoenroll: false,
})

const showDialog = ref(false)
const edit = ref(false)
const dialogTitle = ref("Add Contribution")

const closeDialog = () => {
    showDialog.value = false
}

const newContribution = () => {
    cancel()
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
    form.recurrent = contributionType.recurrent ? true : false
    form.recurrence_unit = contributionType.recurrence_unit
    form.recurrence_value = contributionType.recurrence_value ? contributionType.recurrence_value.toString() : null
    form.deadline = contributionType.deadline
    form.amount = contributionType.amount
    form.back_date = contributionType.back_date
    form.autoenroll = contributionType.autoenroll
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
    form.recurrent = false
    form.recurrence_value = 0
    form.recurrence_unit = ""
    form.deadline = ""
    form.amount = 0
    form.back_date = false
    form.autoenroll = false
}

const submit = () => {
    if (edit.value) {
        form.patch(route('contributions-update', form.id), {
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
    } else {
        form.post(route('contributions-store'), {
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
                <div class="mb-6 flex gap-4">
                    <label class="relative inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" value="" v-model="form.recurrent" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                        <span
                            class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">Recurrent</span>
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" value="" v-model="form.back_date" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">Back Date
                            to Registration
                            date</span>
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" value="" v-model="form.autoenroll" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">Auto
                            Enroll Members</span>
                    </label>
                </div>
                <div v-if="form.recurrent" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="relative group">
                        <label for="recurrenceUnit"
                            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Recurrence
                            Unit</label>
                        <select id="recurrenceUnit"
                            class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer"
                            v-model="form.recurrence_unit">
                            <option selected>Choose a unit</option>
                            <option value="day">Daily</option>
                            <option value="week">Weekly</option>
                            <option value="month">Monthly</option>
                            <option value="year">Yearly</option>
                        </select>
                        <InputError :message="form.errors.recurrence_unit" />
                    </div>
                    <div class="mb-6">
                        <TextInput type="number" id="inputRecurrenceValue" label="Recurrence Value"
                            :error="form.errors.recurrence_value" v-model="form.recurrence_value" />
                    </div>

                </div>
                <div class="relative grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                    <div class="relative z-0 group">
                        <label for="deadline"
                            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Deadline</label>
                        <VueDatePicker id="deadline" v-model="form.deadline"
                            input-class-name="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <InputError :message="form.errors.deadline" />
                    </div>
                    <div>
                        <TextInput label="Amount" :error="form.errors.amount" v-model="form.amount" />
                    </div>
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
                        class="shadow py-4 px-6 rounded-xl border flex flex-col lg:flex-row items-start gap-2 md:justify-between">
                        <ContributionType :item="contribution" />
                        <div class="flex gap-1 self-start lg:self-end">
                            <Link
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-full font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                :href="route('contributions-show', contribution.id)">
                            <div class="flex gap-1">
                                <Icon type="show" class="h-4 w-4" /><span class="hidden lg:inline-flex">Details</span>
                            </div>
                            </Link>
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

            </div>
        </Container>
    </AppLayout></template>
