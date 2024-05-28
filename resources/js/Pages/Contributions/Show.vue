<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import ContributionType from '../../Components/Church/ContributionType.vue';
import Member from '../../Components/Church/Member.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import Icon from '../../Components/Icons/Icon.vue'
defineProps({
    contribution_type: Object,
    members: Object,
})

</script>
<template>
    <AppLayout :title="contribution_type?.description">
        <template #header>
            <div>
                <div v-text="contribution_type?.description"></div>
                <div class="text-xs text-gray-600"></div>
            </div>
        </template>
        <Container>
            <div class="p-6 flex items-center justify-between">
                <ContributionType :item="contribution_type" />
                <SecondaryButton size="sm" :type="Link" :href="route('accounts-contributions')">
                    <Icon class="h-full w-auto object-contain" type="arrow-left" />
                    <span>Back to Contributions</span>
                </SecondaryButton>
            </div>
            <div class="py-4 px-6 flex flex-col gap-3">
                <Member v-for="member in members?.data" :member="member" :contribution_type="contribution_type" />
            </div>
        </Container>
    </AppLayout>
</template>
