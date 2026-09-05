<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import SBLink from '../Components/Custom/SBLink.vue';
import { useLinks } from '@/Composables/useLinks';

withDefaults(defineProps<{
    toggleState?: boolean;
}>(), {
    toggleState: () => localStorage.getItem('toggleMenu') == '1'
});

const { topLinks, bottomLinks, isCentralContext } = useLinks();
</script>

<template>
    <div class="min-h-screen w-full shadow py-2 text-gray-200 flex flex-col bg-gray-800">
        <!-- Logo Area -->
        <div class="shrink-0 flex items-center my-6 px-3 flex-none justify-between">
            <Link :href="isCentralContext ? route('central.admin.dashboard') : route('dashboard')">
                <ApplicationLogo class="block h-9 w-76 text-gray-200" :toggle="toggleState" />
            </Link>
        </div>

        <!-- Sidebar Navigation List -->
        <div class="flex-1 flex justify-between flex-col">
            <ul class="relative">
                <li class="relative block w-76" v-for="link in topLinks" :key="link.name">
                    <SBLink
                        :link="link"
                        :toggle="toggleState"
                    />
                </li>
            </ul>
            <ul class="relative">
                <li class="relative block w-76" v-for="link in bottomLinks" :key="link.name">
                    <SBLink
                        :method="link?.method"
                        :as="link?.as ?? 'a'"
                        :link="link"
                        :toggle="toggleState"
                    />
                </li>
            </ul>
        </div>
    </div>
</template>
