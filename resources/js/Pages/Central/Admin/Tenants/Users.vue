<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface TenantInfo {
    id: string;
    church_name: string;
    subdomain: string;
}

interface UserItem {
    id: number;
    name: string;
    email: string;
    username?: string;
    phone_number?: string;
    status: string;
    is_admin: boolean;
    roles?: { id: number; title: string }[];
    role_ids?: number[];
    created_at: string;
}

interface RoleOption {
    id: number;
    title: string;
    description?: string;
}

const props = defineProps<{
    tenant: TenantInfo;
    users: any;
    roles: { data: RoleOption[] };
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

const applyFilters = () => {
    router.get(
        route('central.admin.tenants.users.index', { tenant: props.tenant.id }),
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

const userForm = useForm({
    name: '',
    email: '',
    username: '',
    phone_number: '',
    status: 'active',
    password: '',
    password_confirmation: '',
    role_ids: [] as number[],
});

const showUserModal = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    userForm.reset();
    userForm.clearErrors();
    showUserModal.value = true;
};

const openEditModal = (user: UserItem) => {
    isEditing.value = true;
    editingId.value = user.id;
    userForm.clearErrors();
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.username = user.username || '';
    userForm.phone_number = user.phone_number || '';
    userForm.status = user.status || 'active';
    userForm.password = '';
    userForm.password_confirmation = '';
    userForm.role_ids = user.roles?.map((r) => r.id) || [];
    showUserModal.value = true;
};

const toggleRole = (roleId: number) => {
    if (userForm.role_ids.includes(roleId)) {
        userForm.role_ids = userForm.role_ids.filter((id) => id !== roleId);
    } else {
        userForm.role_ids.push(roleId);
    }
};

const submitUserForm = () => {
    if (isEditing.value && editingId.value) {
        userForm.patch(
            route('central.admin.tenants.users.update', { tenant: props.tenant.id, user: editingId.value }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    showUserModal.value = false;
                    userForm.reset();
                },
            }
        );
    } else {
        userForm.post(route('central.admin.tenants.users.store', { tenant: props.tenant.id }), {
            preserveScroll: true,
            onSuccess: () => {
                showUserModal.value = false;
                userForm.reset();
            },
        });
    }
};

const statusTarget = ref<UserItem | null>(null);
const statusLoading = ref(false);

const confirmToggleStatus = () => {
    if (!statusTarget.value) return;
    statusLoading.value = true;
    router.patch(
        route('central.admin.tenants.users.toggle-status', {
            tenant: props.tenant.id,
            user: statusTarget.value.id,
        }),
        {
            status: statusTarget.value.status === 'active' ? 'suspended' : 'active',
        },
        {
            preserveScroll: true,
            onFinish: () => {
                statusLoading.value = false;
                statusTarget.value = null;
            },
        }
    );
};

const passwordResetTarget = ref<UserItem | null>(null);
const resetPasswordForm = ref({
    password: '',
    password_confirmation: '',
    errors: {} as Record<string, string>,
});
const passwordLoading = ref(false);

const submitPasswordReset = () => {
    if (!passwordResetTarget.value) return;
    resetPasswordForm.value.errors = {};
    passwordLoading.value = true;
    router.post(
        route('central.admin.tenants.users.reset-password', {
            tenant: props.tenant.id,
            user: passwordResetTarget.value.id,
        }),
        {
            password: resetPasswordForm.value.password,
            password_confirmation: resetPasswordForm.value.password_confirmation,
        },
        {
            preserveScroll: true,
            onError: (errs) => {
                resetPasswordForm.value.errors = errs;
            },
            onSuccess: () => {
                passwordResetTarget.value = null;
                resetPasswordForm.value.password = '';
                resetPasswordForm.value.password_confirmation = '';
            },
            onFinish: () => {
                passwordLoading.value = false;
            },
        }
    );
};
</script>

<template>
    <Head :title="`Users - ${tenant.church_name}`" />

    <AppLayout>
        <div class="mx-4 py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader :title="`${tenant.church_name} — Users`"
                :description="`Manage user accounts in ${tenant.church_name} (${tenant.subdomain}).`"
                :back-route="route('central.admin.tenants.index')">
                <template #actions>
                    <PrimaryButton @click="openCreateModal">
                        + Add User
                    </PrimaryButton>
                </template>
            </PageHeader>

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

            <DataTable :columns="columns" :data="(users.data as UserItem[])" :sort-by="sortBy" :sort-order="sortOrder"
                @sort="handleSort" empty-title="No users found"
                empty-description="No users match your criteria. Add a user to get started.">
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
                                {{ row.phone_number }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #cell-email="{ row }">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ row.email }}
                    </div>
                    <div v-if="row.username" class="text-xs text-gray-400">
                        @{{ row.username }}
                    </div>
                </template>

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

                <template #cell-status="{ value }">
                    <StatusBadge :status="value" />
                </template>

                <template #cell-created_at="{ value }">
                    <span class="text-xs text-gray-500">
                        {{ new Date(value).toLocaleDateString() }}
                    </span>
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <button @click="openEditModal(row)" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Edit User">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <button @click="statusTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            :title="row.status === 'active' ? 'Suspend User' : 'Reactivate User'">
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

                        <button @click="passwordResetTarget = row" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                            title="Reset Password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z" />
                            </svg>
                        </button>
                    </div>
                </template>
            </DataTable>

            <div class="mt-6">
                <Paginator :items="users" />
            </div>

            <ConfirmationDialog :show="!!statusTarget"
                :title="statusTarget?.status === 'active' ? 'Suspend User' : 'Reactivate User'"
                :message="`Are you sure you want to ${statusTarget?.status === 'active' ? 'suspend' : 'reactivate'} user '${statusTarget?.name}' in ${tenant.church_name}?`"
                :confirm-text="statusTarget?.status === 'active' ? 'Suspend Account' : 'Reactivate Account'"
                :variant="statusTarget?.status === 'active' ? 'warning' : 'info'" :loading="statusLoading"
                @confirm="confirmToggleStatus" @close="statusTarget = null" />

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
                            <PrimaryButton :disabled="passwordLoading">Update Password</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>

            <Modal :show="showUserModal" max-width="lg" @close="showUserModal = false">
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ isEditing ? 'Edit User' : 'Add User' }} — {{ tenant.church_name }}
                        </h3>
                        <button @click="showUserModal = false"
                            class="text-gray-400 hover:text-gray-500 text-lg leading-none">
                            &times;
                        </button>
                    </div>

                    <form @submit.prevent="submitUserForm" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="name" value="Full Name *" />
                                <TextInput id="name" v-model="userForm.name" type="text" required class="w-full mt-1" />
                                <InputError :message="userForm.errors.name" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Address *" />
                                <TextInput id="email" v-model="userForm.email" type="email" required class="w-full mt-1" />
                                <InputError :message="userForm.errors.email" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="username" value="Username (Optional)" />
                                <TextInput id="username" v-model="userForm.username" type="text" class="w-full mt-1" />
                                <InputError :message="userForm.errors.username" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="phone_number" value="Phone Number (Optional)" />
                                <TextInput id="phone_number" v-model="userForm.phone_number" type="text" class="w-full mt-1" />
                                <InputError :message="userForm.errors.phone_number" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="status" value="Account Status" />
                                <select id="status" v-model="userForm.status"
                                    class="mt-1 w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3">
                                    <option value="active">Active (Can Log In)</option>
                                    <option value="suspended">Suspended (Access Blocked)</option>
                                </select>
                                <InputError :message="userForm.errors.status" class="mt-1" />
                            </div>

                            <div v-if="isEditing">
                                <InputLabel for="password" value="New Password (Optional)" />
                                <TextInput id="password" v-model="userForm.password" type="password" class="w-full mt-1" />
                            </div>
                        </div>

                        <template v-if="!isEditing">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="password" value="Password *" />
                                    <TextInput id="password" v-model="userForm.password" type="password" required class="w-full mt-1" />
                                    <InputError :message="userForm.errors.password" class="mt-1" />
                                </div>

                                <div>
                                    <InputLabel for="password_confirmation" value="Confirm Password *" />
                                    <TextInput id="password_confirmation" v-model="userForm.password_confirmation" type="password" required class="w-full mt-1" />
                                </div>
                            </div>
                        </template>

                        <div>
                            <InputLabel value="Role Assignments" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                Select one or more roles to grant to this user.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div v-for="role in roles.data" :key="role.id" @click="toggleRole(role.id)"
                                    :class="[
                                        'p-3 rounded-xl border cursor-pointer transition-all flex items-start gap-3',
                                        userForm.role_ids.includes(role.id)
                                            ? 'bg-indigo-50/60 dark:bg-indigo-950/40 border-indigo-500 text-indigo-900 dark:text-indigo-200'
                                            : 'bg-gray-50 dark:bg-gray-800/40 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-300'
                                    ]">
                                    <Checkbox :checked="userForm.role_ids.includes(role.id)" class="mt-0.5" />
                                    <div>
                                        <div class="font-bold text-sm">{{ role.title }}</div>
                                        <div v-if="role.description" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ role.description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <InputError :message="userForm.errors.role_ids" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                            <SecondaryButton @click="showUserModal = false">
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton type="submit" :disabled="userForm.processing">
                                {{ isEditing ? 'Save Changes' : 'Add User' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </AppLayout>
</template>