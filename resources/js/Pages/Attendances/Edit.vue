<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import { ref } from "vue";
import Swal from 'sweetalert2'
import { useForm, router } from '@inertiajs/vue3';


const props = defineProps({
    attendance: Array,
    notification: Object
})

const form = useForm({
    id: null,
    member: null,
})

form.patch(route('attendances-mark'), {
    onSuccess: () => {

    }
})

</script>

<template>
    <AppLayout title="Edit Attendance">
        <template #header>
            Edit Attendance
        </template>

        <Container>

        </Container>
    </AppLayout>
</template>
