<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PageToolbar from '@/Components/PageToolbar.vue';
import SearchBox from '@/Components/SearchBox.vue';
import DataTable, { TableColumn } from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Paginator from '@/Components/Paginator.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';

interface MemberItem {
    id: number;
    member_number: string | null;
    full_name: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string;
    gender: string;
    status: string;
    photo_url: string;
    date_of_birth: string | null;
    date_joined: string | null;
    household?: { id: number; name: string } | null;
    org_unit?: { id: number; name: string } | null;
}

const props = defineProps<{
    members: {
        data: MemberItem[];
        links: any[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters: {
        search?: string;
        status?: string;
        org_unit_id?: string;
    };
    orgUnits: { id: number; name: string }[];
}>();

const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedOrgUnit = ref(props.filters.org_unit_id || '');

const statusTabs = [
    { value: '', label: 'All Members' },
    { value: 'active', label: 'Active' },
    { value: 'visitor', label: 'Visitors' },
    { value: 'new_convert', label: 'New Converts' },
    { value: 'new_member', label: 'New Members' },
    { value: 'inactive', label: 'Inactive' },
];

const columns: TableColumn[] = [
    { key: 'full_name', label: 'Member Name', sortable: true },
    { key: 'contact', label: 'Contact Details' },
    { key: 'status', label: 'Lifecycle Status', sortable: true },
    { key: 'household', label: 'Household' },
    { key: 'org_unit', label: 'Parish / Unit' },
];

const applyFilters = () => {
    router.get(
        route('members.index'),
        {
            search: search.value || undefined,
            status: selectedStatus.value || undefined,
            org_unit_id: selectedOrgUnit.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const filterByStatus = (statusVal: string) => {
    selectedStatus.value = statusVal;
    applyFilters();
};

watch([selectedOrgUnit], () => {
    applyFilters();
});

// Delete confirmation modal state
const confirmDeleteModal = ref(false);
const memberToDelete = ref<MemberItem | null>(null);

const promptDelete = (member: MemberItem) => {
    memberToDelete.value = member;
    confirmDeleteModal.value = true;
};

const deleteMember = () => {
    if (!memberToDelete.value) return;
    router.delete(route('members.destroy', memberToDelete.value.id), {
        onSuccess: () => {
            confirmDeleteModal.value = false;
            memberToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Member Directory" />

    <AppLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                title="Member Directory"
                description="Manage church membership, spiritual profiles, households, and lifecycle transitions."
            >
                <template #actions>
                    <PrimaryLink :href="route('members.create')">
                        + Register New Member
                    </PrimaryLink>
                </template>
            </PageHeader>

            <!-- Status Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-4 scrollbar-none">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.value"
                    @click="filterByStatus(tab.value)"
                    :class="[
                        'px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all',
                        selectedStatus === tab.value
                            ? 'bg-gray-800 text-white shadow-sm'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Toolbar & Search -->
            <PageToolbar>
                <template #search>
                    <SearchBox
                        v-model="search"
                        @search="applyFilters"
                        placeholder="Search by member name, ID, phone, or email..."
                    />
                </template>
                <template #filters>
                    <select
                        v-model="selectedOrgUnit"
                        class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    >
                        <option value="">All Parishes / Units</option>
                        <option v-for="unit in orgUnits" :key="unit.id" :value="unit.id">
                            {{ unit.name }}
                        </option>
                    </select>
                </template>
            </PageToolbar>

            <!-- Members Data Table -->
            <DataTable
                :columns="columns"
                :data="members.data"
                empty-title="No members registered yet"
                empty-description="Register church members or visitors to begin tracking spiritual profiles."
            >
                <template #cell-full_name="{ row }">
                    <div class="flex items-center gap-3">
                        <img
                            :src="row.photo_url"
                            :alt="row.full_name"
                            class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-700"
                        />
                        <div>
                            <button
                                @click="router.get(route('members.show', row.id))"
                                class="font-bold text-gray-900 dark:text-white text-sm hover:text-indigo-600 text-left transition-colors"
                            >
                                {{ row.full_name }}
                            </button>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ row.member_number || `#MEM-${row.id}` }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #cell-contact="{ row }">
                    <div>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                            {{ row.phone }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ row.email || 'No email' }}
                        </div>
                    </div>
                </template>

                <template #cell-status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>

                <template #cell-household="{ row }">
                    <span v-if="row.household" class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        {{ row.household.name }}
                    </span>
                    <span v-else class="text-xs text-gray-400">None</span>
                </template>

                <template #cell-org_unit="{ row }">
                    <span v-if="row.org_unit" class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        {{ row.org_unit.name }}
                    </span>
                    <span v-else class="text-xs text-gray-400">Main Church</span>
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="router.get(route('members.show', row.id))"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-xs font-semibold"
                        >
                            View
                        </button>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <button
                            @click="router.get(route('members.edit', row.id))"
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 text-xs font-semibold"
                        >
                            Edit
                        </button>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <button
                            @click="promptDelete(row)"
                            class="text-rose-600 dark:text-rose-400 hover:text-rose-900 text-xs font-semibold"
                        >
                            Delete
                        </button>
                    </div>
                </template>
            </DataTable>

            <Paginator :items="members as any" />
        </div>

        <ConfirmationDialog
            :show="confirmDeleteModal"
            title="Delete Member Record"
            :message="`Are you sure you want to delete member ${memberToDelete?.full_name}? This action cannot be undone.`"
            confirm-button-text="Delete Member"
            @confirm="deleteMember"
            @close="confirmDeleteModal = false"
        />
    </AppLayout>
</template>
