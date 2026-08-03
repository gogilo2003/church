<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface TenantSummary {
    id: string;
    church_name: string;
    admin_email: string | null;
    subdomain: string;
    custom_domains: string[];
    created_at: string | null;
}

defineProps<{
    tenants: TenantSummary[];
}>();

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
    router.post(route('central.admin.tenants.migrate'), { tenant_id: tenantId ?? null }, {
        preserveScroll: true,
        onFinish: () => {
            isMigrating.value = false;
        },
    });
};
</script>

<template>

    <Head title="Central Admin - Tenant Management" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Central Tenant Management</h2>
                    <p class="text-xs text-gray-500">Manage church workspaces, custom domain routing, and tenant
                        database migrations.</p>
                </div>
                <div class="flex gap-2">
                    <SecondaryButton @click="runMigrations()" :disabled="isMigrating">
                        Run All Tenant Migrations
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tenant ID</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Church Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin
                                    Email</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Primary Subdomain</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Custom Domains</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="tenant in tenants" :key="tenant.id">
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ tenant.id }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{
                                    tenant.church_name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{
                                    tenant.admin_email || '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ tenant.subdomain }}.church.test
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="domain in tenant.custom_domains" :key="domain"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ domain }}
                                        </span>
                                        <span v-if="!tenant.custom_domains.length"
                                            class="text-xs text-gray-400">None</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openAddDomainModal(tenant)"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">
                                            + Add Domain
                                        </button>
                                        <button @click="runMigrations(tenant.id)"
                                            class="text-yellow-600 hover:text-yellow-900 text-xs font-semibold">
                                            Migrate DB
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!tenants.length">
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                                    No tenants registered yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Custom Domain Modal -->
        <Modal :show="showAddCustomDomainDialog" max-width="md" @close="showAddCustomDomainDialog = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Custom External Domain</h3>
                <form @submit.prevent="submitCustomDomain" class="space-y-4">
                    <div>
                        <InputLabel value="Target Tenant" />
                        <input :value="selectedTenant?.church_name" readonly disabled
                            class="w-full mt-1 rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-300" />
                    </div>
                    <div>
                        <InputLabel for="custom_domain" value="Custom Domain Name" />
                        <TextInput id="custom_domain" v-model="customDomainForm.domain" type="text"
                            placeholder="e.g. mis.elck.org or churchabc.com" class="w-full mt-1" />
                        <InputError :message="customDomainForm.errors.domain" class="mt-1" />
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <SecondaryButton @click="showAddCustomDomainDialog = false">Cancel</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="customDomainForm.processing">Save Domain</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
