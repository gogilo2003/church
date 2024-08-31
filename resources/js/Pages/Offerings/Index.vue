<script lang="ts" setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { iOfferings, iOffering, iNotification, iOfferingType } from '../../types';
import Paginator from '../../Components/Paginator.vue'
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Icon from '../../Components/Icons/Icon.vue';
import Model from '../../Components/Modal.vue'
import { ref, watch, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import TextInput from '../../Components/FlowBite/TextInput.vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import { formatCurrency, prepDate } from '../../helpers';
import SelectInput from '../../Components/FlowBite/SelectInput.vue';
import Container from '../../Components/Custom/Container.vue';
import { format } from 'date-fns';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps<{
    offerings: iOfferings,
    search?: string | null
    notification?: iNotification,
    types: iOfferingType[]
}>()

const searchVal = ref<string | null | undefined>(props.search)

watch(() => searchVal.value, (value) => {
    let options: { search?: string }
    if (value) {
        options = { search: value }
    }

    router.get(route('accounts-offerings'), options, {
        preserveScroll: true,
        preserveState: true,
        only: ['offerings', 'search']
    })
})

const form = useForm<{
    id: number | null
    offering_date: string
    amount: number | null
    type: number | null
}>({
    id: null,
    offering_date: '',
    amount: null,
    type: null
})
const show = ref<boolean>(false)

const addOffering = () => {
    form.id = null
    form.offering_date = null
    form.amount = null
    form.type = null
    show.value = true
}

const editOffering = (offering: iOffering) => {
    form.id = offering.id
    form.offering_date = prepDate(offering.offering_date)
    form.amount = offering.amount
    form.type = offering.type?.id
    show.value = true
}

const close = () => {
    form.id = null
    form.offering_date = null
    form.amount = null
    form.clearErrors()
    show.value = false
}

const title = computed(() => {
    if (form.id) {
        return 'Edit Offering'
    }
    return 'Add Offering'
})

const submit = () => {

    if (form.id) {
        form.patch(route('accounts-offerings-update', form.id), {
            only: ['offerings', 'errors', 'notification'],
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
                    text: props?.notification?.danger ?? 'An error occurred! please check your fields and try again'
                })

            }
        })
    } else {
        form.post(route('accounts-offerings-store'), {
            only: ['offerings', 'errors', 'notification'],
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
                    text: props?.notification?.danger ?? 'An error occurred! please check your fields and try again'
                })

            }
        })
    }
}

const showType = ref<boolean>(false)

const typeForm = useForm<{
    id: number | null
    name: string | null
}>({
    id: null,
    name: null
})

const addType = () => {
    showType.value = true
}

const closeType = () => {
    typeForm.clearErrors()
    typeForm.name = null
    showType.value = false
}

const submitType = () => {
    let oldTypes: Array<iOfferingType> = props.types.map(item => item.value)
    typeForm.post(route('accounts-offering-types-store'), {
        only: ['types', 'errors', 'notification'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            let addition = props.types.map(item => item.value).filter(item => !oldTypes.includes(item))
            if (addition.length) {
                form.type = addition[0]
            }

            Swal.fire({
                icon: 'success',
                text: props?.notification?.success
            })
            closeType()
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                text: props?.notification?.danger ?? 'An error occurred! please check your fields and try again'
            })

        }
    })
}

const formatDate = date => {
    return format(date, 'eee, do MMM, yyyy')
}

</script>
<template>
    <Model :show="show">
        <div class="mx-6 mb-6 border-b py-3 flex items-center justify-between">
            <div class="text-uppercase text-gray-800 font-medium" v-text="title"></div>
            <button @click="close">
                <Icon class="h-5 w-5" type="times" />
            </button>
        </div>
        <div class="mx-6 py-4">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <div class="flex flex-row gap-2">
                        <div class="flex-1">
                            <SelectInput :options="types" id="inputOfferingType" label="Offering Type"
                                :error="form.errors.type" v-model="form.type" />
                        </div>
                        <div class="flex-nonept-2">
                            <SecondaryButton @click.prevent="addType" class="flex gap-2">
                                <Icon class="h-4 w-4" type="add" />
                                <span class="text-xs">Add Type</span>
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <InputLabel value="Offering Date" />
                    <VueDatePicker v-model="form.offering_date" :format="formatDate" />
                    <InputError :message="form.errors.offering_date" />
                </div>
                <div class="mb-4">
                    <TextInput id="inputAmount" label="Amount" :error="form.errors.amount" v-model="form.amount" />
                </div>
                <div class="flex items-center justify-between">
                    <PrimaryButton>Save</PrimaryButton>
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Model>
    <Model :show="showType">
        <div class="p-4">
            <form @submit.prevent="submitType">
                <div class="flex flex-row gap-2">
                    <div class="flex-1">
                        <TextInput id="inputTypeName" label="Offering Type Name" v-model="typeForm.name"
                            :error="typeForm.errors.name" />
                    </div>
                    <div class="flex flex-row items-center gap-2">
                        <PrimaryButton>
                            <Icon class="h-4 w-5" type="done" />
                            <span class="text-xs">Save</span>
                        </PrimaryButton>
                        <SecondaryButton @click.prevent="closeType">
                            <Icon class="h-4 w-5" type="times" />
                            <span class="text-xs">Cancel</span>
                        </SecondaryButton>
                    </div>
                </div>
            </form>
        </div>
    </Model>
    <AppLayout title="Offerings">
        <template #header>
            <div class="flex items-center justify-between flex-1">
                <div>Offerings</div>
            </div>
        </template>
        <Container>
            <div class="p-6">
                <PrimaryButton class="flex items-center gap-2" @click="addOffering">
                    <Icon class="h-5 w-5" type="add" />
                    <span>New Offering</span>
                </PrimaryButton>
                <div class="flex gap-2 flex-col mt-6">
                    <div v-for="offering in offerings.data"
                        class="p-3 shadow bg-gray-50 border rounded-lg flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium"
                                v-text="`${offering.offering_date}${offering?.type?.name ? ' - ' : ''}${offering?.type?.name ?? ''}`">
                            </div>
                            <div class="font-light" v-text="formatCurrency(offering.amount)"></div>
                        </div>
                        <div>
                            <SecondaryButton @click="editOffering(offering)" class="flex items-center gap-2 p-2">
                                <Icon class="h-4 w-4" type="edit" />
                                <span class="text-xs">Edit</span>
                            </SecondaryButton>
                        </div>
                    </div>
                    <Paginator :items="offerings" />
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
