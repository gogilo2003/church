<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface RoleOption {
    id: number;
    title: string;
    description?: string;
}

interface UserData {
    id: number;
    name: string;
    email: string;
    username?: string;
    phone_number?: string;
    status: string;
    role_ids?: number[];
}

const props = defineProps<{
    user: {
        data: UserData;
    };
    roles: {
        data: RoleOption[];
    };
}>();

const form = useForm({
    name: props.user.data.name,
    email: props.user.data.email,
    username: props.user.data.username || '',
    phone_number: props.user.data.phone_number || '',
    status: props.user.data.status,
    password: '',
    password_confirmation: '',
    role_ids: props.user.data.role_ids || [],
});

const toggleRole = (roleId: number) => {
    if (form.role_ids.includes(roleId)) {
        form.role_ids = form.role_ids.filter((id) => id !== roleId);
    } else {
        form.role_ids.push(roleId);
    }
};

const submit = () => {
    form.patch(route('users.update', props.user.data.id));
};
</script>

<template>
    <AppLayout title="Edit User">
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                :title="`Edit User: ${user.data.name}`"
                description="Update user account details, phone number, status, and assigned roles."
                :back-route="route('users.index')"
            />

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Personal Details -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">
                            1. Personal Details
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <InputLabel for="name" value="Full Name *" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    class="mt-1 block w-full text-sm"
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.name" />
                            </div>

                            <!-- Email Address -->
                            <div>
                                <InputLabel for="email" value="Email Address *" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    class="mt-1 block w-full text-sm"
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.email" />
                            </div>

                            <!-- Username -->
                            <div>
                                <InputLabel for="username" value="Username (Optional)" />
                                <TextInput
                                    id="username"
                                    type="text"
                                    v-model="form.username"
                                    class="mt-1 block w-full text-sm"
                                />
                                <InputError class="mt-1" :message="form.errors.username" />
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <InputLabel for="phone_number" value="Phone Number (Optional)" />
                                <TextInput
                                    id="phone_number"
                                    type="text"
                                    v-model="form.phone_number"
                                    class="mt-1 block w-full text-sm"
                                />
                                <InputError class="mt-1" :message="form.errors.phone_number" />
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <!-- Account Status & Optional Password -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">
                            2. Account Status & Password Update
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="Account Status *" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3"
                                >
                                    <option value="active">Active (Can Log In)</option>
                                    <option value="suspended">Suspended (Access Blocked)</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.status" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Optional Password -->
                            <div>
                                <InputLabel for="password" value="New Password (Leave blank to keep unchanged)" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    class="mt-1 block w-full text-sm"
                                />
                                <InputError class="mt-1" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <InputLabel for="password_confirmation" value="Confirm New Password" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="mt-1 block w-full text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <!-- Roles -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">
                            3. Role Assignments
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="role in roles.data"
                                :key="role.id"
                                @click="toggleRole(role.id)"
                                :class="[
                                    'p-4 rounded-xl border cursor-pointer transition-all flex items-start gap-3',
                                    form.role_ids.includes(role.id)
                                        ? 'bg-indigo-50/60 dark:bg-indigo-950/40 border-indigo-500 text-indigo-900 dark:text-indigo-200'
                                        : 'bg-gray-50 dark:bg-gray-800/40 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-300'
                                ]"
                            >
                                <Checkbox :checked="form.role_ids.includes(role.id)" class="mt-0.5" />
                                <div>
                                    <div class="font-bold text-sm">{{ role.title }}</div>
                                    <div v-if="role.description" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.role_ids" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('users.index')">
                            <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
