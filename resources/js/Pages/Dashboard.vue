<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Welcome from '../Components/Welcome.vue';
import Container from '../Components/Custom/Container.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            Dashboards
        </template>

        <Container>
            <Welcome />
        </Container>
    </AppLayout>
</template>
