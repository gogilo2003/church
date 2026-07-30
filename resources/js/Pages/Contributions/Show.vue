<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import ContributionType from '../../Components/Church/ContributionType.vue';
import Member from '../../Components/Church/Member.vue';
import { Link } from '@inertiajs/vue3';
import Icon from '../../Components/Icons/Icon.vue'
import { iContributionType, iContribution, iMemberWithContributions, iShowMembers } from '@/types';

defineProps<{
    contribution_type?: iContributionType | null;
    members?: iShowMembers | null;
}>()

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
                <ContributionType :item="contribution_type ?? undefined" />
                <Link :href="route('accounts-contributions')"
                    class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    <Icon class="h-4 w-4 object-contain" type="arrow-left" />
                    <span>Back to Contributions</span>
                </Link>
            </div>
            <div class="py-4 px-6 flex flex-col gap-3">
                <Member v-for="member in members?.data" :key="member.id" :member="member" :contribution_type="contribution_type" />
            </div>
        </Container>
    </AppLayout>
</template>
