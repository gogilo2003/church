<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('central.admin.login.store'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Central Platform Admin Login" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 transition-colors duration-300">
        <!-- Logo Area -->
        <div class="mb-6 flex flex-col items-center gap-2">
            <ApplicationLogo class="w-16 h-16 fill-current text-indigo-600 dark:text-indigo-400" />
            <h1 class="text-2xl font-black tracking-tight">Central Platform Control</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">SaaS Administration & Multi-Tenant Management</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-400">
            {{ status }}
        </div>

        <!-- Form Card reusing existing Vue components & supports Light/Dark theme -->
        <div class="w-full sm:max-w-md px-8 py-8 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl rounded-2xl transition-all">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="email" value="Platform Admin Email" class="dark:text-gray-300" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="dark:text-gray-300" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Keep me logged in</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <PrimaryButton class="w-full" :disabled="form.processing">
                        Authenticate Platform Access &rarr;
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-600">
            Internal Platform Operations • Church SaaS Management
        </div>
    </div>
</template>
