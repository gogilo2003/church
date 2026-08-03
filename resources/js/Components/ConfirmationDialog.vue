<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = withDefaults(
    defineProps<{
        show: boolean;
        title?: string;
        message?: string;
        confirmText?: string;
        cancelText?: string;
        variant?: 'danger' | 'warning' | 'info';
        loading?: boolean;
    }>(),
    {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed with this action?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        variant: 'danger',
        loading: false,
    }
);

const emit = defineEmits<{
    (e: 'confirm'): void;
    (e: 'close'): void;
}>();
</script>

<template>
    <Modal :show="show" @close="emit('close')" max-width="sm">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Icon header -->
            <div class="flex items-center gap-3 mb-4">
                <div
                    :class="[
                        'w-10 h-10 rounded-full flex items-center justify-center shrink-0',
                        variant === 'danger' ? 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400' :
                        variant === 'warning' ? 'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400' :
                        'bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400'
                    ]"
                >
                    <svg v-if="variant === 'danger'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <svg v-else-if="variant === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold">{{ title }}</h3>
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ message }}
            </p>

            <div class="flex items-center justify-end gap-3">
                <SecondaryButton @click="emit('close')" :disabled="loading">
                    {{ cancelText }}
                </SecondaryButton>

                <DangerButton v-if="variant === 'danger'" @click="emit('confirm')" :disabled="loading">
                    <span v-if="loading" class="animate-spin me-2">⏳</span>
                    {{ confirmText }}
                </DangerButton>
                <PrimaryButton v-else @click="emit('confirm')" :disabled="loading">
                    <span v-if="loading" class="animate-spin me-2">⏳</span>
                    {{ confirmText }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
