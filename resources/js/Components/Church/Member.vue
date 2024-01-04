<script lang="ts" setup>
import { ref } from 'vue';
import MemberContributions from './MemberContributions.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
defineProps({
    member: Object,
    contribution_type: Object
})

const show = ref(false)

const closed = () => {
    show.value = false
}

</script>
<template>
    <MemberContributions @closed="closed" :show="show" :member="member" :contribution_type="contribution_type" />
    <div class="shadow px-4 py-3 rounded-xl border border-b-4 flex">
        <div class="flex-1">
            <div class="font-semibold text-gray-800 capitalize" v-text="member?.name"></div>
        </div>
        <div class="flex-none">
            <SecondaryButton size="xs" @click="show = true">View</SecondaryButton>
        </div>
    </div>
</template>
