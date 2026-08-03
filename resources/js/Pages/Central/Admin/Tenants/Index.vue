<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PageToolbar from '@/Components/PageToolbar.vue';
import SearchBox from '@/Components/SearchBox.vue';
import DataTable, { TableColumn } from '@/Components/DataTable.vue';

interface TenantSummary {
    id: string;
    church_name: string;
    admin_name: string | null;
    admin_email: string | null;
    phone: string | null;
    subdomain: string;
    custom_domains: string[];
    created_at: string | null;
}

const props = defineProps<{
    tenants: TenantSummary[];
}>();

const searchQuery = ref('');

const filteredTenants = computed(() => {
    if (!searchQuery.value.trim()) return props.tenants;
    const q = searchQuery.value.toLowerCase();
    return props.tenants.filter(
        (t) =>
            t.id.toLowerCase().includes(q) ||
            t.church_name.toLowerCase().includes(q) ||
            (t.admin_name && t.admin_name.toLowerCase().includes(q)) ||
            (t.admin_email && t.admin_email.toLowerCase().includes(q)) ||
            t.subdomain.toLowerCase().includes(q)
    );
});

const columns: TableColumn[] = [
    { key: 'id', label: 'Tenant ID', sortable: true },
    { key: 'church_name', label: 'Church Name', sortable: true },
    { key: 'admin_email', label: 'Admin Contact', sortable: true },
    { key: 'subdomain', label: 'Primary Subdomain', sortable: true },
    { key: 'custom_domains', label: 'Custom Domains' },
];

// Create / Edit Tenant Modal State
const showTenantModal = ref(false);
const isEditing = ref(false);

const tenantForm = useForm({
    id: '',
    church_name: '',
    subdomain: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
    admin_password_confirmation: '',
    phone: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    tenantForm.reset();
    tenantForm.clearErrors();
    showTenantModal.value = true;
};

const openEditModal = (tenant: TenantSummary) => {
    isEditing.value = true;
    tenantForm.clearErrors();
    tenantForm.id = tenant.id;
    tenantForm.church_name = tenant.church_name;
    tenantForm.subdomain = tenant.subdomain;
    tenantForm.admin_name = tenant.admin_name || '';
    tenantForm.admin_email = tenant.admin_email || '';
    tenantForm.phone = tenant.phone || '';
    tenantForm.admin_password = '';
    tenantForm.admin_password_confirmation = '';
    showTenantModal.value = true;
};

const submitTenantForm = () => {
    if (isEditing.value) {
        tenantForm.put(route('central.admin.tenants.update', tenantForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                showTenantModal.value = false;
                tenantForm.reset();
            },
        });
    } else {
        tenantForm.post(route('central.admin.tenants.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showTenantModal.value = false;
                tenantForm.reset();
            },
        });
    }
};

// Add Custom Domain Modal State
const showAddCustomDomainDialog = ref(false);
const selectedTenant = ref<TenantSummary | null>(null);

const customDomainForm = useForm({
    tenant_id: '',
    domain: '',
});

const openAddDomainModal = (tenant: TenantSummary) => {
    selectedTenant.value = tenant;
    customDomainForm.tenant_id = tenant.id;
    customDomainForm.domain = '';
    customDomainForm.clearErrors();
    showAddCustomDomainDialog.value = true;
};

const submitCustomDomain = () => {
    customDomainForm.post(route('central.admin.tenants.add-custom-domain'), {
        preserveScroll: true,
        onSuccess: () => {
            showAddCustomDomainDialog.value = false;
            customDomainForm.reset();
        },
    });
};

const isMigrating = ref(false);

const runMigrations = (tenantId?: string) => {
    isMigrating.value = true;
    router.post(
        route('central.admin.tenants.migrate'),
        { tenant_id: tenantId ?? null },
        {
            preserveScroll: true,
            onFinish: () => {
                isMigrating.value = false;
            },
        }
    );
};
</script>

<template>

    <Head title="Central Admin - Tenant Management" />

    <AppLayout>
        <div class="mx-4 py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader title="Central Tenant Management"
                description="Manage church workspaces, create or update tenant configurations, domain routing, and database migrations.">
                <template #actions>
                    <SecondaryButton @click="runMigrations()" :disabled="isMigrating">
                        Run All Migrations
                    </SecondaryButton>
                    <PrimaryButton @click="openCreateModal">
                        + Provision New Tenant
                    </PrimaryButton>
                </template>
            </PageHeader>

            <PageToolbar>
                <template #search>
                    <SearchBox v-model="searchQuery" placeholder="Search by church, admin, or subdomain..." />
                </template>
            </PageToolbar>

            <!-- Tenants Data Table -->
            <DataTable :columns="columns" :data="filteredTenants" empty-title="No tenants found"
                empty-description="Provision a new tenant workspace to get started.">
                <template #cell-id="{ row }">
                    <span class="font-bold text-gray-900 dark:text-white text-xs">
                        {{ row.id }}
                    </span>
                </template>

                <template #cell-church_name="{ row }">
                    <div>
                        <div class="font-bold text-gray-900 dark:text-white text-sm">
                            {{ row.church_name }}
                        </div>
                        <div v-if="row.phone" class="text-xs text-gray-500 dark:text-gray-400">
                            {{ row.phone }}
                        </div>
                    </div>
                </template>

                <template #cell-admin_email="{ row }">
                    <div>
                        <div class="text-sm text-gray-800 dark:text-gray-200">
                            {{ row.admin_name || 'Admin User' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ row.admin_email || '-' }}
                        </div>
                    </div>
                </template>

                <template #cell-subdomain="{ row }">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ row.subdomain }}.church.test
                    </span>
                </template>

                <template #cell-custom_domains="{ row }">
                    <div class="flex flex-wrap gap-1">
                        <span v-for="domain in row.custom_domains" :key="domain"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            {{ domain }}
                        </span>
                        <span v-if="!row.custom_domains.length" class="text-xs text-gray-400">None</span>
                    </div>
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <button @click="openEditModal(row)"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-xs font-semibold">
                            Edit
                        </button>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <button @click="openAddDomainModal(row)"
                            class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 text-xs font-semibold">
                            + Domain
                        </button>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <button @click="runMigrations(row.id)"
                            class="text-amber-600 dark:text-amber-400 hover:text-amber-900 text-xs font-semibold">
                            Migrate
                        </button>
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Create / Edit Tenant Modal Dialog -->
        <Modal :show="showTenantModal" max-width="lg" @close="showTenantModal = false">
            <div class="p-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ isEditing ? 'Edit Tenant Details' : 'Provision New Tenant Workspace' }}
                    </h3>
                    <button @click="showTenantModal = false"
                        class="text-gray-400 hover:text-gray-500 text-lg leading-none">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="submitTenantForm" class="space-y-4">
                    <div>
                        <InputLabel for="church_name" value="Church / Organization Name *" />
                        <TextInput id="church_name" v-model="tenantForm.church_name" type="text"
                            placeholder="e.g. Grace Fellowship Church" class="w-full mt-1" required />
                        <InputError :message="tenantForm.errors.church_name" class="mt-1" />
                    </div>

                    <div v-if="!isEditing">
                        <InputLabel for="subdomain" value="Subdomain Identifier *" />
                        <div class="flex items-center mt-1">
                            <TextInput id="subdomain" v-model="tenantForm.subdomain" type="text" placeholder="grace"
                                class="w-full rounded-r-none" required />
                            <span
                                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs font-semibold rounded-r-md">
                                .church.test
                            </span>
                        </div>
                        <InputError :message="tenantForm.errors.subdomain" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="admin_name" value="Admin Contact Name" />
                            <TextInput id="admin_name" v-model="tenantForm.admin_name" type="text"
                                placeholder="Pastor John Doe" class="w-full mt-1" />
                            <InputError :message="tenantForm.errors.admin_name" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="phone" value="Phone Number" />
                            <TextInput id="phone" v-model="tenantForm.phone" type="text" placeholder="+254 700 000 000"
                                class="w-full mt-1" />
                            <InputError :message="tenantForm.errors.phone" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="admin_email" value="Admin Email Address *" />
                        <TextInput id="admin_email" v-model="tenantForm.admin_email" type="email"
                            placeholder="admin@church.org" class="w-full mt-1" required />
                        <InputError :message="tenantForm.errors.admin_email" class="mt-1" />
                    </div>

                    <template v-if="!isEditing">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="admin_password" value="Initial Admin Password *" />
                                <TextInput id="admin_password" v-model="tenantForm.admin_password" type="password"
                                    class="w-full mt-1" required />
                                <InputError :message="tenantForm.errors.admin_password" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="admin_password_confirmation" value="Confirm Password *" />
                                <TextInput id="admin_password_confirmation"
                                    v-model="tenantForm.admin_password_confirmation" type="password" class="w-full mt-1"
                                    required />
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <SecondaryButton @click="showTenantModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="tenantForm.processing">
                            {{ isEditing ? 'Save Changes' : 'Provision Tenant' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Custom Domain Modal Dialog -->
        <Modal :show="showAddCustomDomainDialog" max-width="md" @close="showAddCustomDomainDialog = false">
            <div class="p-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Add Custom External Domain
                    </h3>
                    <button @click="showAddCustomDomainDialog = false"
                        class="text-gray-400 hover:text-gray-500 text-lg leading-none">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="submitCustomDomain" class="space-y-4">
                    <div>
                        <InputLabel value="Target Tenant Workspace" />
                        <TextInput :model-value="selectedTenant?.church_name || ''" readonly disabled
                            class="w-full mt-1 bg-gray-100 dark:bg-gray-800" />
                    </div>
                    <div>
                        <InputLabel for="custom_domain" value="Custom Domain Name *" />
                        <TextInput id="custom_domain" v-model="customDomainForm.domain" type="text"
                            placeholder="e.g. mis.elck.org or churchabc.com" class="w-full mt-1" required />
                        <InputError :message="customDomainForm.errors.domain" class="mt-1" />
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <SecondaryButton @click="showAddCustomDomainDialog = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="customDomainForm.processing">
                            Save Domain
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
