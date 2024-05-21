<script lang="ts" setup>
import AppLayout from '../../Layouts/AppLayout.vue'
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Icon from '../../Components/Icons/Icon.vue'
import { iTithes, iNotification, iTithe } from '../../types';
import { ref, watch, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Model from '../../Components/Modal.vue'
import Swal from 'sweetalert2';
import { formatCurrency, prepDate } from '../../helpers';
import TextInput from '../../Components/FlowBite/TextInput.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue';
import Paginator from '../../Components/Paginator.vue';
import Container from '../../Components/Custom/Container.vue';

const props = defineProps<{
    tithes: iTithes
    search?: string
    notification?: iNotification
}>()

const searchVal = ref<string>()

watch(() => searchVal.value, (value) => {
    let options: { search?: string } = {}

    if (value) {
        options = { search: value }
    }

    router.get(route('accounts-tithes'), options, {
        preserveScroll: true,
        preserveState: true,
        only: ['tithes']
    })
})

const form = useForm<{
    id: number | null
    tithed_on: string | null
    amount: number | null
}>({
    id: null,
    tithed_on: null,
    amount: null
})

const show = ref<boolean>(false)

const title = computed(() => {
    if (form.id) {
        return 'Edit Tithe'
    }
    return 'Add Tithe'
})

const addTithe = () => {
    close()
    show.value = true
}

const editTithe = (tithe: iTithe) => {
    form.clearErrors()
    form.id = tithe.id
    form.tithed_on = prepDate(tithe.tithed_on)
    form.amount = tithe.amount
    show.value = true
}

const close = () => {
    form.clearErrors()
    form.id = null;
    form.tithed_on = null;
    form.amount = null;
    show.value = false
}
const submit = () => {
    if (form.id) {
        form.patch(route('accounts-tithes-update', form.id), {
            only: ['tithes', 'notification', 'errors'],
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
                close()
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    text: props?.notification?.danger ?? 'An Error occurred! Please try again.'
                })
            }
        })
    } else {
        form.post(route('accounts-tithes-store'), {
            only: ['tithes', 'notification', 'errors'],
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    text: props?.notification?.success
                })
                close()
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    text: props?.notification?.danger ?? 'An Error occurred! Please try again.'
                })
            }
        })
    }
}

</script>
<template>
    <Model :show="show">
        <div class="flex justify-between p-3 border-b mb-3">
            <div v-text="title"></div>
            <button @click="close">
                <Icon class="h-5 w-5" type="times" />
            </button>
        </div>
        <div class="p-4">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <TextInput :error="form.errors.tithed_on" type="date" v-model="form.tithed_on"
                        label="Date of Tithe" />
                </div>
                <div class="mb-4">
                    <TextInput :error="form.errors.amount" v-model="form.amount" label="Amount" />
                </div>
                <div class="flex items-center justify-between">
                    <PrimaryButton type="submit">Save</PrimaryButton>
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Model>
    <AppLayout title="Tithes">
        <template #header>
            <div class="flex justify-between items-center flex-1">
                <div class="flex-1">Tithes</div>
                <div class="flex-none">
                    <SecondaryButton class="flex items-center gap-2" @click="addTithe">
                        <Icon class="h-4 w-4" type="add" />
                        <span class="text-xs">Add Tithe</span>
                    </SecondaryButton>
                </div>
            </div>
        </template>
        <Container>
            <div class="p-6">
                <PrimaryButton class="flex items-center gap-2" @click="addTithe">
                    <Icon class="h-4 w-4" type="add" />
                    <span class="text-xs">Add Tithe</span>
                </PrimaryButton>
                <div class="flex flex-col gap-2 mt-6">
                    <div v-for="tithe in tithes.data"
                        class="flex flex-col md:flex-row justify-between items-center gap-2 shadow border bg-gray-50 rounded-lg p-3">
                        <div>
                            <div class="text-sm font-medium" v-text="tithe.tithed_on"></div>
                            <div class="text-lg font-light text-gray-800" v-text="formatCurrency(tithe.amount)"></div>
                        </div>
                        <div>
                            <SecondaryButton @click="editTithe(tithe)">
                                <Icon class="h-4 w-4" type="edit" />
                                <span class="text-xs">Edit</span>
                            </SecondaryButton>
                        </div>
                    </div>
                    <Paginator :items="tithes" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
