<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import axios from 'axios';

const props = defineProps<{
    rules: Array<any>;
    distributions: Array<any>;
    hierarchyLevels: Array<any>;
    orgUnits: Array<any>;
    offeringTypes: Array<any>;
    projects: Array<any>;
    voteHeads: Array<any>;
}>();

const activeTab = ref<'rules' | 'simulator' | 'ledger'>('rules');

const ruleForm = useForm({
    name: '',
    description: '',
    source_type: 'tithe',
    source_id: null as number | null,
    source_scope_type: 'global',
    source_scope_id: null as number | null,
    destination_type: 'parent',
    destination_id: null as number | null,
    direction: 'upward',
    priority: 1,
    calculation_type: 'percentage',
    value: 10,
    min_collection_threshold: 0,
    max_cap_amount: null as number | null,
});

const simForm = ref({
    source_type: 'tithe',
    source_id: null as number | null,
    source_unit_id: props.orgUnits[0]?.id || 1,
    gross_amount: 1000,
});

const simulationResults = ref<Array<any>>([]);
const isSimulating = ref(false);

const submitRule = () => {
    ruleForm.post(route('revenue-sharing.rules.store'), {
        preserveScroll: true,
        onSuccess: () => {
            ruleForm.reset();
        },
    });
};

const runSimulation = async () => {
    isSimulating.value = true;
    try {
        const response = await axios.post(route('revenue-sharing.simulate'), simForm.value);
        simulationResults.value = response.data.splits;
    } catch {
        alert('Simulation failed. Please check inputs.');
    } finally {
        isSimulating.value = false;
    }
};
</script>

<template>
    <Head title="Revenue & Collection Sharing Engine" />

    <div class="min-h-screen bg-gray-900 text-gray-100 p-6 sm:p-10">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-800 pb-5">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Revenue Sharing Engine</h1>
                    <p class="text-sm text-gray-400 mt-1">Configure multi-tier collection splits, simulate distributions, and audit ledger transactions</p>
                </div>

                <div class="flex items-center gap-2 bg-gray-800 p-1.5 rounded-xl border border-gray-700">
                    <button
                        @click="activeTab = 'rules'"
                        :class="activeTab === 'rules' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-white'"
                        class="px-4 py-2 text-xs font-semibold rounded-lg transition"
                    >
                        Rules Management
                    </button>
                    <button
                        @click="activeTab = 'simulator'"
                        :class="activeTab === 'simulator' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-white'"
                        class="px-4 py-2 text-xs font-semibold rounded-lg transition"
                    >
                        Distribution Simulator
                    </button>
                    <button
                        @click="activeTab = 'ledger'"
                        :class="activeTab === 'ledger' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-white'"
                        class="px-4 py-2 text-xs font-semibold rounded-lg transition"
                    >
                        Settlement Ledger
                    </button>
                </div>
            </div>

            <!-- TAB 1: RULES MANAGEMENT -->
            <div v-if="activeTab === 'rules'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Create Rule Form -->
                <div class="lg:col-span-1 bg-gray-800 p-6 rounded-2xl border border-gray-700 space-y-4">
                    <h2 class="text-lg font-bold text-white mb-2">Add Sharing Rule</h2>
                    <form @submit.prevent="submitRule" class="space-y-4">
                        <div>
                            <InputLabel value="Rule Name" class="text-gray-300 text-xs" />
                            <TextInput v-model="ruleForm.name" type="text" placeholder="e.g. 15% Tithe to Diocese" class="w-full mt-1 bg-gray-900 text-xs" required />
                            <InputError :message="ruleForm.errors.name" />
                        </div>

                        <div>
                            <InputLabel value="Collection Type" class="text-gray-300 text-xs" />
                            <select v-model="ruleForm.source_type" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option value="tithe">Tithe</option>
                                <option value="offering_type">Offering Type</option>
                                <option value="project">Project Contribution</option>
                            </select>
                        </div>

                        <div v-if="ruleForm.source_type === 'offering_type'">
                            <InputLabel value="Specific Offering Type (Optional)" class="text-gray-300 text-xs" />
                            <select v-model="ruleForm.source_id" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option :value="null">All Offering Types</option>
                                <option v-for="type in offeringTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <InputLabel value="Direction" class="text-gray-300 text-xs" />
                                <select v-model="ruleForm.direction" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                    <option value="upward">Upward</option>
                                    <option value="downward">Downward</option>
                                    <option value="direct">Direct</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Calculation" class="text-gray-300 text-xs" />
                                <select v-model="ruleForm.calculation_type" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed_amount">Fixed Amount ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <InputLabel :value="ruleForm.calculation_type === 'percentage' ? 'Rate (%)' : 'Fixed Amount ($)'" class="text-gray-300 text-xs" />
                                <TextInput v-model="ruleForm.value" type="number" step="0.01" class="w-full mt-1 bg-gray-900 text-xs" required />
                            </div>
                            <div>
                                <InputLabel value="Min Threshold ($)" class="text-gray-300 text-xs" />
                                <TextInput v-model="ruleForm.min_collection_threshold" type="number" step="0.01" class="w-full mt-1 bg-gray-900 text-xs" />
                            </div>
                        </div>

                        <PrimaryButton :disabled="ruleForm.processing" class="w-full justify-center bg-blue-600 hover:bg-blue-500 py-2.5 text-xs">
                            Save Revenue Sharing Rule
                        </PrimaryButton>
                    </form>
                </div>

                <!-- Rules List -->
                <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl border border-gray-700">
                    <h2 class="text-lg font-bold text-white mb-4">Active Distribution Rules</h2>

                    <div v-if="rules.length === 0" class="text-center py-10 text-gray-400 text-sm">
                        No revenue sharing rules configured yet. Create one on the left.
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="rule in rules" :key="rule.id" class="bg-gray-900/60 p-4 rounded-xl border border-gray-700/60 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-white text-sm">{{ rule.name }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-900/50 text-blue-300 border border-blue-700/50">
                                        {{ rule.source_type }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    Flow: <strong class="text-gray-200 capitalize">{{ rule.direction }}</strong> | 
                                    Split: <strong class="text-emerald-400">{{ rule.calculation_type === 'percentage' ? `${rule.value}%` : `$${rule.value}` }}</strong>
                                    <span v-if="rule.min_collection_threshold > 0"> (Min Collection: ${{ rule.min_collection_threshold }})</span>
                                </p>
                            </div>
                            <span class="text-xs text-gray-400 font-mono">Priority: #{{ rule.priority }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SIMULATOR -->
            <div v-if="activeTab === 'simulator'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 bg-gray-800 p-6 rounded-2xl border border-gray-700 space-y-4">
                    <h2 class="text-lg font-bold text-white mb-2">Test Revenue Distribution</h2>
                    <div class="space-y-4">
                        <div>
                            <InputLabel value="Collection Type" class="text-gray-300 text-xs" />
                            <select v-model="simForm.source_type" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option value="tithe">Tithe</option>
                                <option value="offering_type">Offering Type</option>
                                <option value="project">Project Contribution</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel value="Collecting Unit" class="text-gray-300 text-xs" />
                            <select v-model="simForm.source_unit_id" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-xs p-2.5">
                                <option v-for="unit in orgUnits" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel value="Gross Collection Amount ($)" class="text-gray-300 text-xs" />
                            <TextInput v-model="simForm.gross_amount" type="number" step="0.01" class="w-full mt-1 bg-gray-900 text-xs" />
                        </div>

                        <PrimaryButton @click="runSimulation" :disabled="isSimulating" class="w-full justify-center bg-emerald-600 hover:bg-emerald-500 py-2.5 text-xs">
                            Run Split Simulation
                        </PrimaryButton>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-gray-800 p-6 rounded-2xl border border-gray-700">
                    <h2 class="text-lg font-bold text-white mb-4">Calculated Distribution Breakdown</h2>

                    <div v-if="simulationResults.length === 0" class="text-center py-12 text-gray-400 text-sm">
                        Configure simulation parameters on the left and click "Run Split Simulation".
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="(split, idx) in simulationResults" :key="idx" class="bg-gray-900 p-4 rounded-xl border border-emerald-500/30 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wider">{{ split.rule_name }}</p>
                                <p class="text-sm font-bold text-white mt-1">
                                    {{ split.source_unit.name }} &rarr; {{ split.destination_unit.name }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ split.summary }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-emerald-400">${{ split.distributed_amount }}</span>
                                <p class="text-[10px] text-gray-400">from ${{ split.gross_amount }} gross</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SETTLEMENT LEDGER -->
            <div v-if="activeTab === 'ledger'" class="bg-gray-800 p-6 rounded-2xl border border-gray-700">
                <h2 class="text-lg font-bold text-white mb-4">Recorded Revenue Split Ledger</h2>

                <div v-if="distributions.length === 0" class="text-center py-12 text-gray-400 text-sm">
                    No transactions have generated revenue distribution records yet.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-300">
                        <thead class="bg-gray-900/80 text-gray-400 uppercase font-semibold">
                            <tr>
                                <th class="p-3">Rule</th>
                                <th class="p-3">Transaction</th>
                                <th class="p-3">Source Unit</th>
                                <th class="p-3">Destination Unit</th>
                                <th class="p-3">Gross Amount</th>
                                <th class="p-3">Split Amount</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            <tr v-for="item in distributions" :key="item.id">
                                <td class="p-3 font-semibold text-white">{{ item.rule?.name || 'Rule #' + item.revenue_sharing_rule_id }}</td>
                                <td class="p-3">{{ item.transaction_type }} #{{ item.transaction_id }}</td>
                                <td class="p-3">{{ item.source_unit?.name }}</td>
                                <td class="p-3">{{ item.destination_unit?.name }}</td>
                                <td class="p-3 font-mono">${{ item.gross_amount }}</td>
                                <td class="p-3 font-mono font-bold text-emerald-400">${{ item.distributed_amount }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-900/50 text-amber-300 border border-amber-700/50">
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
