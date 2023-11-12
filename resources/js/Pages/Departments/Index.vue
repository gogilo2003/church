<script lang="ts" setup>
import Container from '../../Components/Custom/Container.vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Modal from '../../Components/Modal.vue';
import Icon from '../../Components/Icons/Icon.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import Swal from 'sweetalert2';


interface Department {
    id: Number,
    title: String
}

const props = defineProps({ departments: Array<Department>, notification: Object })

const form = useForm({
    id: null,
    title: ""
})
const showDialog = ref(false)
const dialogTitle = ref('New Department')

const editDepartment = (id) => {
    showDialog.value = true
    dialogTitle.value = "Edit Department"
}
const deleteDepartment = (id) => {

}
const newDepartment = (id) => {
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
    if (editDepartment.value) {
        form.patch(route('setup-departments-update'), {
            preserveScroll: true,
            preserveState: true,
            only: ['departments', 'notification', 'errors'],
            onSuccess: (res) => {
                console.log(res)
                Swal.fire({
                    icon: "success",
                    text: props?.notification?.success
                })
            },
            onError: (er) => {
                console.log(er);

                Swal.fire({
                    icon: "error",
                    text: props?.notification?.danger || "An error occured"
                })
            }
        })
    } else {
        form.post(route('setup-departments-store'), {
            preserveScroll: true,
            preserveState: true,
            only: ['departments', 'notification', 'errors'],
            onSuccess: (res) => {
                console.log(res);

                Swal.fire({
                    icon: "success",
                    text: props?.notification?.success
                })
            },
            onError: (er) => {
                console.log(er);

                Swal.fire({
                    icon: "error",
                    text: props?.notification?.danger || "An error occured"
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
