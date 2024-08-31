<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import { onMounted } from 'vue';
import Swal from 'sweetalert2'
import { useForm } from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite'
import PrimaryButton from '../../Components/PrimaryButton.vue';
import SecondaryLink from '../../Components/SecondaryLink.vue';
import { iMember, iNotification, iAttendance } from '../../types';


const props = defineProps<{
    attendance: iAttendance,
    notification?: iNotification,
    members: iMember[]
}>()

const form = useForm({
    members: [],
})

const submit = () => {
    form.patch(route('attendance-mark-post', props?.attendance?.id), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: props?.notification?.success
            })
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                text: props?.notification?.danger ?? 'An error occurred! Please try again'
            })
        }
    })
}

onMounted(() => {
    form.members = props?.attendance?.members
    initFlowbite()
})
</script>

<template>
    <AppLayout title="View Attendance">
        <template #header>
            View Attendance
        </template>

        <Container>
            <form @submit.prevent="submit">
                <div class="p-6">
                    <h1 class="text-xl font-semibold uppercase" v-text="attendance?.title"></h1>
                    <div class="border-b mb-3 pb-3" v-text="attendance?.attendance_date"></div>
                    <div class="flex flex-col gap-3">

                        <div class="flex justify-between items-center shadow border rounded-lg p-2"
                            v-for="member in members">
                            <div class="flex gap-2 items-center">
                                <div class="h-16 w-16 rounded-full overflow-hidden flex-none">
                                    <img class="h-full w-full object-cover" :src="member?.photo" alt="">
                                </div>
                                <div class="flex-1">
                                    <div v-text="member?.name" class="font-semibold uppercase text-gray-700"></div>
                                    <div class="text-sm text-gray-500" v-text="`${member?.phone} | ${member?.email}`">
                                    </div>
                                </div>
                            </div>
                            <div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :value="member?.id" v-model="form.members"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-3 px-6 flex gap-2 justify-end">
                    <PrimaryButton v-if="form.members.length" :class="{ 'opacity-30': form.processing }"
                        :disabled="form.processing">Done</PrimaryButton>
                    <SecondaryLink :href="route('attendance')">Back to Attendances</SecondaryLink>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
