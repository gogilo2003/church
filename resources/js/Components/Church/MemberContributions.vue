<script lang="ts" setup>
import SecondaryButton from '../../Components/SecondaryButton.vue';
import { formatCurrency, formatDate } from '../../helpers';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import Modal from '../Modal.vue';
import { ref } from 'vue';
import Icon from '../Icons/Icon.vue';
import TextInput from '../TextInput.vue';

interface iContribution {
    id: Number,
    contribution_date: string,
    end_at: string,
    description: string,
    amount: Number,
    paid: Number,
    balance: Number,
    status: String,
}

const props = defineProps({
    contribution_type: Object,
    member: Object,
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['closed', 'enrolled', 'paid'])

const registerForm = useForm({
    contribution_type: null,
    member: null,
    amount: null,
    end_at: "",
})

const paymentForm = useForm({
    contribution: null,
    amount: null,
    receipt_number: null,
    details: null,
    mode: "",
})

const page = usePage()

const showPaymentDialog = ref(false)

const enrollContribution = (contribution: iContribution) => {
    registerForm.contribution_type = props?.contribution_type?.id
    registerForm.member = props.member?.id
    registerForm.amount = props?.contribution_type?.amount || contribution?.amount || prompt("Enter amount")
    registerForm.end_at = contribution?.end_at

    registerForm.post(route('contributions-register'), {
        only: ['notification', 'errors', 'contribution_type', 'members'],
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: page.props.notification?.success || 'Contribution enrolled'
            })
            emit('enrolled')
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                text: "An error ocurred! Please try again."
            })
        },
        preserveScroll: true,
        preserveState: true,
    })
}

const makePayment = (contribution) => {
    paymentForm.contribution = contribution.id
    paymentForm.amount = contribution.balance
    paymentForm.details = contribution.details
    paymentForm.mode = 'cash'

    showPaymentDialog.value = true
}

const pay = () => {
    paymentForm.post(route('payments-store'), {
        only: ['notification', 'errors', 'contribution_type', 'members'],
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: page.props.notification?.success || 'Payment registered'
            })
            emit('enrolled')
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                text: "An error ocurred! Please try again."
            })
        },
        preserveScroll: true,
        preserveState: true,
    })
}

const close = () => {
    emit('closed')
}

const closePayment = () => {
    showPaymentDialog.value = false
}

</script>
<template>
    <Modal :show="show" max-width="3xl">
        <Modal :show="showPaymentDialog" max-width="md">
            <div class="px-4 py-3 flex">
                <div class="flex-1">
                    <div class="text-lg text-medium text-gray-800">Payment</div>
                </div>
                <div class="flex-none">
                    <Icon type="times" class=" cursor-pointer text-gray-600" @click="closePayment" />
                </div>
            </div>
            <div>
                <form @submit.prevent="pay">
                    <div>
                        <TextInput />
                    </div>
                </form>
            </div>
        </Modal>
        <div class="px-6 py-3 flex">
            <div class="flex-1" v-text="contribution_type.description"></div>
            <div class="flex-none text-gray-500">
                <Icon @click="close" type="times" class=" cursor-pointer" />
            </div>
        </div>
        <div class="px-6 pb-6 pt-3">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                <table class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 w-full">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Paid
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Balance
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contribution in member?.contributions"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-2 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                v-text="contribution?.description">
                            </th>
                            <td class="px-2 py-1" v-text="formatDate(contribution?.end_at)">
                            </td>
                            <td class="px-2 py-1 uppercase font-medium text-white text-center" :class="{
                                'bg-slate-400': contribution?.status == 'pending',
                                'bg-orange-400': contribution?.status == 'partial',
                                'bg-lime-400': contribution?.status == 'paid',
                                'bg-red-500': contribution?.status == 'late',
                                'bg-red-200': !contribution?.status,
                            }" v-text="contribution?.status || 'none'">
                            </td>
                            <td class="px-2 py-1 text-right" v-text="formatCurrency(contribution?.amount)">
                            </td>
                            <td class="px-2 py-1 text-right" v-text="formatCurrency(contribution?.paid)">
                            </td>
                            <td class="px-2 py-1 text-right" v-text="formatCurrency(contribution?.balance)">
                            </td>
                            <td class="px-2 py-1 text-right">
                                <SecondaryButton v-if="!contribution?.id" @click="enrollContribution(contribution)"
                                    size="xs">
                                    Enroll
                                </SecondaryButton>
                                <SecondaryButton v-if="contribution?.id" @click="makePayment(contribution)" size="xs">Pay
                                </SecondaryButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Modal>
</template>
