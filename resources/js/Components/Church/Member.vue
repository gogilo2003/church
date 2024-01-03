<script lang="ts" setup>
import MemberContributions from './MemberContributions.vue';
defineProps({
    member: Object,
    contribution_type: Object
})
</script>
<template>
    <div class="shadow px-4 py-3 rounded-xl border border-b-4">
        <div class="font-semibold text-gray-800 capitalize" v-text="member?.name"></div>
        <MemberContributions :member="member" :contribution_type="contribution_type" />
    </div>
</template>
