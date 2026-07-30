<script setup lang="ts">
interface Column {
    field: string;
    caption?: string;
}

defineProps<{
    items: Record<string, any>[];
    columns: Column[];
}>()
</script>
<template>
    <table class="w-full">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-50 py-3 px-5" v-for="column in columns"
                    v-text="column.caption ?? column.field"></th>
            </tr>
        </thead>
        <tbody>
            <tr class="odd:bg-gray-50 hover:bg-blue-50 transition duration-200" v-for="item in items">
                <td class="border border-gray-200 py-3 px-5" v-for="(column, index) in columns"
                    v-text="`${item[column.field]}`">
                </td>
            </tr>
        </tbody>
    </table>
</template>
