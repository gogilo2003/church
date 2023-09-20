<script lang="ts" setup>
import Container from '../../Components/Custom/Container.vue';
import Icon from '../../Components/Icons/Icon.vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';

defineProps({ users: Array })

</script>
<template>
    <AppLayout title="Users">
        <template #header>
            <div>Users</div>
        </template>
        <Container>
            <div class="p-6">
                <div class="flex flex-col gap-3">
                    <div v-for="user in users"
                        class="shadow rounded-lg border p-3 flex flex-col md:flex-row gap-3 justify-start md:justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="h-16 w-16 rounded-full overflow-hidden flex-none">
                                <img class="h-full w-full object-cover" :src="user?.profile_photo_url" alt="">
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <div v-text="user?.name" class="text-gray-700 font-semibold uppercase"></div>
                                <div v-text="`${user?.email} | Role: ${user?.is_admin == 1 ? 'Admin' : 'User'}`"
                                    class="text-gray-500 text-sm"></div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <SecondaryButton>
                                <div class="flex gap-2">
                                    <Icon type="edit" class="h-4 w-4" /><span class="hidden md:inline-block">Edit</span>
                                </div>
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
