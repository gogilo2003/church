<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import EmptyState from '@/Components/EmptyState.vue';

interface HouseholdDetail {
    id: number;
    name: string;
    primary_contact_phone: string | null;
    address: string | null;
    marriage_date: string | null;
    org_unit?: { id: number; name: string } | null;
    member_relationships: {
        id: number;
        full_name: string;
        first_name: string;
        last_name: string;
        phone: string;
        email: string | null;
        status: string;
        photo_url: string;
        pivot: {
            relationship: string;
        };
    }[];
}

const props = defineProps<{
    household: HouseholdDetail;
}>();

const formatDate = (date: string | null): string => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const relationshipColor = (relationship: string): string => {
    const map: Record<string, string> = {
        head: 'bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        spouse: 'bg-pink-100 dark:bg-pink-950/80 text-pink-700 dark:text-pink-300 border-pink-200 dark:border-pink-800',
        child: 'bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800',
        parent: 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        guardian: 'bg-violet-100 dark:bg-violet-950/80 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-800',
        other: 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700',
    };
    return map[relationship.toLowerCase()] ?? map.other;
};

const headerDescription = (() => {
    const parts: string[] = [];
    if (props.household.marriage_date) {
        parts.push(`Married: ${formatDate(props.household.marriage_date)}`);
    }
    if (props.household.primary_contact_phone) {
        parts.push(`Contact: ${props.household.primary_contact_phone}`);
    }
    return parts.join(' • ') || 'Household Profile';
})();
</script>

<template>
    <Head :title="`Household - ${household.name}`" />

    <AppLayout>
        <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
            <PageHeader
                :title="household.name"
                :description="headerDescription"
                :back-route="route('households.index')"
            >
                <template #actions>
                    <Link :href="route('households.index')">
                        <PrimaryButton>
                            Edit Household
                        </PrimaryButton>
                    </Link>
                </template>
            </PageHeader>

            <!-- Household Summary Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 sm:p-8">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider pb-2 border-b border-gray-100 dark:border-gray-800">
                    Household Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-4 text-xs">
                    <div>
                        <span class="text-gray-400">Household Name</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ household.name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Primary Contact Phone</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ household.primary_contact_phone || '—' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Marriage Date</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ formatDate(household.marriage_date) }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-400">Address</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ household.address || 'No address specified' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">Organizational Unit</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">
                            <span v-if="household.org_unit" class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded">
                                🏛️ {{ household.org_unit.name }}
                            </span>
                            <span v-else>—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Members Section -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        Family Members
                    </h3>
                    <span class="text-xs text-gray-400 font-medium">
                        {{ household.member_relationships.length }} member{{ household.member_relationships.length !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div v-if="household.member_relationships.length" class="mt-4 space-y-3">
                    <Link
                        v-for="member in household.member_relationships"
                        :key="member.id"
                        :href="route('members.show', member.id)"
                        class="member-row flex items-center gap-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 hover:border-indigo-200 dark:hover:border-indigo-800 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all duration-200 cursor-pointer"
                    >
                        <!-- Avatar -->
                        <img
                            :src="member.photo_url"
                            :alt="member.full_name"
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700 shadow-sm flex-shrink-0"
                        />

                        <!-- Name & Relationship -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ member.full_name }}
                                </span>
                                <span
                                    :class="[
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border',
                                        relationshipColor(member.pivot.relationship),
                                    ]"
                                >
                                    {{ member.pivot.relationship }}
                                </span>
                                <StatusBadge :status="member.status" />
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                {{ member.phone }}
                                <span v-if="member.email"> • {{ member.email }}</span>
                            </div>
                        </div>

                        <!-- Arrow indicator -->
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <div v-else class="mt-4">
                    <EmptyState
                        title="No Family Members"
                        description="This household doesn't have any members linked yet. Add members from the Members page."
                    >
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </template>
                    </EmptyState>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.member-row {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.member-row:hover {
    transform: translateX(2px);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
}
</style>
