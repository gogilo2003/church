# Vue 3, Inertia.js & TypeScript Guidelines

## Vue 3 Composition API Standard
All Vue component files MUST follow the single-file component (SFC) standard using `<script setup lang="ts">`.

## State & Form Handling with Inertia `useForm`
Always use `@inertiajs/vue3`'s `useForm` helper for data mutations:

```vue
<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { PageProps } from '@/types';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

const page = usePage<PageProps>();

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
});

const submit = () => {
    form.post(route('members.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <label for="first_name" class="block text-sm font-medium">First Name</label>
            <InputText id="first_name" v-model="form.first_name" class="w-full" :class="{ 'p-invalid': form.errors.first_name }" />
            <small v-if="form.errors.first_name" class="p-error">{{ form.errors.first_name }}</small>
        </div>
        <Button type="submit" label="Save Member" :loading="form.processing" />
    </form>
</template>
```

## PrimeVue Integration Guidelines
- Import UI controls directly from PrimeVue (`primevue/button`, `primevue/datatable`, `primevue/dialog`, `primevue/toast`).
- Style container layouts using **Tailwind CSS**.
- Use PrimeVue's built-in props for input state (`:invalid="!!form.errors.field"`, `:loading="form.processing"`).

## Type Definitions Standard
- Centralize domain interfaces in `resources/js/types/index.d.ts`.
- Prefix pagination wrappers with `PaginatedData<T>`.

```ts
export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}
```
