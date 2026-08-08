<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps<{
    voteHeads: Array<any>;
}>();

const form = useForm({
    code: '',
    name: '',
    type: 'income',
    description: '',
    parent_id: null as number | null,
});

const submit = () => {
    form.post(route('setup-vote-heads-store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Setup Vote Heads / Chart of Accounts" />

    <div class="min-h-screen bg-gray-900 text-gray-100 p-6 sm:p-10">
        <div class="max-w-6xl mx-auto space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Vote Heads & Chart of Accounts</h1>
                <p class="text-sm text-gray-400 mt-1">Manage financial account codes for income, expenses, assets, and liabilities tracking</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Create Vote Head -->
                <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 space-y-4">
                    <h2 class="text-lg font-bold text-white mb-2">Create Vote Head</h2>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel value="Account Code" class="text-gray-300 text-xs" />
                            <TextInput v-model="form.code" type="text" placeholder="e.g. 4100" class="w-full mt-1 bg-gray-900 text-xs" required />
                            <InputError :message="form.errors.code" />
                        </div>

                        <div>
                            <InputLabel value="Account Name" class="text-gray-300 text-xs" />
                            <TextInput v-model="form.name" type="text" placeholder="e.g. Tithe Collections" class="w-full mt-1 bg-gray-900 text-xs" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel value="Account Type" class="text-gray-300 text-xs" />
                            <select v-model="form.type" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                                <option value="asset">Asset</option>
                                <option value="liability">Liability</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel value="Parent Vote Head (Optional)" class="text-gray-300 text-xs" />
                            <select v-model="form.parent_id" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option :value="null">None (Top-Level Account)</option>
                                <option v-for="vh in voteHeads" :key="vh.id" :value="vh.id">{{ vh.code }} - {{ vh.name }}</option>
                            </select>
                        </div>

                        <PrimaryButton :disabled="form.processing" class="w-full justify-center bg-blue-600 hover:bg-blue-500 py-2.5 text-xs">
                            Save Vote Head
                        </PrimaryButton>
                    </form>
                </div>

                <!-- List Vote Heads -->
                <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl border border-gray-700">
                    <h2 class="text-lg font-bold text-white mb-4">Configured Vote Heads</h2>

                    <div v-if="voteHeads.length === 0" class="text-center py-12 text-gray-400 text-sm">
                        No vote heads created yet.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-300">
                            <thead class="bg-gray-900/80 text-gray-400 uppercase font-semibold">
                                <tr>
                                    <th class="p-3">Code</th>
                                    <th class="p-3">Name</th>
                                    <th class="p-3">Type</th>
                                    <th class="p-3">Parent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                <tr v-for="vh in voteHeads" :key="vh.id">
                                    <td class="p-3 font-mono font-bold text-white">{{ vh.code }}</td>
                                    <td class="p-3 font-semibold">{{ vh.name }}</td>
                                    <td class="p-3">
                                        <span
                                            :class="{
                                                'bg-emerald-900/50 text-emerald-300 border-emerald-700/50': vh.type === 'income',
                                                'bg-rose-900/50 text-rose-300 border-rose-700/50': vh.type === 'expense',
                                                'bg-blue-900/50 text-blue-300 border-blue-700/50': vh.type === 'asset',
                                                'bg-amber-900/50 text-amber-300 border-amber-700/50': vh.type === 'liability'
                                            }"
                                            class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border"
                                        >
                                            {{ vh.type }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-400">{{ vh.parent ? `${vh.parent.code} - ${vh.parent.name}` : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
