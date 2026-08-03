<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface HierarchyLevel {
    id: number;
    depth: number;
    name: string;
    plural_name: string;
    allow_institutions: boolean;
}

interface OrganizationalUnit {
    id: number;
    name: string;
    code?: string;
    email?: string;
    phone?: string;
    parent_id?: number;
    hierarchy_level_id: number;
    level?: HierarchyLevel;
    parent?: OrganizationalUnit;
    children?: OrganizationalUnit[];
}

interface Organization {
    id: number;
    name: string;
    description?: string;
}

interface Definition {
    id: number;
    name: string;
}

const props = defineProps<{
    organization: Organization;
    definition: Definition;
    levels: HierarchyLevel[];
    units: OrganizationalUnit[];
}>();

const showLevelModal = ref(false);
const showUnitModal = ref(false);

const levelForm = useForm({
    hierarchy_definition_id: props.definition.id,
    name: '',
    plural_name: '',
    depth: props.levels.length,
    allow_institutions: false,
});

const unitForm = useForm({
    organization_id: props.organization.id,
    hierarchy_level_id: props.levels.length > 0 ? props.levels[0].id : '',
    parent_id: '',
    name: '',
    code: '',
    email: '',
    phone: '',
    address: '',
});

const submitLevel = () => {
    levelForm.post(route('setup-organization-levels-store'), {
        onSuccess: () => {
            showLevelModal.value = false;
            levelForm.reset('name', 'plural_name');
        },
    });
};

const submitUnit = () => {
    unitForm.post(route('setup-organization-units-store'), {
        onSuccess: () => {
            showUnitModal.value = false;
            unitForm.reset('name', 'code', 'email', 'phone', 'address');
        },
    });
};

const deleteUnit = (id: number) => {
    if (confirm('Are you sure you want to remove this organizational unit?')) {
        unitForm.delete(route('setup-organization-units-destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Organization Hierarchy Setup">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Organization Structure & Hierarchy
            </h2>
        </template>

        <div class="py-12 mx-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Header Banner -->
            <div
                class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-gray-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-bold">{{ organization.name }}</h3>
                    <p class="text-indigo-200 text-sm mt-1">{{ definition.name }} • Configured Levels: {{ levels.length
                        }}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showLevelModal = true"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow transition">
                        + Add Structure Level
                    </button>
                    <button @click="showUnitModal = true" :disabled="levels.length === 0"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow transition">
                        + Add Unit / Branch
                    </button>
                </div>
            </div>

            <!-- Hierarchy Levels Overview -->
            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>🏛️</span> Defined Organizational Levels
                </h4>

                <div v-if="levels.length === 0" class="text-center py-8 text-gray-500 text-sm">
                    No hierarchy levels defined yet. Click <strong>"+ Add Structure Level"</strong> to define your
                    structure
                    (e.g., Region → Diocese → District → Parish → Church).
                </div>

                <div v-else class="flex flex-wrap items-center gap-3">
                    <div v-for="(lvl, index) in levels" :key="lvl.id" class="flex items-center gap-3">
                        <div
                            class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-3 text-center min-w-[140px]">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-500">Level {{
                                lvl.depth + 1
                                }}</span>
                            <div class="font-extrabold text-gray-900 text-base mt-0.5">{{ lvl.name }}</div>
                            <span class="text-xs text-gray-500">({{ lvl.plural_name }})</span>
                        </div>
                        <span v-if="index < levels.length - 1" class="text-gray-300 font-bold text-xl">&rarr;</span>
                    </div>
                </div>
            </div>

            <!-- Organizational Units Tree / List -->
            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>📍</span> Organizational Units & Branches
                </h4>

                <div v-if="units.length === 0" class="text-center py-10 text-gray-500 text-sm">
                    No organizational units added yet. Add units under your defined structure levels.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-bold uppercase text-gray-500">
                                <th class="py-3 px-4">Unit Name</th>
                                <th class="py-3 px-4">Level</th>
                                <th class="py-3 px-4">Parent Unit</th>
                                <th class="py-3 px-4">Code</th>
                                <th class="py-3 px-4">Contact</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="unit in units" :key="unit.id" class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4 font-semibold text-gray-900">
                                    {{ unit.name }}
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                        {{ unit.level?.name || 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ unit.parent?.name || '— Top Level —' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-gray-500">
                                    {{ unit.code || '-' }}
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500">
                                    <div>{{ unit.phone || '-' }}</div>
                                    <div class="text-gray-400">{{ unit.email || '' }}</div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="deleteUnit(unit.id)"
                                        class="text-red-600 hover:text-red-800 font-bold text-xs">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Hierarchy Level Modal -->
        <div v-if="showLevelModal"
            class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Add Structure Level</h3>
                <form @submit.prevent="submitLevel" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Singular Name</label>
                        <input v-model="levelForm.name" type="text" placeholder="e.g., Diocese or District"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Plural Name</label>
                        <input v-model="levelForm.plural_name" type="text" placeholder="e.g., Dioceses or Districts"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Rank / Depth Index</label>
                        <input v-model="levelForm.depth" type="number" min="0"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required />
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showLevelModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Cancel</button>
                        <button type="submit" :disabled="levelForm.processing"
                            class="px-5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow">Save
                            Level</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Organizational Unit Modal -->
        <div v-if="showUnitModal"
            class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Add Organizational Unit</h3>
                <form @submit.prevent="submitUnit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Structure Level</label>
                        <select v-model="unitForm.hierarchy_level_id"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required>
                            <option v-for="lvl in levels" :key="lvl.id" :value="lvl.id">
                                {{ lvl.name }} (Level {{ lvl.depth + 1 }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Parent Unit
                            (Optional)</label>
                        <select v-model="unitForm.parent_id"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            <option value="">— None (Top Level) —</option>
                            <option v-for="u in units" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.level?.name || 'Unit' }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Unit Name</label>
                        <input v-model="unitForm.name" type="text" placeholder="e.g., St. Paul Parish or Western Region"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Code / Abbreviation</label>
                        <input v-model="unitForm.code" type="text" placeholder="e.g., REG-01"
                            class="w-full rounded-xl border-gray-300 shadow-sm text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone</label>
                            <input v-model="unitForm.phone" type="text" placeholder="+123..."
                                class="w-full rounded-xl border-gray-300 shadow-sm text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                            <input v-model="unitForm.email" type="email" placeholder="unit@church.org"
                                class="w-full rounded-xl border-gray-300 shadow-sm text-sm" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showUnitModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">Cancel</button>
                        <button type="submit" :disabled="unitForm.processing"
                            class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow">Save
                            Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
