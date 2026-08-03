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

const props = defineProps<{
    roles: {
        data: RoleOption[];
    };
}>();

const form = useForm({
    name: '',
    email: '',
    username: '',
    phone_number: '',
    status: 'active',
    password: '',
    password_confirmation: '',
    role_ids: [] as number[],
});

const toggleRole = (roleId: number) => {
    if (form.role_ids.includes(roleId)) {
        form.role_ids = form.role_ids.filter((id) => id !== roleId);
    } else {
        form.role_ids.push(roleId);
    }
};

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <AppLayout title="Create New User">
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                title="Create New User"
                description="Add a new user to the system and assign appropriate roles."
                :back-route="route('users.index')"
            />

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information Section -->
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
                                    autofocus
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

                    <!-- Status & Credentials -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">
                            2. Account & Credentials
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="Initial Account Status *" />
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
                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Password *" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    class="mt-1 block w-full text-sm"
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <InputLabel for="password_confirmation" value="Confirm Password *" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="mt-1 block w-full text-sm"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <!-- Role Assignments -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4">
                            3. Role Assignments
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Select one or more roles to grant to this user.
                        </p>

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

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('users.index')">
                            <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Create User
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
