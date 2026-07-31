<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import { links, linksBottom } from '../links';
import SBLink from '../Components/Custom/SBLink.vue';
import { PageProps } from '@/types';

withDefaults(defineProps<{
    toggleState?: boolean;
}>(), {
    toggleState: () => localStorage.getItem('toggleMenu') == '1'
});

const page = usePage<PageProps>();

onMounted(() => {
    links.value = links.value.map(item => {
        const isAdmin = !!page.props.auth.user.is_admin;
        item.show = isAdmin || item.permission === 0;
        return item;
    }).filter(link => link.show)
})
</script>

<template>
    <div class="min-h-screen w-full shadow py-2 text-gray-200 flex flex-col bg-gray-800">

        <div class="shrink-0 flex items-center my-8 px-3 flex-none">
            <Link :href="route('dashboard')">
            <ApplicationLogo class="block h-9 w-76 text-gray-200" :toggle="toggleState" />
            </Link>
        </div>
        <div class="flex-1 flex justify-between flex-col">
            <ul class="relative">
                <li class="relative block w-76" v-for="link in links">
                    <SBLink :link="link" :active="route().current(link.name) || (route().current()?.startsWith(link.name) ?? false)"
                        :toggle="toggleState" />
                </li>
            </ul>
            <ul class="relative">
                <li class="relative block w-76" v-for="link in linksBottom">
                    <SBLink :method="link?.method" :as="link?.as ?? 'a'" :link="link"
                        :active="route().current(link.name)" :toggle="toggleState" />
                </li>
            </ul>

        </div>
    </div>
</template>
