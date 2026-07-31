<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import axios from 'axios';

const form = useForm({
    church_name: '',
    subdomain: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
    admin_password_confirmation: '',
    phone: '',
});

const isCheckingSubdomain = ref(false);
const subdomainAvailable = ref<boolean | null>(null);
const subdomainMessage = ref('');

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const checkSubdomainAvailability = (value: string) => {
    subdomainAvailable.value = null;
    subdomainMessage.value = '';

    if (!value || value.length < 3) {
        return;
    }

    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(async () => {
        isCheckingSubdomain.value = true;
        try {
            const response = await axios.get<{ available: boolean; message: string }>(
                route('central.check-subdomain'),
                { params: { subdomain: value } }
            );
            subdomainAvailable.value = response.data.available;
            subdomainMessage.value = response.data.message;
        } catch {
            subdomainAvailable.value = false;
            subdomainMessage.value = 'Failed to check subdomain availability.';
        } finally {
            isCheckingSubdomain.value = false;
        }
    }, 400);
};

watch(() => form.subdomain, (val) => {
    // Sanitize input to lowercase alphanumeric
    form.subdomain = val.toLowerCase().replace(/[^a-z0-9]/g, '');
    checkSubdomainAvailability(form.subdomain);
});

const submit = () => {
    form.post(route('central.register-tenant.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Register Your Church Workspace" />

    <div class="min-h-screen bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-gray-100">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h1 class="text-3xl font-extrabold text-white">Church SaaS Platform</h1>
            <p class="mt-2 text-sm text-gray-400">Set up your church's isolated workspace in seconds</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="bg-gray-800 py-8 px-6 shadow-xl rounded-2xl sm:px-10 border border-gray-700">
                <div class="mb-6 bg-blue-900/30 border border-blue-500/40 p-4 rounded-xl flex items-center gap-3">
                    <p class="text-xs text-blue-200">
                        <strong>Free Onboarding:</strong> No credit card or upfront payment required. Enjoy full access to church management tools immediately.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel for="church_name" value="Church Name" class="text-gray-300" />
                        <TextInput
                            id="church_name"
                            v-model="form.church_name"
                            type="text"
                            placeholder="e.g. Grace Community Church"
                            class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                        />
                        <InputError :message="form.errors.church_name" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="subdomain" value="Workspace Subdomain" class="text-gray-300" />
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <TextInput
                                id="subdomain"
                                v-model="form.subdomain"
                                type="text"
                                placeholder="e.g. grace"
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-r-none"
                            />
                            <span class="inline-flex items-center px-4 rounded-r-md border border-l-0 border-gray-700 bg-gray-700 text-gray-300 text-sm font-medium">
                                .church.test
                            </span>
                        </div>
                        <div v-if="isCheckingSubdomain" class="text-xs text-yellow-400 mt-1">
                            Checking availability...
                        </div>
                        <div v-else-if="subdomainAvailable === true" class="text-xs text-green-400 mt-1">
                            ✓ {{ subdomainMessage }}
                        </div>
                        <div v-else-if="subdomainAvailable === false" class="text-xs text-red-400 mt-1">
                            ✕ {{ subdomainMessage }}
                        </div>
                        <InputError :message="form.errors.subdomain" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="admin_name" value="Admin Name" class="text-gray-300" />
                            <TextInput
                                id="admin_name"
                                v-model="form.admin_name"
                                type="text"
                                placeholder="e.g. Pastor John Doe"
                                class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                            />
                            <InputError :message="form.errors.admin_name" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="phone" value="Contact Phone" class="text-gray-300" />
                            <TextInput
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                placeholder="e.g. +254712345678"
                                class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                            />
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="admin_email" value="Admin Email Address" class="text-gray-300" />
                        <TextInput
                            id="admin_email"
                            v-model="form.admin_email"
                            type="email"
                            placeholder="admin@gracechurch.org"
                            class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                        />
                        <InputError :message="form.errors.admin_email" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="admin_password" value="Password" class="text-gray-300" />
                            <TextInput
                                id="admin_password"
                                v-model="form.admin_password"
                                type="password"
                                class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                            />
                            <InputError :message="form.errors.admin_password" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="admin_password_confirmation" value="Confirm Password" class="text-gray-300" />
                            <TextInput
                                id="admin_password_confirmation"
                                v-model="form.admin_password_confirmation"
                                type="password"
                                class="w-full mt-1 bg-gray-900 border-gray-700 text-white"
                            />
                        </div>
                    </div>

                    <div class="pt-4">
                        <PrimaryButton
                            type="submit"
                            class="w-full justify-center py-3 rounded-xl text-sm font-bold uppercase tracking-wider"
                            :disabled="form.processing"
                        >
                            Create Church Workspace
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
