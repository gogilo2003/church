<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

interface MemberDetail {
    id: number;
    member_number: string | null;
    full_name: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    gender: string;
    marital_status: string;
    phone: string;
    email: string | null;
    occupation: string | null;
    national_id: string | null;
    address: string | null;
    status: string;
    photo_url: string;
    date_of_birth: string | null;
    date_joined: string | null;
    spiritual_milestones: {
        baptized?: boolean;
        confirmed?: boolean;
        communion?: boolean;
    } | null;
    household?: { id: number; name: string } | null;
    org_unit?: { id: number; name: string } | null;
    lifecycle_logs?: {
        id: number;
        from_status: string;
        to_status: string;
        reason: string | null;
        created_at: string;
        changed_by_user?: { name: string } | null;
    }[];
}

const props = defineProps<{
    member: MemberDetail;
}>();

const showStatusModal = ref(false);

const statusForm = useForm({
    status: props.member.status,
    reason: '',
});

const submitStatusChange = () => {
    statusForm.patch(route('members.status', props.member.id), {
        onSuccess: () => {
            showStatusModal.value = false;
            statusForm.reset('reason');
        },
    });
};
</script>

<template>
    <Head :title="`Member Profile - ${member.full_name}`" />

    <AppLayout>
        <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
            <PageHeader
                :title="member.full_name"
                :description="`Member Number: ${member.member_number || '#MEM-' + member.id} • Registered Member Profile`"
                :back-route="route('members.index')"
            >
                <template #actions>
                    <SecondaryButton @click="showStatusModal = true">
                        Update Status
                    </SecondaryButton>
                    <Link :href="route('members.edit', member.id)">
                        <PrimaryButton>
                            Edit Profile
                        </PrimaryButton>
                    </Link>
                </template>
            </PageHeader>

            <!-- Profile Summary Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <img
                        :src="member.photo_url"
                        :alt="member.full_name"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700 shadow-sm"
                    />
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ member.full_name }}
                            </h2>
                            <StatusBadge :status="member.status" />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Phone: {{ member.phone }} • Email: {{ member.email || 'N/A' }}
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-300 mt-2 font-medium">
                            <span v-if="member.household" class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded">
                                🏠 Household: {{ member.household.name }}
                            </span>
                            <span v-if="member.org_unit" class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded">
                                🏛️ Parish: {{ member.org_unit.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-800 pt-4 md:pt-0 md:pl-6 text-center w-full md:w-auto">
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Baptized</div>
                        <div class="text-lg font-bold" :class="member.spiritual_milestones?.baptized ? 'text-emerald-600' : 'text-gray-300'">
                            {{ member.spiritual_milestones?.baptized ? '✓ Yes' : '✗ No' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Confirmed</div>
                        <div class="text-lg font-bold" :class="member.spiritual_milestones?.confirmed ? 'text-emerald-600' : 'text-gray-300'">
                            {{ member.spiritual_milestones?.confirmed ? '✓ Yes' : '✗ No' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Communion</div>
                        <div class="text-lg font-bold" :class="member.spiritual_milestones?.communion ? 'text-emerald-600' : 'text-gray-300'">
                            {{ member.spiritual_milestones?.communion ? '✓ Yes' : '✗ No' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Details Card -->
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider pb-2 border-b border-gray-100 dark:border-gray-800">
                        Personal & Demographics
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400">Gender</span>
                            <div class="font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ member.gender }}</div>
                        </div>
                        <div>
                            <span class="text-gray-400">Marital Status</span>
                            <div class="font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ member.marital_status }}</div>
                        </div>
                        <div>
                            <span class="text-gray-400">Occupation</span>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ member.occupation || '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-400">National ID</span>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ member.national_id || '-' }}</div>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400">Physical Address</span>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ member.address || 'No address specified' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Lifecycle History Logs -->
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider pb-2 border-b border-gray-100 dark:border-gray-800">
                        Status Lifecycle Transition History
                    </h3>

                    <div v-if="member.lifecycle_logs && member.lifecycle_logs.length" class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                        <div
                            v-for="log in member.lifecycle_logs"
                            :key="log.id"
                            class="p-3 rounded-md bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-start justify-between text-xs"
                        >
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white capitalize">
                                    {{ log.from_status }} → {{ log.to_status }}
                                </div>
                                <div v-if="log.reason" class="text-gray-500 italic mt-0.5">
                                    "{{ log.reason }}"
                                </div>
                            </div>
                            <div class="text-right text-[10px] text-gray-400">
                                {{ new Date(log.created_at).toLocaleDateString() }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-xs text-gray-400 italic py-4 text-center">
                        No status transitions recorded yet.
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Change Modal -->
        <Modal :show="showStatusModal" max-width="md" @close="showStatusModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Update Member Lifecycle Status
                </h3>
                <form @submit.prevent="submitStatusChange" class="space-y-4">
                    <div>
                        <InputLabel value="New Lifecycle Status *" />
                        <select v-model="statusForm.status" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                            <option value="active">Active Member</option>
                            <option value="new_member">New Member</option>
                            <option value="new_convert">New Convert</option>
                            <option value="visitor">Visitor</option>
                            <option value="inactive">Inactive</option>
                            <option value="transferred">Transferred</option>
                            <option value="suspended">Suspended</option>
                            <option value="deceased">Deceased</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel value="Reason / Transition Notes" />
                        <TextInput v-model="statusForm.reason" type="text" placeholder="e.g. Relocated to another parish" class="w-full mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <SecondaryButton @click="showStatusModal = false">Cancel</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="statusForm.processing">Save Status</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
