<script setup lang="ts">
import { ref, computed } from 'vue';

export interface DualListItem {
    value: string | number;
    label: string;
    disabled?: boolean;
    [key: string]: any;
}

const props = withDefaults(
    defineProps<{
        items: DualListItem[];
        modelValue?: (string | number)[];
        availableTitle?: string;
        selectedTitle?: string;
        disabled?: boolean;
        searchable?: boolean;
        placeholder?: string;
    }>(),
    {
        modelValue: () => [],
        availableTitle: 'Available Items',
        selectedTitle: 'Selected Items',
        disabled: false,
        searchable: true,
        placeholder: 'Search items...',
    }
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: (string | number)[]): void;
    (e: 'change', value: (string | number)[]): void;
}>();

const availableSearch = ref('');
const selectedSearch = ref('');

const highlightedAvailable = ref<(string | number)[]>([]);
const highlightedSelected = ref<(string | number)[]>([]);

const lastAvailableIndex = ref<number | null>(null);
const lastSelectedIndex = ref<number | null>(null);

const selectedValueSet = computed(() => new Set(props.modelValue));

const availableItems = computed(() => {
    return props.items.filter((item) => !selectedValueSet.value.has(item.value));
});

const selectedItems = computed(() => {
    const itemMap = new Map(props.items.map((i) => [i.value, i]));
    return props.modelValue
        .map((val) => itemMap.get(val))
        .filter((item): item is DualListItem => item !== undefined);
});

const filteredAvailableItems = computed(() => {
    if (!availableSearch.value.trim()) return availableItems.value;
    const query = availableSearch.value.toLowerCase();
    return availableItems.value.filter((item) =>
        item.label.toLowerCase().includes(query)
    );
});

const filteredSelectedItems = computed(() => {
    if (!selectedSearch.value.trim()) return selectedItems.value;
    const query = selectedSearch.value.toLowerCase();
    return selectedItems.value.filter((item) =>
        item.label.toLowerCase().includes(query)
    );
});

const updateSelection = (newSelectedValues: (string | number)[]) => {
    emit('update:modelValue', newSelectedValues);
    emit('change', newSelectedValues);
    highlightedAvailable.value = [];
    highlightedSelected.value = [];
};

const handleItemClick = (
    item: DualListItem,
    index: number,
    type: 'available' | 'selected',
    event: MouseEvent
) => {
    if (props.disabled || item.disabled) return;

    const isAvailable = type === 'available';
    const currentList = isAvailable ? filteredAvailableItems.value : filteredSelectedItems.value;
    const highlightedRef = isAvailable ? highlightedAvailable : highlightedSelected;
    const lastIndexRef = isAvailable ? lastAvailableIndex : lastSelectedIndex;

    let updatedHighlight = [...highlightedRef.value];

    if (event.shiftKey && lastIndexRef.value !== null) {
        const start = Math.min(lastIndexRef.value, index);
        const end = Math.max(lastIndexRef.value, index);
        const rangeValues = currentList
            .slice(start, end + 1)
            .filter((i) => !i.disabled)
            .map((i) => i.value);

        if (event.ctrlKey || event.metaKey) {
            updatedHighlight = Array.from(new Set([...updatedHighlight, ...rangeValues]));
        } else {
            updatedHighlight = rangeValues;
        }
    } else if (event.ctrlKey || event.metaKey) {
        if (updatedHighlight.includes(item.value)) {
            updatedHighlight = updatedHighlight.filter((v) => v !== item.value);
        } else {
            updatedHighlight.push(item.value);
        }
        lastIndexRef.value = index;
    } else {
        updatedHighlight = [item.value];
        lastIndexRef.value = index;
    }

    highlightedRef.value = updatedHighlight;
};

const handleItemDoubleClick = (item: DualListItem, type: 'available' | 'selected') => {
    if (props.disabled || item.disabled) return;

    if (type === 'available') {
        const nextValues = [...props.modelValue, item.value];
        updateSelection(nextValues);
    } else {
        const nextValues = props.modelValue.filter((v) => v !== item.value);
        updateSelection(nextValues);
    }
};

const moveSelectedRight = () => {
    if (props.disabled || highlightedAvailable.value.length === 0) return;
    const toAdd = new Set(highlightedAvailable.value);
    const nextValues = [...props.modelValue, ...toAdd];
    updateSelection(nextValues);
};

const moveAllRight = () => {
    if (props.disabled) return;
    const eligibleToAdd = filteredAvailableItems.value
        .filter((item) => !item.disabled)
        .map((item) => item.value);
    const nextValues = Array.from(new Set([...props.modelValue, ...eligibleToAdd]));
    updateSelection(nextValues);
};

const moveSelectedLeft = () => {
    if (props.disabled || highlightedSelected.value.length === 0) return;
    const toRemove = new Set(highlightedSelected.value);
    const nextValues = props.modelValue.filter((v) => !toRemove.has(v));
    updateSelection(nextValues);
};

const moveAllLeft = () => {
    if (props.disabled) return;
    const eligibleToRemove = new Set(
        filteredSelectedItems.value.filter((item) => !item.disabled).map((item) => item.value)
    );
    const nextValues = props.modelValue.filter((v) => !eligibleToRemove.has(v));
    updateSelection(nextValues);
};
</script>

<template>
    <div class="w-full flex flex-col md:flex-row items-stretch justify-center gap-4 text-sm font-sans">
        <!-- Available Items -->
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md shadow-sm overflow-hidden transition-colors">
            <div class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <span class="font-semibold text-gray-800 dark:text-gray-200 text-xs uppercase tracking-wider">
                    {{ availableTitle }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    {{ filteredAvailableItems.length }}
                </span>
            </div>

            <div v-if="searchable" class="p-2 border-b border-gray-200 dark:border-gray-800">
                <div class="relative">
                    <input
                        v-model="availableSearch"
                        type="text"
                        :placeholder="placeholder"
                        :disabled="disabled"
                        class="w-full pl-8 pr-3 py-1 text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 transition"
                    />
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex-1 min-h-[200px] max-h-[300px] overflow-y-auto p-1 space-y-0.5 select-none" role="listbox">
                <div v-if="filteredAvailableItems.length === 0" class="h-full min-h-[160px] flex items-center justify-center text-xs text-gray-400 italic text-center px-4">
                    No items available
                </div>

                <div
                    v-for="(item, index) in filteredAvailableItems"
                    :key="item.value"
                    @click="handleItemClick(item, index, 'available', $event)"
                    @dblclick="handleItemDoubleClick(item, 'available')"
                    :class="[
                        'px-3 py-1.5 rounded-md cursor-pointer text-xs font-medium transition flex items-center justify-between',
                        highlightedAvailable.includes(item.value)
                            ? 'bg-gray-800 text-white font-semibold'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200',
                        (disabled || item.disabled) ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                    ]"
                >
                    <span>{{ item.label }}</span>
                    <span v-if="item.disabled" class="text-[10px] uppercase font-bold text-gray-400">Disabled</span>
                </div>
            </div>
        </div>

        <!-- Controls Column -->
        <div class="flex md:flex-col items-center justify-center gap-1.5 self-center py-2">
            <button
                type="button"
                @click="moveSelectedRight"
                :disabled="disabled || highlightedAvailable.length === 0"
                title="Move Selected Right"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
            >
                &gt;
            </button>
            <button
                type="button"
                @click="moveAllRight"
                :disabled="disabled || filteredAvailableItems.filter(i => !i.disabled).length === 0"
                title="Move All Right"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
            >
                &gt;&gt;
            </button>
            <button
                type="button"
                @click="moveSelectedLeft"
                :disabled="disabled || highlightedSelected.length === 0"
                title="Move Selected Left"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
            >
                &lt;
            </button>
            <button
                type="button"
                @click="moveAllLeft"
                :disabled="disabled || filteredSelectedItems.filter(i => !i.disabled).length === 0"
                title="Move All Left"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
            >
                &lt;&lt;
            </button>
        </div>

        <!-- Selected Items -->
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md shadow-sm overflow-hidden transition-colors">
            <div class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <span class="font-semibold text-gray-800 dark:text-gray-200 text-xs uppercase tracking-wider">
                    {{ selectedTitle }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    {{ filteredSelectedItems.length }}
                </span>
            </div>

            <div v-if="searchable" class="p-2 border-b border-gray-200 dark:border-gray-800">
                <div class="relative">
                    <input
                        v-model="selectedSearch"
                        type="text"
                        :placeholder="placeholder"
                        :disabled="disabled"
                        class="w-full pl-8 pr-3 py-1 text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 transition"
                    />
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex-1 min-h-[200px] max-h-[300px] overflow-y-auto p-1 space-y-0.5 select-none" role="listbox">
                <div v-if="filteredSelectedItems.length === 0" class="h-full min-h-[160px] flex items-center justify-center text-xs text-gray-400 italic text-center px-4">
                    No items selected
                </div>

                <div
                    v-for="(item, index) in filteredSelectedItems"
                    :key="item.value"
                    @click="handleItemClick(item, index, 'selected', $event)"
                    @dblclick="handleItemDoubleClick(item, 'selected')"
                    :class="[
                        'px-3 py-1.5 rounded-md cursor-pointer text-xs font-medium transition flex items-center justify-between',
                        highlightedSelected.includes(item.value)
                            ? 'bg-gray-800 text-white font-semibold'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200',
                        (disabled || item.disabled) ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                    ]"
                >
                    <span>{{ item.label }}</span>
                    <span v-if="item.disabled" class="text-[10px] uppercase font-bold text-gray-400">Disabled</span>
                </div>
            </div>
        </div>
    </div>
</template>
