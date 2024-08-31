<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import Modal from '../../Components/Modal.vue'
import { ref } from "vue";
import PrimaryButton from '../../Components/PrimaryButton.vue'
import Icon from '../../Components/Icons/Icon.vue';
import { useForm, router } from '@inertiajs/vue3';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import SelectInput from '../../Components/FlowBite/SelectInput.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Photo from './Photo.vue';
import Show from './Show.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    members: Array,
    notification: Object
})

const form = useForm({
    id: null,
    first_name: null,
    last_name: null,
    phone: null,
    email: null,
    box_no: null,
    post_code: null,
    town: null,
    address: null,
    date_of_birth: null,
    gender: null,
})

const showDialog = ref(false)
const titleDialog = ref('New Member')
const edit = ref(false)

const submit = () => {
    if (edit.value) {
        form.patch(route('members-update', form.id), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
                closeDialog()
            },
            onError: () => {
                if (props?.notification?.danger) {
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        text: props?.notification?.success
                    })
                } else {
                    if (form.errors) {
                        Swal.fire({
                            title: "Validation Error",
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
    } else {
        form.post(route('members-store'), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
                closeDialog()
            },
            onError: () => {
                if (props?.notification?.danger) {
                    Swal.fire({
                        icon: 'error',
                        text: props?.notification?.success
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
}

const editMember = (member) => {

    form.id = member.id
    form.first_name = member.first_name
    form.last_name = member.last_name
    form.phone = member.phone
    form.email = member.email
    form.box_no = member.box_no
    form.post_code = member.post_code
    form.town = member.town
    form.address = member.address
    form.date_of_birth = member.date_of_birth
    form.gender = member.gender

    edit.value = true
    titleDialog.value = `Edit Member (${member.first_name} ${member.last_name})`
    showDialog.value = true
}

const closeDialog = () => {
    edit.value = false
    titleDialog.value = "New Member"
    form.reset()
    showDialog.value = false
}

const photoId = ref(null)
const showPhoto = ref(false)
const updatePhoto = (id) => {
    photoId.value = id
    showPhoto.value = true
}

const uploadingPhoto = () => { }
const uploadedPhoto = () => {
    photoId.value = null
    showPhoto.value = false
}

const showViewDialog = ref(false)
const selectedMember = ref()
const closeViewDialog = () => {
    selectedMember.value = null;
    showViewDialog.value = false
}
const openViewDialog = (member) => {
    selectedMember.value = member
    showViewDialog.value = true
}

const deleteMember = member => {
    router.delete(route('members-delete', member.id), {
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

const downloadMembers = () => {

}
</script>

<template>
    <Photo :show="showPhoto" :member-id="photoId" @uploading="uploadingPhoto" @uploaded="uploadedPhoto"
        @close="uploadedPhoto" />
    <Show :show="showViewDialog" :member="selectedMember" @close="closeViewDialog" />
    <AppLayout title="Members">
        <template #header>
            Members
        </template>

        <Container>
            <div class="p-6">
                <div class="py-3 flex items-center gap-2">
                    <PrimaryButton class="flex items-center gap-1" @click="showDialog = true">
                        <Icon class="h-5 w-5" type="add" />
                        New Member
                    </PrimaryButton>
                    <SecondaryButton @click="downloadMembers">
                        <Icon class="h-5 w-5" type="download" />
                        Download
                    </SecondaryButton>
                </div>
                <div class="flex flex-col gap-3">
                    <div v-for="member in members"
                        class="shadow p-3 rounded-lg border flex flex-col lg:flex-row items-start gap-2 md:justify-between">
                        <div class="flex gap-2 items-center">
                            <img :src="member.photo_url" class="flex-none w-16 h-16 object-cover" />
                            <div class="flex-1">
                                <div class="text-base font-semibold uppercase"
                                    v-text="`${member.first_name} ${member.last_name}`">
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span v-text="member.phone"></span>&nbsp;|&nbsp;
                                    <span v-text="member.email"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1 self-start lg:self-end">
                            <SecondaryButton @click="updatePhoto(member.id)">
                                <div class="flex gap-1">
                                    <Icon type="image" class="h-4 w-4" /><span
                                        class="hidden lg:inline-flex">Photo</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton>
                                <div class="flex gap-1" @click="openViewDialog(member)">
                                    <Icon type="id-card" class="h-4 w-4" /><span
                                        class="hidden lg:inline-flex">View</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton @click="editMember(member)">
                                <div class="flex gap-1">
                                    <Icon type="edit" class="h-4 w-4" /><span class="hidden lg:inline-flex">Edit</span>
                                </div>
                            </SecondaryButton>
                            <SecondaryButton class="text-red-500" @click="deleteMember(member)">
                                <div class="flex gap-1">
                                    <Icon type="delete" class="h-4 w-4" /><span
                                        class="hidden lg:inline-flex">Delete</span>
                                </div>
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
    <Modal :show="showDialog">
        <div class="p-3 flex items-center justify-between text-gray-700 border-b">
            <div class="font-semibold uppercase" v-text="titleDialog"></div>
            <Icon type="times" @click="closeDialog" class="cursor-pointer" />
        </div>
        <div class="p-4">
            <form @submit.prevent="submit">
                <div class="grid gap-2 grid-cols-1 md:grid-cols-2 mb-6">
                    <TextInput id="inputFirstName" label="First Name" :error="form.errors.first_name"
                        v-model="form.first_name" />
                    <TextInput id="inputLastName" label="Last Name" :error="form.errors.last_name"
                        v-model="form.last_name" />
                </div>
                <div class="grid gap-2 grid-cols-1 md:grid-cols-2 mb-6">
                    <TextInput id="inputPhone" label="Phone" :error="form.errors.phone" v-model="form.phone" />
                    <TextInput id="inputEmail" label="Email" :error="form.errors.email" v-model="form.email" />
                </div>
                <div class="grid gap-2 grid-cols-1 md:grid-cols-2 mb-6">
                    <TextInput id="inputDateOfBirth" label="Date Of Birth" :error="form.errors.date_of_birth"
                        v-model="form.date_of_birth" />
                    <SelectInput id="inputGender" label="Gender" :error="form.errors.gender" v-model="form.gender"
                        :options="['Male', 'Female']" />
                </div>
                <div class="grid gap-2 grid-cols-1 md:grid-cols-3 mb-6">
                    <TextInput id="inputBoxNo" label="Box No" :error="form.errors.box_no" v-model="form.box_no" />
                    <TextInput id="inputPostCode" label="Post Code" :error="form.errors.post_code"
                        v-model="form.post_code" />
                    <TextInput id="inputTown" label="Town" :error="form.errors.town" v-model="form.town" />
                </div>
                <TextInput class="mb-6" id="inputAddress" label="Address" :error="form.errors.address"
                    v-model="form.address" />
                <div class="flex justify-between">
                    <PrimaryButton :class="{ 'opacity-30': form.processing }" :disabled="form.processing" type="submit">
                        Save
                    </PrimaryButton>
                    <SecondaryButton @click="closeDialog">Cancel</SecondaryButton>
                </div>
            </form>
        </div>

    </Modal>
</template>
