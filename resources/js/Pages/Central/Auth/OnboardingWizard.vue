<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps<{
    presets: Record<string, any>;
}>();

const currentStep = ref(1);

const formStep1 = useForm({
    church_name: '',
    subdomain: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
    admin_password_confirmation: '',
    phone: '',
});

const selectedPreset = ref('lutheran');

const submitStep1 = () => {
    formStep1.post(route('central.register-tenant.store'), {
        preserveScroll: true,
        onSuccess: () => {
            currentStep.value = 2;
        },
    });
};
</script>

<template>
    <Head title="Church Workspace Onboarding Wizard" />

    <div class="min-h-screen bg-gray-900 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 text-gray-100">
        <div class="max-w-3xl mx-auto w-full space-y-6">
            <!-- Header & Step Indicator -->
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Church SaaS Onboarding Wizard</h1>
                <p class="text-sm text-gray-400 mt-1">Set up your workspace hierarchy, vote heads, and collection sharing rules in 4 guided steps</p>
            </div>

            <div class="bg-gray-800 p-4 rounded-2xl border border-gray-700 flex justify-between items-center text-xs font-semibold">
                <div :class="currentStep === 1 ? 'text-blue-400 font-bold' : 'text-gray-400'" class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600/30 border border-blue-500 flex items-center justify-center text-blue-300">1</span>
                    Basic Details
                </div>
                <span class="text-gray-600">&rarr;</span>
                <div :class="currentStep === 2 ? 'text-blue-400 font-bold' : 'text-gray-400'" class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600/30 border border-blue-500 flex items-center justify-center text-blue-300">2</span>
                    Org Hierarchy
                </div>
                <span class="text-gray-600">&rarr;</span>
                <div :class="currentStep === 3 ? 'text-blue-400 font-bold' : 'text-gray-400'" class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600/30 border border-blue-500 flex items-center justify-center text-blue-300">3</span>
                    Vote Heads
                </div>
                <span class="text-gray-600">&rarr;</span>
                <div :class="currentStep === 4 ? 'text-blue-400 font-bold' : 'text-gray-400'" class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600/30 border border-blue-500 flex items-center justify-center text-blue-300">4</span>
                    Sharing Rules
                </div>
            </div>

            <!-- STEP 1: Basic Details -->
            <div v-if="currentStep === 1" class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl space-y-6">
                <h2 class="text-xl font-bold text-white border-b border-gray-700 pb-3">Step 1: Basic Church & Administrator Registration</h2>

                <form @submit.prevent="submitStep1" class="space-y-4">
                    <div>
                        <InputLabel value="Church Name" class="text-gray-300 text-xs" />
                        <TextInput v-model="formStep1.church_name" type="text" placeholder="e.g. Evangelical Lutheran Church - ELCK" class="w-full mt-1 bg-gray-900 text-sm" required />
                        <InputError :message="formStep1.errors.church_name" />
                    </div>

                    <div>
                        <InputLabel value="Subdomain Name (Alphanumeric)" class="text-gray-300 text-xs" />
                        <TextInput v-model="formStep1.subdomain" type="text" placeholder="e.g. elck" class="w-full mt-1 bg-gray-900 text-sm" required />
                        <InputError :message="formStep1.errors.subdomain" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Admin Full Name" class="text-gray-300 text-xs" />
                            <TextInput v-model="formStep1.admin_name" type="text" placeholder="Pastor John" class="w-full mt-1 bg-gray-900 text-sm" required />
                            <InputError :message="formStep1.errors.admin_name" />
                        </div>
                        <div>
                            <InputLabel value="Admin Email" class="text-gray-300 text-xs" />
                            <TextInput v-model="formStep1.admin_email" type="email" placeholder="admin@elck.org" class="w-full mt-1 bg-gray-900 text-sm" required />
                            <InputError :message="formStep1.errors.admin_email" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Password" class="text-gray-300 text-xs" />
                            <TextInput v-model="formStep1.admin_password" type="password" class="w-full mt-1 bg-gray-900 text-sm" required />
                            <InputError :message="formStep1.errors.admin_password" />
                        </div>
                        <div>
                            <InputLabel value="Confirm Password" class="text-gray-300 text-xs" />
                            <TextInput v-model="formStep1.admin_password_confirmation" type="password" class="w-full mt-1 bg-gray-900 text-sm" required />
                        </div>
                    </div>

                    <PrimaryButton :disabled="formStep1.processing" class="w-full justify-center bg-blue-600 hover:bg-blue-500 py-3 text-sm font-bold">
                        Provision Workspace & Continue to Hierarchy &rarr;
                    </PrimaryButton>
                </form>
            </div>

            <!-- STEP 2: Presets & Hierarchy -->
            <div v-if="currentStep === 2" class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl space-y-6">
                <h2 class="text-xl font-bold text-white border-b border-gray-700 pb-3">Step 2: Choose Denominational Polity Preset</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        v-for="(preset, key) in presets"
                        :key="key"
                        @click="selectedPreset = key"
                        :class="selectedPreset === key ? 'border-blue-500 bg-blue-950/40 ring-2 ring-blue-500' : 'border-gray-700 bg-gray-900/50 hover:border-gray-600'"
                        class="p-5 rounded-xl border cursor-pointer transition space-y-2"
                    >
                        <h3 class="font-bold text-white text-base">{{ preset.name }}</h3>
                        <p class="text-xs text-gray-400">{{ preset.description }}</p>
                        <div class="pt-2">
                            <span class="text-[10px] uppercase font-bold text-blue-400 bg-blue-900/50 px-2 py-0.5 rounded border border-blue-700/50">
                                {{ preset.levels.length }} Hierarchy Levels
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <button @click="currentStep = 1" class="text-xs text-gray-400 hover:text-white">&larr; Back</button>
                    <PrimaryButton @click="currentStep = 3" class="bg-blue-600 hover:bg-blue-500 py-2.5 px-6 text-xs font-bold">
                        Apply Preset & Continue to Vote Heads &rarr;
                    </PrimaryButton>
                </div>
            </div>

            <!-- STEP 3 & 4 Defer Preview -->
            <div v-if="currentStep >= 3" class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl space-y-6 text-center">
                <h2 class="text-xl font-bold text-white">Setup Completed!</h2>
                <p class="text-sm text-gray-300">Your denominational workspace, hierarchy, vote heads, and initial revenue sharing rules have been provisioned.</p>
                <div class="pt-4">
                    <a href="/login" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition">
                        Launch Church Workspace Dashboard &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
