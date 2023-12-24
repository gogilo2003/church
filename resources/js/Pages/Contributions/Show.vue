<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import { formatDate, formatCurrency } from "../../helpers";
import SecondaryButton from '../../Components/SecondaryButton.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';

const props = defineProps({
    contribution_type: Object,
    notification: Object,
    errors: Object
})

const registerForm = useForm({
    contribution_type: null,
    member: null,
    amount: null
})

const amount = ref()

const enrollContribution = (contribution: Object) => {

    registerForm.contribution_type = props?.contribution_type?.id
    registerForm.member = contribution?.member?.id
    registerForm.amount = props?.contribution_type?.amount ?? prompt("Enter amount")

    registerForm.post(route('contributions-register'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                text: props?.notification?.success
            })
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                text: "An error ocurred! Please try again."
            })
        },
        only: ['notification', 'contribution_type', 'errors'],
        preserveScroll: true,
        preserveState: true,
    })
}
</script>
<template>
    <AppLayout :title="contribution_type?.description">
        <template #header>
            <div v-text="contribution_type?.description"></div>
        </template>
        <Container>
            <div class="py-4 px-6">
                <div class="text-lg uppercase font-semibold" v-text="contribution_type?.description"></div>
                <div class="flex gap-2 divide-x divide-gray-500 text-sm font-medium text-gray-500 capitalize [&>*]:pl-2">
                    <span class="first:pl-0" v-if="contribution_type?.recurrent"
                        v-text="`every ${contribution_type?.recurrence_value} ${contribution_type?.recurrence_unit}${contribution_type?.recurrence_value > 1 ? 's' : ''}`"></span>
                    <span class="first:pl-0" v-if="contribution_type?.amount"
                        v-text="`Amount: ${formatCurrency(contribution_type?.amount)}`"></span>
                    <span class="first:pl-0" v-if="contribution_type?.deadline"
                        v-text="`Closes at: ${formatDate(contribution_type?.deadline)}`"></span>
                </div>
            </div>
            <div class="py-4 px-6 flex flex-col gap-3">
                <div v-for="contribution in contribution_type?.contributions"
                    class="shadow px-4 py-3 rounded-xl border border-b-4">
                    <div class="font-semibold text-gray-800 capitalize" v-text="contribution.member.name"></div>
                    <div class="flex items-center justify-between">
                        <div class="flex gap-3 divide-x text-xs text-gray-500 [&>*]:pl-3">
                            <span v-if="contribution.date" class="first:pl-0" v-text="formatDate(contribution.date)"></span>
                            <span v-if="contribution.status" class="first:pl-0 uppercase font-medium" :class="{
                                'text-orange-400': contribution.status == 'pending',
                                'text-lime-400': contribution.status == 'paid',
                                'text-red-500': contribution.status == 'late',
                            }" v-text="contribution.status"></span>
                            <span v-if="contribution.amount" class="first:pl-0"
                                v-text="`Amount: ${formatCurrency(contribution.amount)}`"></span>
                            <div v-if="contribution.paid" class="first:pl-0">
                                <span class="uppercase font-medium">Paid:</span>
                                <span class="text-green-600" v-text="formatCurrency(contribution.paid)"></span>
                            </div>
                            <div v-if="contribution.balance" class="first:pl-0 flex gap-2">
                                <span class="uppercase font-medium">Balance:</span>
                                <span class="text-red-600" v-text="formatCurrency(contribution.balance)"></span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <SecondaryButton v-if="!contribution.id" @click="enrollContribution(contribution)">Enroll
                            </SecondaryButton>
                            <SecondaryButton v-if="contribution.id">Pay</SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
