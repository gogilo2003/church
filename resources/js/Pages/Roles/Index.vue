<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PageToolbar from '@/Components/PageToolbar.vue';
import SearchBox from '@/Components/SearchBox.vue';
import DataTable, { TableColumn } from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Paginator from '@/Components/Paginator.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';

interface RoleItem {
    id: number;
    name: string;
    title: string;
    description?: string;
    is_system_role: boolean;
    users_count?: number;
    created_at: string;
}

const props = defineProps<{
    roles: any;
    filters?: {
        search?: string;
        sort_by?: string;
        sort_order?: string;
    };
}>();

const columns: TableColumn[] = [
    { key: 'title', label: 'Role Title', sortable: true },
    { key: 'description', label: 'Description' },
    { key: 'is_system_role', label: 'Type', sortable: true, align: 'center' },
    { key: 'users_count', label: 'Assigned Users', sortable: true, align: 'center' },
    { key: 'created_at', label: 'Created At', sortable: true },
];

const search = ref(props.filters?.search || '');
const sortBy = ref(props.filters?.sort_by || 'created_at');
const sortOrder = ref<'asc' | 'desc'>((props.filters?.sort_order as any) || 'desc');

const deleteTarget = ref<any>(null);
const loading = ref(false);

const applyFilters = () => {
    router.get(
        route('roles.index'),
        {
            search: search.value || undefined,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
        },
        { preserveState: true, replace: true }
    );
};

const handleSort = (columnKey: string) => {
    if (sortBy.value === columnKey) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = columnKey;
        sortOrder.value = 'asc';
    }
    applyFilters();
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    if (deleteTarget.value.is_system_role) {
        alert('System roles cannot be deleted.');
        deleteTarget.value = null;
        return;
    }

    loading.value = true;
    router.delete(route('roles.destroy', deleteTarget.value.id), {
        onFinish: () => {
            loading.value = false;
            deleteTarget.value = null;
        },
    });
};
</script>

<template>
    <AppLayout title="Role Management">
        <div class="mx-4 py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader title="Role Management" description="Manage user roles and assign users to specific roles.">
                <template #actions>
                    <PrimaryLink :href="route('roles.create')">
                        + Add New Role
                    </PrimaryLink>
                </template>
            </PageHeader>

            <!-- Page Toolbar -->
            <PageToolbar>
                <template #search>
                    <SearchBox v-model="search" @search="applyFilters" placeholder="Search roles..." />
                </template>
            </PageToolbar>

            <!-- Data Table -->
            <DataTable :columns="columns" :data="roles.data" :sort-by="sortBy" :sort-order="sortOrder"
                @sort="handleSort" empty-title="No roles found" empty-description="No roles match your search filters.">
                <!-- Title Column -->
                <template #cell-title="{ row }">
                    <div class="font-bold text-gray-900 dark:text-white text-sm">
                        {{ row.title }}
                    </div>
                    <div v-if="row.name" class="text-xs text-gray-400 font-mono">
                        {{ row.name }}
                    </div>
                </template>

                <!-- Description Column -->
                <template #cell-description="{ value }">
                    <span class="text-xs text-gray-600 dark:text-gray-300 max-w-xs truncate block">
                        {{ value || 'No description provided.' }}
                    </span>
                </template>

                <!-- Type Column -->
                <template #cell-is_system_role="{ value }">
                    <StatusBadge :type="value ? 'system' : 'custom'" />
                </template>

                <!-- Users Count Column -->
                <template #cell-users_count="{ value }">
                    <span
                        class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                        {{ value ?? 0 }} Users
                    </span>
                </template>

                <!-- Created At Column -->
                <template #cell-created_at="{ value }">
                    <span class="text-xs text-gray-500">
                        {{ new Date(value).toLocaleDateString() }}
                    </span>
                </template>

                <!-- Actions Column -->
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Assign Users Button -->
                        <Link :href="route('roles.users', row.id)"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition flex items-center gap-1"
                            title="Assign Users">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Assign Users</span>
                        </Link>

                        <!-- Edit Button -->
                        <Link :href="route('roles.edit', row.id)"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Edit Role">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </Link>

                        <!-- Delete Button -->
                        <button v-if="!row.is_system_role" @click="deleteTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Delete Role">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <span v-else class="p-1.5 text-gray-300 dark:text-gray-700 cursor-not-allowed"
                            title="System roles cannot be deleted">
                            🔒
                        </span>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="mt-6">
                <Paginator :items="roles" />
            </div>

            <!-- Confirmation Dialog: Delete -->
            <ConfirmationDialog :show="!!deleteTarget" title="Delete Role"
                :message="`Are you sure you want to delete role '${deleteTarget?.title}'? Assigned users will lose this role.`"
                confirm-text="Delete Role" variant="danger" :loading="loading" @confirm="confirmDelete"
                @close="deleteTarget = null" />
        </div>
    </AppLayout>
</template>
