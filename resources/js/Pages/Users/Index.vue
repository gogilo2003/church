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
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';

interface UserItem {
    id: number;
    name: string;
    email: string;
    username?: string;
    phone_number?: string;
    status: string;
    is_admin: boolean;
    roles?: { id: number; title: string }[];
    created_at: string;
}

const props = defineProps<{
    users: any;
    filters?: {
        search?: string;
        status?: string;
        sort_by?: string;
        sort_order?: string;
    };
}>();

const columns: TableColumn[] = [
    { key: 'name', label: 'User Name', sortable: true },
    { key: 'email', label: 'Email / Username', sortable: true },
    { key: 'roles', label: 'Roles' },
    { key: 'status', label: 'Status', sortable: true, align: 'center' },
    { key: 'created_at', label: 'Created At', sortable: true },
];

const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const sortBy = ref(props.filters?.sort_by || 'created_at');
const sortOrder = ref<'asc' | 'desc'>((props.filters?.sort_order as any) || 'desc');

// Dialog States
const deleteTarget = ref<any>(null);
const statusTarget = ref<any>(null);
const passwordResetTarget = ref<any>(null);
const loading = ref(false);

const resetPasswordForm = ref({
    password: '',
    password_confirmation: '',
    errors: {} as Record<string, string>,
});

const applyFilters = () => {
    router.get(
        route('users.index'),
        {
            search: search.value || undefined,
            status: selectedStatus.value || undefined,
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
    loading.value = true;
    router.delete(route('users.destroy', deleteTarget.value.id), {
        onFinish: () => {
            loading.value = false;
            deleteTarget.value = null;
        },
    });
};

const confirmToggleStatus = () => {
    if (!statusTarget.value) return;
    loading.value = true;
    router.patch(route('users.toggle-status', statusTarget.value.id), {}, {
        onFinish: () => {
            loading.value = false;
            statusTarget.value = null;
        },
    });
};

const submitPasswordReset = () => {
    if (!passwordResetTarget.value) return;
    resetPasswordForm.value.errors = {};
    loading.value = true;

    router.post(
        route('users.reset-password', passwordResetTarget.value.id),
        {
            password: resetPasswordForm.value.password,
            password_confirmation: resetPasswordForm.value.password_confirmation,
        },
        {
            onError: (errs) => {
                resetPasswordForm.value.errors = errs;
            },
            onSuccess: () => {
                passwordResetTarget.value = null;
                resetPasswordForm.value.password = '';
                resetPasswordForm.value.password_confirmation = '';
            },
            onFinish: () => {
                loading.value = false;
            },
        }
    );
};
</script>

<template>
    <AppLayout title="User Management">
        <div class="mx-4 py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader title="User Management"
                description="View, manage, assign roles, and handle user status and accounts.">
                <template #actions>
                    <PrimaryLink :href="route('users.create')">
                        + Add New User
                    </PrimaryLink>
                </template>
            </PageHeader>

            <!-- Page Toolbar -->
            <PageToolbar>
                <template #search>
                    <SearchBox v-model="search" @search="applyFilters" placeholder="Search by name, email..." />
                </template>
                <template #filters>
                    <select v-model="selectedStatus" @change="applyFilters"
                        class="text-xs rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </template>
            </PageToolbar>

            <!-- Data Table -->
            <DataTable :columns="columns" :data="users.data" :sort-by="sortBy" :sort-order="sortOrder"
                @sort="handleSort" empty-title="No users found"
                empty-description="No users match your criteria. Create a new user to get started.">
                <!-- Name Column -->
                <template #cell-name="{ row }">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-black text-xs uppercase">
                            {{ row.name.charAt(0) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                {{ row.name }}
                                <span v-if="row.is_admin"
                                    class="px-1.5 py-0.5 rounded text-[10px] bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-bold uppercase">Admin</span>
                            </div>
                            <div v-if="row.phone_number" class="text-xs text-gray-400">
                                📞 {{ row.phone_number }}
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Email Column -->
                <template #cell-email="{ row }">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ row.email }}
                    </div>
                    <div v-if="row.username" class="text-xs text-gray-400">
                        @{{ row.username }}
                    </div>
                </template>

                <!-- Roles Column -->
                <template #cell-roles="{ row }">
                    <div class="flex flex-wrap gap-1">
                        <template v-if="row.roles && row.roles.length > 0">
                            <span v-for="r in row.roles" :key="r.id"
                                class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                {{ r.title }}
                            </span>
                        </template>
                        <span v-else class="text-xs italic text-gray-400">No Roles</span>
                    </div>
                </template>

                <!-- Status Column -->
                <template #cell-status="{ value }">
                    <StatusBadge :status="value" />
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
                        <!-- Edit Button -->
                        <Link :href="route('users.edit', row.id)"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Edit User">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </Link>

                        <!-- Toggle Status (Suspend / Unsuspend) -->
                        <button @click="statusTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            :title="row.status === 'active' ? 'Suspend User' : 'Unsuspend User'">
                            <svg v-if="row.status === 'active'" class="w-4 h-4 text-amber-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <svg v-else class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <!-- Reset Password -->
                        <button @click="passwordResetTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Reset Password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z" />
                            </svg>
                        </button>

                        <!-- Delete Button -->
                        <button @click="deleteTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Delete User">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div class="mt-6">
                <Paginator :items="users" />
            </div>

            <!-- Confirmation Dialog: Delete -->
            <ConfirmationDialog :show="!!deleteTarget" title="Delete User Account"
                :message="`Are you sure you want to permanently delete user account '${deleteTarget?.name}' (${deleteTarget?.email})? This action cannot be undone.`"
                confirm-text="Delete User" variant="danger" :loading="loading" @confirm="confirmDelete"
                @close="deleteTarget = null" />

            <!-- Confirmation Dialog: Toggle Status -->
            <ConfirmationDialog :show="!!statusTarget"
                :title="statusTarget?.status === 'active' ? 'Suspend User' : 'Unsuspend User'"
                :message="`Are you sure you want to ${statusTarget?.status === 'active' ? 'suspend' : 'reactivate'} user '${statusTarget?.name}'?`"
                :confirm-text="statusTarget?.status === 'active' ? 'Suspend Account' : 'Reactivate Account'"
                :variant="statusTarget?.status === 'active' ? 'warning' : 'info'" :loading="loading"
                @confirm="confirmToggleStatus" @close="statusTarget = null" />

            <!-- Modal: Reset Password -->
            <Modal :show="!!passwordResetTarget" @close="passwordResetTarget = null" max-width="md">
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                        Reset Password for {{ passwordResetTarget?.name }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-6">Enter a new secure password for this user.</p>

                    <form @submit.prevent="submitPasswordReset" class="space-y-4">
                        <div>
                            <InputLabel for="reset_password" value="New Password" />
                            <TextInput id="reset_password" type="password" v-model="resetPasswordForm.password"
                                class="mt-1 block w-full text-sm" required />
                            <InputError class="mt-1" :message="resetPasswordForm.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="reset_password_confirmation" value="Confirm New Password" />
                            <TextInput id="reset_password_confirmation" type="password"
                                v-model="resetPasswordForm.password_confirmation" class="mt-1 block w-full text-sm"
                                required />
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-6">
                            <SecondaryButton @click="passwordResetTarget = null">Cancel</SecondaryButton>
                            <PrimaryButton :disabled="loading">Update Password</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </AppLayout>
</template>
