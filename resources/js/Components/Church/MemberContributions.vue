<script lang="ts" setup>
import SecondaryButton from '../../Components/SecondaryButton.vue';
import { formatCurrency, formatDate } from '../../helpers';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

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
    member: Object
})

const registerForm = useForm({
    contribution_type: null,
    member: null,
    amount: null,
    end_at: "",
})

const page = usePage()

const enrollContribution = (contribution: iContribution) => {
    console.log(contribution.end_at);

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
    console.log(contribution);

}
</script>
<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                        <SecondaryButton v-if="!contribution?.id" @click="enrollContribution(contribution)" size="xs">
                            Enroll
                        </SecondaryButton>
                        <SecondaryButton v-if="contribution?.id" @click="makePayment(contribution)" size="xs">Pay
                        </SecondaryButton>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--
    <div class="flex items-center justify-between" v-for="contribution in member?.contributions">
        <div class="flex gap-3 divide-x text-xs text-gray-500 [&>*]:pl-3">
            <span v-if="contribution?.description" class="first:pl-0" v-text="contribution?.description"></span>
            <span v-if="contribution?.end_at" class="first:pl-0" v-text="formatDate(contribution?.end_at)"></span>
            <span v-if="contribution?.status" class="first:pl-0 uppercase font-medium text-white" :class="{
                'bg-slate-400': contribution?.status == 'pending',
                'bg-orange-400': contribution?.status == 'partial',
                'bg-lime-400': contribution?.status == 'paid',
                'bg-red-500': contribution?.status == 'late',
                'bg-gray-500': !contribution?.status,
            }" v-text="contribution?.status"></span>
            <span v-if="contribution?.amount" class="first:pl-0"
                v-text="`Amount: ${formatCurrency(contribution?.amount)}`"></span>
            <div v-if="contribution?.paid" class="first:pl-0">
                <span class="uppercase font-medium">Paid:</span>
                <span class="text-green-600" v-text="formatCurrency(contribution?.paid)"></span>
            </div>
            <div v-if="contribution?.balance" class="first:pl-0 flex gap-2">
                <span class="uppercase font-medium">Balance:</span>
                <span class="text-red-600" v-text="formatCurrency(contribution?.balance)"></span>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2">
            <SecondaryButton v-if="!contribution?.id" @click="enrollContribution(contribution)">Enroll
            </SecondaryButton>
            <SecondaryButton v-if="contribution?.id" @click="makePayment(contribution)">Pay</SecondaryButton>
        </div>
    </div>
     -->
</template>
