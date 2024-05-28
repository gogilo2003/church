<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import Modal from '../../Components/Modal.vue'
import { ref } from "vue";
import PrimaryButton from '../../Components/PrimaryButton.vue'
import Icon from '../../Components/Icons/Icon.vue';
import Swal from 'sweetalert2'
import { useForm, router, usePage, Link } from '@inertiajs/vue3';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import SelectInput from '../../Components/FlowBite/SelectInput.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import InputLabel from '../../Components/InputLabel.vue';
import InputError from '../../Components/InputError.vue';
import SecondaryLink from '../../Components/SecondaryLink.vue';
import { iAttendances, iNotification } from '../../types';
import Paginator from '../../Components/Paginator.vue';

const props = defineProps<{
    attendances: iAttendances,
    notification: iNotification
}>()

const form = useForm({
    id: null,
    title: null,
    attendance_date: null,
})

const showDialog = ref(false)
const titleDialog = ref('New Attendance')
const edit = ref(false)

const editAttendance = (attendance: any) => {

    form.id = attendance.id
    form.title = attendance.title
    form.attendance_date = attendance.attendance_date

    edit.value = true
    titleDialog.value = `Edit Attendance (${attendance.first_name} ${attendance.last_name})`
    showDialog.value = true
}

const closeDialog = () => {
    edit.value = false
    titleDialog.value = "New Attendance"
    form.reset()
    showDialog.value = false
}



const showViewDialog = ref(false)
const selectedAttendance = ref()

const deleteAttendance = attendance => {
    router.delete(route('attendance-delete', attendance.id), {
        onSuccess: () => {
            if (props?.notification?.success) {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    text: props?.notification?.danger
                })
            }
        }
    })
}

const submit = () => {
    if (edit.value) {
        form.patch(route('attendance-update', form.id), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
            }
        })
    } else {
        form.post(route('attendance-store'), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
            }
        })
    }
}

</script>

<template>
    <AppLayout title="Attendances">
        <template #header>
            Attendances
        </template>

        <Container>
            <div class="p-6">
                <div class="py-3">
                    <PrimaryButton @click="showDialog = true" class="flex items-center gap-2">
                        <Icon class="h-5 w-5" type="add" />
                        <span>New Attendance</span>
                    </PrimaryButton>
                </div>
                <div class="flex flex-col gap-3">
                    <div v-for="attendance in attendances.data"
                        class="shadow p-3 rounded-lg border flex flex-col lg:flex-row items-start gap-2 md:justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="flex-1">
                                <div class="text-base font-semibold uppercase" v-text="attendance.title">
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span v-text="attendance.attendance_date"></span>&nbsp;|&nbsp;
                                    <span v-text="`${attendance.members.length} members`"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1 self-start lg:self-end">
                            <SecondaryButton :type="Link" :href="route('attendance-mark', attendance?.id)">
                                <div class="flex gap-1">
                                    <Icon type="id-card" class="h-4 w-4" /><span
                                        class="hidden lg:inline-flex">Mark</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton @click="editAttendance(attendance)">
                                <div class="flex gap-1">
                                    <Icon type="edit" class="h-4 w-4" /><span class="hidden lg:inline-flex">Edit</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton class="text-red-500" @click="deleteAttendance(attendance)">
                                <div class="flex gap-1">
                                    <Icon type="delete" class="h-4 w-4" /><span
                                        class="hidden lg:inline-flex">Delete</span>
                                </div>
                            </SecondaryButton>
                        </div>
                    </div>
                    <Paginator :items="attendances" />
                </div>
            </div>
        </Container>
    </AppLayout>

    <Modal :show="showDialog">
        <form @submit.prevent="submit" class="p-6">
            <div class="mb-4">
                <InputLabel value="Title" />
                <TextInput v-model="form.title" />
                <InputError :message="form.errors.title" />
            </div>
            <div class="mb-4">
                <InputLabel value="Date" />
                <!-- <TextInput type="date" v-model="form.attendance_date" /> -->
                <VueDatePicker />
                <InputError :message="form.errors.attendance_date" />
            </div>
            <div class="flex justify-between">
                <PrimaryButton>Save</PrimaryButton>
                <SecondaryButton @click.prevent="closeDialog">Cancel</SecondaryButton>
            </div>
        </form>
        <!--
        Submit the start of attendance register to store route
        which redirects to show edit route where a selected attendance is marked for individual member
        the form saves the attendances being marked to the localStorage to remember what has been selected
        and unselected on the browser. This is then used to update attendance status to the server.
        Local storage is also pre-populated when attendance is loaded just to keep things in sync.
     -->
    </Modal>
</template>
