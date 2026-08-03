<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

interface FollowUpCard {
    id: number;
    stage: string;
    visit_date: string;
    visit_purpose: string | null;
    prayer_requests: string | null;
    notes: string | null;
    last_contacted_at: string | null;
    member: {
        id: number;
        full_name: string;
        phone: string;
        email: string | null;
        status: string;
    };
    assigned_user?: { id: number; name: string } | null;
}

interface PipelineBoardData {
    new: FollowUpCard[];
    contacted: FollowUpCard[];
    visited: FollowUpCard[];
    in_classes: FollowUpCard[];
    converted: FollowUpCard[];
    dropped: FollowUpCard[];
}

const props = defineProps<{
    board: PipelineBoardData;
    leaders: { id: number; name: string }[];
    orgUnits: { id: number; name: string }[];
}>();

const showVisitorModal = ref(false);

const visitorForm = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    gender: 'male',
    phone: '',
    email: '',
    org_unit_id: '',
    visit_date: new Date().toISOString().split('T')[0],
    visit_purpose: 'Sunday Worship Service',
    prayer_requests: '',
    notes: '',
    assigned_user_id: '',
});

const submitVisitorIntake = () => {
    visitorForm.post(route('visitors.store'), {
        onSuccess: () => {
            showVisitorModal.value = false;
            visitorForm.reset();
        },
    });
};

const stages = [
    { key: 'new', label: 'New Intake', color: 'border-blue-500 bg-blue-50/50' },
    { key: 'contacted', label: 'Contacted', color: 'border-indigo-500 bg-indigo-50/50' },
    { key: 'visited', label: 'Home/Pastoral Visit', color: 'border-amber-500 bg-amber-50/50' },
    { key: 'in_classes', label: 'Membership Classes', color: 'border-purple-500 bg-purple-50/50' },
    { key: 'converted', label: 'Converted Members', color: 'border-emerald-500 bg-emerald-50/50' },
    { key: 'dropped', label: 'Dropped', color: 'border-gray-400 bg-gray-50/50' },
];

const moveStage = (cardId: number, nextStage: string) => {
    router.patch(route('visitors.stage', cardId), { stage: nextStage });
};

const convertToMember = (cardId: number) => {
    router.post(route('visitors.convert', cardId));
};

const assignLeader = (cardId: number, userId: string) => {
    router.patch(route('visitors.leader', cardId), { assigned_user_id: userId || null });
};
</script>

<template>
    <Head title="Visitor Follow-up Pipeline" />

    <AppLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
            <PageHeader
                title="Visitor Follow-up & Assimilation Pipeline"
                description="Track first-time visitors, assign pastoral follow-up leaders, log care interactions, and convert visitors to active members."
            >
                <template #actions>
                    <PrimaryButton @click="showVisitorModal = true">
                        + Register First-Time Visitor
                    </PrimaryButton>
                </template>
            </PageHeader>

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 overflow-x-auto pb-6">
                <div
                    v-for="stg in stages"
                    :key="stg.key"
                    class="bg-white dark:bg-gray-900 border-t-4 border-b border-l border-r border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-3 flex flex-col min-h-[450px]"
                    :class="stg.color"
                >
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-200 dark:border-gray-800">
                        <span class="font-bold text-xs text-gray-900 dark:text-white uppercase tracking-wider">
                            {{ stg.label }}
                        </span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                            {{ (board as any)[stg.key]?.length || 0 }}
                        </span>
                    </div>

                    <div class="flex-1 space-y-3 overflow-y-auto">
                        <div
                            v-for="card in (board as any)[stg.key]"
                            :key="card.id"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 shadow-xs space-y-2 hover:shadow-sm transition-all"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-bold text-xs text-gray-900 dark:text-white">
                                        {{ card.member.full_name }}
                                    </h4>
                                    <div class="text-[11px] text-gray-500">
                                        📞 {{ card.member.phone }}
                                    </div>
                                </div>
                            </div>

                            <p v-if="card.prayer_requests" class="text-[11px] text-gray-600 dark:text-gray-300 italic line-clamp-2">
                                "{{ card.prayer_requests }}"
                            </p>

                            <!-- Leader Assignment -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                <label class="text-[10px] text-gray-400 font-bold uppercase block mb-0.5">Assigned Leader</label>
                                <select
                                    :value="card.assigned_user?.id || ''"
                                    @change="(e: any) => assignLeader(card.id, e.target.value)"
                                    class="w-full text-[11px] py-0.5 px-1.5 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-200"
                                >
                                    <option value="">Unassigned</option>
                                    <option v-for="ldr in leaders" :key="ldr.id" :value="ldr.id">
                                        {{ ldr.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Stage Action Controls -->
                            <div class="pt-2 flex items-center justify-between text-[10px] font-bold">
                                <select
                                    :value="card.stage"
                                    @change="(e: any) => moveStage(card.id, e.target.value)"
                                    class="text-[10px] py-0.5 px-1 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-200"
                                >
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="visited">Visited</option>
                                    <option value="in_classes">In Classes</option>
                                    <option value="converted">Converted</option>
                                    <option value="dropped">Dropped</option>
                                </select>

                                <button
                                    v-if="card.stage !== 'converted'"
                                    @click="convertToMember(card.id)"
                                    class="text-emerald-600 hover:text-emerald-800"
                                    title="Convert to Member"
                                >
                                    Convert ✓
                                </button>
                            </div>
                        </div>

                        <div v-if="!(board as any)[stg.key]?.length" class="h-32 flex items-center justify-center text-[11px] text-gray-400 italic text-center">
                            Empty
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Intake Modal -->
        <Modal :show="showVisitorModal" max-width="md" @close="showVisitorModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Register First-Time Visitor
                </h3>

                <form @submit.prevent="submitVisitorIntake" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="First Name *" />
                            <TextInput v-model="visitorForm.first_name" type="text" class="w-full mt-1" required />
                        </div>
                        <div>
                            <InputLabel value="Last Name *" />
                            <TextInput v-model="visitorForm.last_name" type="text" class="w-full mt-1" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Phone Number *" />
                            <TextInput v-model="visitorForm.phone" type="text" placeholder="+254 700 000 000" class="w-full mt-1" required />
                        </div>
                        <div>
                            <InputLabel value="Gender *" />
                            <select v-model="visitorForm.gender" class="w-full mt-1 rounded-md border-gray-300 shadow-sm text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Email Address" />
                        <TextInput v-model="visitorForm.email" type="email" class="w-full mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Visit Purpose / Service" />
                        <TextInput v-model="visitorForm.visit_purpose" type="text" class="w-full mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Prayer Requests" />
                        <textarea v-model="visitorForm.prayer_requests" rows="2" class="w-full mt-1 rounded-md border-gray-300 shadow-sm text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"></textarea>
                    </div>

                    <div>
                        <InputLabel value="Assign Follow-up Leader" />
                        <select v-model="visitorForm.assigned_user_id" class="w-full mt-1 rounded-md border-gray-300 shadow-sm text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                            <option value="">Select Follow-up Leader</option>
                            <option v-for="ldr in leaders" :key="ldr.id" :value="ldr.id">
                                {{ ldr.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <SecondaryButton @click="showVisitorModal = false">Cancel</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="visitorForm.processing">Register Visitor</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
