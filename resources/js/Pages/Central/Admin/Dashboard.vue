<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface StatItem {
    name: string;
    value: string | number;
    icon: string;
    color: string;
}

interface RecentTenant {
    id: string;
    church_name: string;
    admin_email: string | null;
    subdomain: string;
    custom_domains: string[];
    created_at: string;
}

defineProps<{
    stats: StatItem[];
    recentTenants: RecentTenant[];
}>();
</script>

<template>
    <Head title="Central Admin Dashboard" />

    <AppLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                        Central Platform Dashboard
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        System-wide monitoring, multi-tenant workspace administration, and domain controls.
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('central.register-tenant.create')">
                        <PrimaryButton class="text-xs uppercase tracking-wider">
                            + Provision Tenant
                        </PrimaryButton>
                    </Link>
                    <Link :href="route('central.admin.tenants.index')">
                        <SecondaryButton class="text-xs">
                            Manage Workspaces
                        </SecondaryButton>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Platform Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ stat.name }}
                        </span>
                        <span
                            class="inline-flex items-center justify-center p-2 rounded-xl text-xs font-bold"
                            :class="{
                                'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': stat.color === 'blue',
                                'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300': stat.color === 'indigo',
                                'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300': stat.color === 'green',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300': stat.color === 'emerald',
                            }"
                        >
                            ● Active
                        </span>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white">
                            {{ stat.value }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content Area: Recent Onboarding Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Recent Church Onboarding Activity
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Latest registered church workspaces and their domain routing configuration.
                        </p>
                    </div>
                    <Link :href="route('central.admin.tenants.index')" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        View All Tenants &rarr;
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Church Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subdomain</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Custom Domains</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="tenant in recentTenants" :key="tenant.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ tenant.church_name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                        {{ tenant.subdomain }}.church.test
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ tenant.admin_email || '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="domain in tenant.custom_domains" :key="domain" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                                            {{ domain }}
                                        </span>
                                        <span v-if="!tenant.custom_domains.length" class="text-xs text-gray-400">None</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ tenant.created_at }}
                                </td>
                            </tr>
                            <tr v-if="!recentTenants.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No churches registered yet. <Link :href="route('central.register-tenant.create')" class="text-indigo-600 font-semibold hover:underline">Click here to register the first church.</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
