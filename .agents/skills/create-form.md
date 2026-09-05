# Skill Playbook: Create Form

## Vue Inertia Form Playbook
```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Button from 'primevue/button';

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    gender: 'male',
});

const genderOptions = [
    { label: 'Male', value: 'male' },
    { label: 'Female', value: 'female' },
];

const submit = () => {
    form.post(route('members.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">First Name</label>
            <InputText v-model="form.first_name" class="w-full" :class="{ 'p-invalid': form.errors.first_name }" />
            <small v-if="form.errors.first_name" class="p-error">{{ form.errors.first_name }}</small>
        </div>

        <div>
            <label class="block text-sm font-medium">Gender</label>
            <Select v-model="form.gender" :options="genderOptions" optionLabel="label" optionValue="value" class="w-full" />
        </div>

        <Button type="submit" label="Submit" :loading="form.processing" />
    </form>
</template>
```
