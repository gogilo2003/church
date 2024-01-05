<script lang="ts" setup>
import { formatCurrency, formatDate } from '../../helpers';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import Modal from '../Modal.vue';
import { ref, computed } from 'vue';
import Icon from '../Icons/Icon.vue';
import TextInput from '../FlowBite/TextInput.vue';
import SelectInput from '../FlowBite/SelectInput.vue';
import PrimaryButton from '../PrimaryButton.vue';
import SecondaryButton from '../SecondaryButton.vue';
import InputError from '../InputError.vue';


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
    amount: "",
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
const showEnrollmentDialog = ref(false)
const hasDescription = computed(() => props?.member?.contributions.filter((item) => item.description).length)

const showEnrollDialog = (contribution: iContribution) => {
    registerForm.contribution_type = props?.contribution_type?.id
    registerForm.member = props.member?.id
    registerForm.amount = props?.contribution_type?.amount || contribution?.amount || ""
    registerForm.end_at = contribution?.end_at

    showEnrollmentDialog.value = true
}

const enrollContribution = () => {
    registerForm.post(route('contributions-register'), {
        only: ['notification', 'errors', 'contribution_type', 'members'],
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: page.props.notification?.success || 'Contribution enrolled'
            })
            emit('enrolled')
            closeEnrollment()
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
    paymentForm.details = props?.contribution_type?.description
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
            closePayment();
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
    paymentForm.contribution = null
    paymentForm.amount = null
    paymentForm.details = null
    paymentForm.mode = 'cash'
    showPaymentDialog.value = false
}

const closeEnrollment = () => {
    registerForm.contribution_type = null
    registerForm.member = null
    registerForm.amount = ""
    registerForm.end_at = ""
    showEnrollmentDialog.value = false
}

</script>
<template>
    <Modal :show="show" max-width="4xl">
        <!-- Pay for contribution -->
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
                    <div class="px-4 py-3">
                        <div class="mb-6">
                            <SelectInput label="Mode of Payment" :error="paymentForm.errors.mode" v-model="paymentForm.mode"
                                class="w-full"
                                :options="[{ value: 'cash', text: 'Cash' }, { value: 'mpesa', text: 'MPesa' }, { value: 'cheque', text: 'Cheque' }]" />
                        </div>
                        <div class="mb-6">
                            <TextInput v-if="paymentForm.mode == 'mpesa'" label="Receipt Number"
                                :error="paymentForm.errors.receipt_number" v-model="paymentForm.receipt_number"
                                class="w-full" />
                        </div>
                        <div class="mb-6">
                            <TextInput label="Amount" :error="paymentForm.errors.amount" v-model="paymentForm.amount"
                                class="w-full" />
                        </div>
                        <div class="mb-6">
                            <TextInput label="Details" :error="paymentForm.errors.details" v-model="paymentForm.details"
                                class="w-full" />
                        </div>
                    </div>
                    <div class="flex justify-between px-4 py-3 border-t">
                        <PrimaryButton :disabled="paymentForm.processing" :class="{ 'opacity-30': paymentForm.processing }">
                            Pay</PrimaryButton>
                        <SecondaryButton @click="closePayment">Cancel</SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Enrollment for Contribution -->
        <Modal :show="showEnrollmentDialog" max-width="md">
            <div class="px-4 py-3 flex">
                <div class="flex-1">
                    <div class="text-lg text-medium text-gray-800">Enroll for Contribution</div>
                </div>
                <div class="flex-none">
                    <Icon type="times" class=" cursor-pointer text-gray-600" @click="closeEnrollment" />
                </div>
            </div>
            <div>
                <form @submit.prevent="enrollContribution">
                    <div class="px-4 py-3">
                        <div class="mb-6">
                            <TextInput label="Amount" :error="registerForm.errors.amount" v-model="registerForm.amount"
                                class="w-full" />
                        </div>
                        <div class="relative z-0 group">
                            <label for="end_at"
                                class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">End
                                Date</label>
                            <VueDatePicker id="end_at" v-model="registerForm.end_at"
                                input-class-name="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                            <InputError :message="registerForm.errors.end_at" />
                        </div>
                    </div>
                    <div class="flex justify-between px-4 py-3 border-t">
                        <PrimaryButton :disabled="registerForm.processing"
                            :class="{ 'opacity-30': registerForm.processing }">
                            Enroll</PrimaryButton>
                        <SecondaryButton @click="closeEnrollment">Cancel</SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <div class="px-6 py-3 flex">
            <div class="flex-1">
                <div class="text-lg uppercase font-semibold" v-text="contribution_type?.description"></div>
                <div class="text-sm capitalize font-medium text-gray-700"
                    v-text="`${member?.name} (${Math.round(member?.paid / member?.amount * 100) || 0}% | ${formatCurrency(member?.amount)})`">
                </div>
            </div>
            <div class="flex-none text-gray-500">
                <Icon @click="close" type="times" class=" cursor-pointer" />
            </div>
        </div>
        <div class="px-6 pb-6 pt-3">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                <table class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 w-full">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th v-if="hasDescription" scope="col" class="px-6 py-3">
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
                            <th v-if="hasDescription" scope="row"
                                class="px-2 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white"
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
                                <SecondaryButton v-if="!contribution?.id" @click="showEnrollDialog(contribution)" size="xs">
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
