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

interface RoleData {
    id: number;
    title: string;
    name?: string;
    description?: string;
    is_system_role: boolean;
}

const props = defineProps<{
    role: {
        data: RoleData;
    };
}>();

const form = useForm({
    title: props.role.data.title,
    name: props.role.data.name || '',
    description: props.role.data.description || '',
    is_system_role: props.role.data.is_system_role,
});

const submit = () => {
    form.patch(route('roles.update', props.role.data.id));
};
</script>

<template>
    <AppLayout title="Edit Role">
        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                :title="`Edit Role: ${role.data.title}`"
                description="Update role title, system slug, description, or system protection status."
                :back-route="route('roles.index')"
            />

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Role Title *" />
                        <TextInput
                            id="title"
                            type="text"
                            v-model="form.title"
                            class="mt-1 block w-full text-sm"
                            required
                        />
                        <InputError class="mt-1" :message="form.errors.title" />
                    </div>

                    <!-- Slug Name -->
                    <div>
                        <InputLabel for="name" value="System Slug Name" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            class="mt-1 block w-full text-sm font-mono"
                        />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="description" value="Description" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full text-sm rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 p-3"
                        ></textarea>
                        <InputError class="mt-1" :message="form.errors.description" />
                    </div>

                    <!-- System Role Toggle -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 rounded-xl flex items-start gap-3">
                        <Checkbox id="is_system_role" v-model:checked="form.is_system_role" class="mt-0.5" />
                        <div>
                            <label for="is_system_role" class="font-bold text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                                Mark as System Role
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Protected core system roles cannot be deleted by users.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('roles.index')">
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
