<script setup lang="ts" generic="T extends Record<string, any>">
export interface TableColumn {
    key: string;
    label: string;
    sortable?: boolean;
    align?: 'left' | 'center' | 'right';
    width?: string;
}

const props = withDefaults(
    defineProps<{
        columns: TableColumn[];
        data: T[];
        sortBy?: string;
        sortOrder?: 'asc' | 'desc';
        loading?: boolean;
        emptyTitle?: string;
        emptyDescription?: string;
    }>(),
    {
        sortBy: '',
        sortOrder: 'desc',
        loading: false,
        emptyTitle: 'No records found',
        emptyDescription: 'Try adjusting your search or filters.',
    }
);

const emit = defineEmits<{
    (e: 'sort', columnKey: string): void;
}>();

const handleHeaderClick = (column: TableColumn) => {
    if (column.sortable) {
        emit('sort', column.key);
    }
};
</script>

<template>
    <div class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors">
        <div class="relative overflow-x-auto">
            <!-- Loading Bar -->
            <div v-if="loading" class="absolute top-0 left-0 right-0 h-1 bg-gray-200 overflow-hidden">
                <div class="h-full bg-gray-800 animate-pulse w-full"></div>
            </div>

            <table class="w-full text-left text-sm">
                <!-- Header -->
                <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            @click="handleHeaderClick(col)"
                            :class="[
                                'px-4 py-3 transition-colors',
                                col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                                col.sortable ? 'cursor-pointer hover:text-gray-900 dark:hover:text-white select-none' : '',
                                col.width || ''
                            ]"
                        >
                            <div class="inline-flex items-center gap-1" :class="{ 'justify-center': col.align === 'center', 'justify-end': col.align === 'right' }">
                                <span>{{ col.label }}</span>
                                <template v-if="col.sortable">
                                    <span v-if="sortBy === col.key" class="font-bold text-gray-900 dark:text-white">
                                        {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">↕</span>
                                </template>
                            </div>
                        </th>
                        <th v-if="$slots.actions" class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <!-- Body -->
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    <template v-if="data.length > 0">
                        <tr
                            v-for="(row, index) in data"
                            :key="row.id ?? index"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors"
                        >
                            <td
                                v-for="col in columns"
                                :key="col.key"
                                :class="[
                                    'px-4 py-3 whitespace-nowrap text-sm',
                                    col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'
                                ]"
                            >
                                <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                    {{ row[col.key] ?? '-' }}
                                </slot>
                            </td>
                            <td v-if="$slots.actions" class="px-4 py-3 text-right whitespace-nowrap">
                                <slot name="actions" :row="row" />
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-10 text-center">
                                <div class="max-w-xs mx-auto text-center space-y-1">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ emptyTitle }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ emptyDescription }}</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>
