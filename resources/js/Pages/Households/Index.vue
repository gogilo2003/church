<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PageToolbar from '@/Components/PageToolbar.vue';
import SearchBox from '@/Components/SearchBox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Paginator from '@/Components/Paginator.vue';

interface MemberMinimal {
    id: number;
    first_name: string;
    last_name: string;
    phone: string;
}

interface HouseholdItem {
    id: number;
    name: string;
    primary_contact_phone: string | null;
    address: string | null;
    marriage_date: string | null;
    org_unit?: { id: number; name: string } | null;
    members?: { id: number; first_name: string; last_name: string }[];
}

const props = defineProps<{
    households: {
        data: HouseholdItem[];
        links: any[];
    };
    filters: { search?: string };
    availableMembers: MemberMinimal[];
    orgUnits: { id: number; name: string }[];
}>();

const search = ref(props.filters.search || '');
const showModal = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: 0,
    name: '',
    org_unit_id: '',
    primary_contact_phone: '',
    address: '',
    marriage_date: '',
    member_relationships: [] as { member_id: number; relationship: string }[],
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (household: HouseholdItem) => {
    isEditing.value = true;
    form.clearErrors();
    form.id = household.id;
    form.name = household.name;
    form.org_unit_id = household.org_unit?.id ? String(household.org_unit.id) : '';
    form.primary_contact_phone = household.primary_contact_phone || '';
    form.address = household.address || '';
    form.marriage_date = household.marriage_date || '';
    form.member_relationships = (household.members || []).map((m) => ({
        member_id: m.id,
        relationship: 'head',
    }));
    showModal.value = true;
};

const addMemberRow = () => {
    if (props.availableMembers.length > 0) {
        form.member_relationships.push({
            member_id: props.availableMembers[0].id,
            relationship: 'child',
        });
    }
};

const removeMemberRow = (index: number) => {
    form.member_relationships.splice(index, 1);
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('households.update', form.id), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('households.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const applySearch = () => {
    router.get(route('households.index'), { search: search.value }, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Households & Family Units" />

    <AppLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
            <PageHeader
                title="Households & Family Units"
                description="Organize church members into family units, assign family heads, track marriages, and joint contacts."
            >
                <template #actions>
                    <PrimaryButton @click="openCreateModal">
                        + Create Household Unit
                    </PrimaryButton>
                </template>
            </PageHeader>

            <PageToolbar>
                <template #search>
                    <SearchBox v-model="search" @search="applySearch" placeholder="Search households..." />
                </template>
            </PageToolbar>

            <!-- Household Card Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="house in households.data"
                    :key="house.id"
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:border-gray-300 transition-all"
                >
                    <div class="space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-base text-gray-900 dark:text-white">
                                    {{ house.name }}
                                </h3>
                                <p v-if="house.org_unit" class="text-xs text-gray-500">
                                    🏛️ {{ house.org_unit.name }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50">
                                {{ house.members?.length || 0 }} Members
                            </span>
                        </div>

                        <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                            <div v-if="house.primary_contact_phone">
                                <span class="text-gray-400">Contact:</span> {{ house.primary_contact_phone }}
                            </div>
                            <div v-if="house.address">
                                <span class="text-gray-400">Address:</span> {{ house.address }}
                            </div>
                        </div>

                        <!-- Member Chips -->
                        <div v-if="house.members && house.members.length" class="pt-2 border-t border-gray-100 dark:border-gray-800 flex flex-wrap gap-1">
                            <span
                                v-for="m in house.members"
                                :key="m.id"
                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                            >
                                👤 {{ m.first_name }} {{ m.last_name }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2">
                        <button
                            @click="openEditModal(house)"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-xs font-semibold"
                        >
                            Edit Unit
                        </button>
                    </div>
                </div>
            </div>

            <Paginator :items="households as any" />
        </div>

        <!-- Household Modal Dialog -->
        <Modal :show="showModal" max-width="lg" @close="showModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    {{ isEditing ? 'Edit Household Unit' : 'Create New Household Unit' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Household / Family Name *" />
                        <TextInput id="name" v-model="form.name" type="text" placeholder="e.g. The Johnsons Household" class="w-full mt-1" required />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="primary_contact_phone" value="Primary Phone" />
                            <TextInput id="primary_contact_phone" v-model="form.primary_contact_phone" type="text" class="w-full mt-1" />
                        </div>
                        <div>
                            <InputLabel for="org_unit_id" value="Parish / Unit" />
                            <select id="org_unit_id" v-model="form.org_unit_id" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                <option value="">Select Parish</option>
                                <option v-for="unit in orgUnits" :key="unit.id" :value="unit.id">
                                    {{ unit.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <InputLabel for="address" value="Residential Address" />
                        <TextInput id="address" v-model="form.address" type="text" class="w-full mt-1" />
                    </div>

                    <!-- Family Member Assignments -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                Household Members & Roles
                            </h4>
                            <button
                                type="button"
                                @click="addMemberRow"
                                class="text-xs text-indigo-600 font-semibold hover:text-indigo-800"
                            >
                                + Add Member
                            </button>
                        </div>

                        <div v-for="(row, idx) in form.member_relationships" :key="idx" class="flex items-center gap-2 mb-2">
                            <select v-model="row.member_id" class="flex-1 text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                <option v-for="m in availableMembers" :key="m.id" :value="m.id">
                                    {{ m.first_name }} {{ m.last_name }} ({{ m.phone }})
                                </option>
                            </select>
                            <select v-model="row.relationship" class="w-32 text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                <option value="head">Head</option>
                                <option value="spouse">Spouse</option>
                                <option value="child">Child</option>
                                <option value="parent">Parent</option>
                                <option value="guardian">Guardian</option>
                                <option value="other">Other</option>
                            </select>
                            <button type="button" @click="removeMemberRow(idx)" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-1">
                                &times;
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <SecondaryButton @click="showModal = false">Cancel</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="form.processing">Save Household</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
