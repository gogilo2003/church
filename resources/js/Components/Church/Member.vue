<script lang="ts" setup>
import { ref } from 'vue';
import MemberContributions from './MemberContributions.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import { formatCurrency } from '../../helpers';

defineProps({
    member: Object,
    contribution_type: Object
})

const show = ref(false)

const closed = () => {
    show.value = false
}

const enrolled = () => {
    // show.value = false
}

</script>
<template>
    <MemberContributions @enrolled="enrolled" @closed="closed" :show="show" :member="member"
        :contribution_type="contribution_type" />
    <div class="shadow px-4 py-3 rounded-xl border border-b-4 flex">
        <div class="flex-1">
            <div class="font-semibold text-gray-800 capitalize" v-text="member?.name"></div>
            <div class="flex flex-col md:flex-row md:divide-x">
                <div class="flex gap-2 text-xs text-gray-600 md:pr-3">
                    <span class="font-medium uppercase">Amount:</span>
                    <span v-text="formatCurrency(member?.amount)"></span>
                </div>
                <div class="flex gap-2 text-xs text-gray-600 md:px-3">
                    <span class="font-medium uppercase">Paid:</span>
                    <span v-text="formatCurrency(member?.paid)"></span>
                </div>
                <div class="flex gap-2 text-xs text-gray-600 md:px-3">
                    <span class="font-medium uppercase">Balance:</span>
                    <span v-text="formatCurrency(member?.balance)"></span>
                </div>
            </div>
        </div>
        <div class="flex-none">
            <SecondaryButton size="xs" @click="show = true">View</SecondaryButton>
        </div>
    </div>
</template>
