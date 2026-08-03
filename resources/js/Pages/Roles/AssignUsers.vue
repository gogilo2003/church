<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DualListBox, { DualListItem } from '@/Components/DualListBox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface RoleData {
    id: number;
    title: string;
    description?: string;
}

interface UserItem {
    id: number;
    name: string;
    email: string;
    status: string;
}

const props = defineProps<{
    role: {
        data: RoleData;
    };
    allUsers: {
        data: UserItem[];
    };
    assignedUserIds: number[];
}>();

const form = useForm({
    user_ids: props.assignedUserIds || [],
});

// Map users array to DualListItem format
const dualListItems = computed<DualListItem[]>(() => {
    return props.allUsers.data.map((user) => ({
        value: user.id,
        label: `${user.name} (${user.email})`,
        disabled: user.status === 'suspended',
    }));
});

const submit = () => {
    form.post(route('roles.users.sync', props.role.data.id));
};
</script>

<template>
    <AppLayout :title="`Assign Users - ${role.data.title}`">
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                :title="`Assign Users: ${role.data.title}`"
                description="Transfer users between the Available and Assigned lists to grant or revoke this role."
                :back-route="route('roles.index')"
            />

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Dual List Box Component -->
                <DualListBox
                    v-model="form.user_ids"
                    :items="dualListItems"
                    available-title="Available Users"
                    :selected-title="`Users with '${role.data.title}' Role`"
                    placeholder="Search by user name or email..."
                />

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        Currently <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ form.user_ids.length }}</span> users assigned to this role.
                    </div>
                    <div class="flex items-center gap-3">
                        <Link :href="route('roles.index')">
                            <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                        <PrimaryButton @click="submit" :disabled="form.processing">
                            Save User Assignments
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
