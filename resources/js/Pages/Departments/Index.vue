<script setup lang="ts">
import Container from '../../Components/Custom/Container.vue';
import Icon from '../../Components/Icons/Icon.vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import Modal from '../../Components/Modal.vue';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { iNotification, iDepartment } from '../../types';
import { ref } from 'vue';

const props = defineProps<{
    departments: iDepartment[];
    notification?: iNotification;
}>()

const form = useForm<{
    id: number | null;
    title: string;
}>({
    id: null,
    title: ""
})
const showDialog = ref(false)
const dialogTitle = ref('New Department')
const edit = ref(false)

const editDepartment = (department: iDepartment) => {
    form.id = department.id
    form.title = department.title
    edit.value = true
    showDialog.value = true
    dialogTitle.value = "Edit Department"
}
const deleteDepartment = (department: iDepartment) => {
    Swal.fire({
        title: 'Delete Department',
        text: `Are you sure you want to delete "${department.title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('setup-departments-destroy', { id: department.id }), {
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        text: props?.notification?.success ?? 'Department deleted successfully'
                    })
                }
            })
        }
    })
}
const newDepartment = () => {
    form.reset()
    edit.value = false
    showDialog.value = true
    dialogTitle.value = "New Department"
}

const closeDialog = () => {
    showDialog.value = false
}

const cancel = () => {
    closeDialog()
}

const submit = () => {
    if (edit.value) {
        form.patch(route('setup-departments-update', { id: form.id }), {
            preserveScroll: true,
            preserveState: true,
            only: ['departments', 'notification', 'errors'],
            onSuccess: (res) => {
                Swal.fire({
                    icon: "success",
                    text: props?.notification?.success
                })
                closeDialog()
            },
            onError: (er) => {
                Swal.fire({
                    icon: "error",
                    text: props?.notification?.danger || "An error occurred"
                })
            }
        })
    } else {
        form.post(route('setup-departments-store'), {
            preserveScroll: true,
            preserveState: true,
            only: ['departments', 'notification', 'errors'],
            onSuccess: (res) => {
                Swal.fire({
                    icon: "success",
                    text: props?.notification?.success
                })
                closeDialog()
            },
            onError: (er) => {
                Swal.fire({
                    icon: "error",
                    text: props?.notification?.danger || "An error occurred"
                })
            }
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
                    <TextInput id="inputTitle" label="Title" :error="form.errors.title" v-model="form.title" />
                </div>
                <div class="flex gap-3 py-4">
                    <PrimaryButton>Save</PrimaryButton>
                    <SecondaryButton @click.prevent="cancel">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
    <AppLayout title="Departments">
        <template #header>
            <div>Departments</div>
        </template>
        <Container>
            <div class="p-6">
                <div class="py-3">
                    <PrimaryButton @click="newDepartment">New Department</PrimaryButton>
                </div>
                <div class="flex flex-col gap-3">
                    <div v-for="department in departments"
                        class="shadow p-3 rounded-lg border flex flex-col lg:flex-row items-start gap-2 md:justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="flex-1">
                                <div class="text-base font-semibold uppercase" v-text="department.title">
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1 self-start lg:self-end">
                            <SecondaryButton @click="editDepartment(department)">
                                <div class="flex gap-1">
                                    <Icon type="edit" class="h-4 w-4" /><span class="hidden lg:inline-flex">Edit</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton class="text-red-500" @click="deleteDepartment(department)">
                                <div class="flex gap-1">
                                    <Icon type="delete" class="h-4 w-4" /><span class="hidden lg:inline-flex">Delete</span>
                                </div>
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <pre v-text="departments"></pre>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
