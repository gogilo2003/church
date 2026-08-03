<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps<{
    orgUnits: { id: number; name: string }[];
    households: { id: number; name: string }[];
}>();

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    gender: 'male',
    marital_status: 'single',
    phone: '',
    email: '',
    occupation: '',
    national_id: '',
    date_of_birth: '',
    date_joined: '',
    status: 'active',
    org_unit_id: '',
    household_id: '',
    address: '',
    spiritual_milestones: {
        baptized: false,
        baptism_date: '',
        confirmed: false,
        communion: false,
    },
});

const submit = () => {
    form.post(route('members.store'));
};
</script>

<template>
    <Head title="Register Member" />

    <AppLayout>
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PageHeader
                title="Register New Church Member"
                description="Capture personal details, contact information, household, and spiritual milestones."
                :back-route="route('members.index')"
            />

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Personal Information Section -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">
                            1. Personal & Contact Profile
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="first_name" value="First Name *" />
                                <TextInput id="first_name" v-model="form.first_name" type="text" class="w-full mt-1" required />
                                <InputError :message="form.errors.first_name" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="middle_name" value="Middle Name" />
                                <TextInput id="middle_name" v-model="form.middle_name" type="text" class="w-full mt-1" />
                                <InputError :message="form.errors.middle_name" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="last_name" value="Last Name *" />
                                <TextInput id="last_name" v-model="form.last_name" type="text" class="w-full mt-1" required />
                                <InputError :message="form.errors.last_name" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div>
                                <InputLabel for="gender" value="Gender *" />
                                <select id="gender" v-model="form.gender" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="marital_status" value="Marital Status" />
                                <select id="marital_status" v-model="form.marital_status" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widowed">Widowed</option>
                                    <option value="divorced">Divorced</option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="date_of_birth" value="Date of Birth" />
                                <TextInput id="date_of_birth" v-model="form.date_of_birth" type="date" class="w-full mt-1" />
                                <InputError :message="form.errors.date_of_birth" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <InputLabel for="phone" value="Phone Number *" />
                                <TextInput id="phone" v-model="form.phone" type="text" placeholder="+254 700 000 000" class="w-full mt-1" />
                                <InputError :message="form.errors.phone" class="mt-1" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Address" />
                                <TextInput id="email" v-model="form.email" type="email" placeholder="member@church.org" class="w-full mt-1" />
                                <InputError :message="form.errors.email" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <InputLabel for="occupation" value="Occupation / Profession" />
                                <TextInput id="occupation" v-model="form.occupation" type="text" class="w-full mt-1" />
                            </div>

                            <div>
                                <InputLabel for="national_id" value="National ID / Passport" />
                                <TextInput id="national_id" v-model="form.national_id" type="text" class="w-full mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Organization & Household Linkage -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">
                            2. Organizational Unit & Household
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="org_unit_id" value="Parish / Organizational Unit" />
                                <select id="org_unit_id" v-model="form.org_unit_id" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    <option value="">Select Parish / Unit</option>
                                    <option v-for="unit in orgUnits" :key="unit.id" :value="unit.id">
                                        {{ unit.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="household_id" value="Assigned Household" />
                                <select id="household_id" v-model="form.household_id" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    <option value="">Select Household (Optional)</option>
                                    <option v-for="house in households" :key="house.id" :value="house.id">
                                        {{ house.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="status" value="Initial Lifecycle Status *" />
                                <select id="status" v-model="form.status" class="w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    <option value="active">Active Member</option>
                                    <option value="new_member">New Member</option>
                                    <option value="new_convert">New Convert</option>
                                    <option value="visitor">Visitor</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Spiritual Milestones -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 dark:border-gray-800">
                            3. Spiritual Milestones
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-800/40 rounded-md border border-gray-200 dark:border-gray-700">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model:checked="form.spiritual_milestones.baptized" />
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Water Baptized</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model:checked="form.spiritual_milestones.confirmed" />
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Confirmed</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model:checked="form.spiritual_milestones.communion" />
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Holy Communion</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('members.index')">
                            <SecondaryButton>Cancel</SecondaryButton>
                        </Link>
                        <PrimaryButton type="submit" :disabled="form.processing">
                            Save & Register Member
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
