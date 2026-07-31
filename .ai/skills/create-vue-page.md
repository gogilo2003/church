# Skill Playbook: Create Vue Page

## Vue Page Standard Template
```vue
<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Member, PaginatedData } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

defineProps<{
    members: PaginatedData<Member>;
}>();
</script>

<template>
    <Head title="Members Directory" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Members Directory</h2>
                <Link :href="route('members.create')">
                    <Button label="Add Member" icon="pi pi-plus" class="p-button-primary" />
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <DataTable :value="members.data" responsiveLayout="scroll">
                        <Column field="id" header="ID"></Column>
                        <Column field="first_name" header="First Name"></Column>
                        <Column field="last_name" header="Last Name"></Column>
                        <Column field="phone" header="Phone"></Column>
                        <Column field="gender" header="Gender"></Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```
