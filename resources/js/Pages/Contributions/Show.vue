<script setup lang="ts">
import AppLayout from '../../Layouts/AppLayout.vue';
import Container from '../../Components/Custom/Container.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import ContributionType from '../../Components/Church/ContributionType.vue';
import Member from '../../Components/Church/Member.vue';

const props = defineProps({
    contribution_type: Object,
    notification: Object,
    errors: Object,
    members: Object,
})


const amount = ref()


</script>
<template>
    <AppLayout :title="contribution_type?.description">
        <template #header>
            <div>
                <div v-text="contribution_type?.description"></div>
                <div class="text-xs text-gray-600"></div>
            </div>
        </template>
        <Container>
            <div class="p-6">
                <ContributionType :item="contribution_type" />
            </div>
            <div class="py-4 px-6 flex flex-col gap-3">
                <Member v-for="member in members?.data" :member="member" :contribution_type="contribution_type" />
            </div>
        </Container>
    </AppLayout>
</template>
