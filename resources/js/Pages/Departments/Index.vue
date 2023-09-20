<script lang="ts" setup>
import Container from '../../Components/Custom/Container.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({ departments: Array })
</script>
<template>
    <AppLayout title="Departments">
        <template #header>
            <div>Departments</div>
        </template>
        <Container>
            <div class="p-8">
                <pre v-text="departments"></pre>
            </div>
        </Container>
    </AppLayout>
</template>
