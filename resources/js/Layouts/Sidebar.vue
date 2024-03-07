<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import ApplicationLogo from '../Components/ApplicationLogo.vue';
import { links, linksBottom } from '../links.js'
import SBLink from '../Components/Custom/SBLink.vue';

defineProps({
    toggleState: {
        type: Boolean,
        default: localStorage.getItem('toggleMenu') == '1'
    }
})

onMounted(() => {
    links.value = links.value.map(item => {
        item.show = usePage()?.props?.auth?.user?.is_admin || item.permission === usePage().props.auth.user.is_admin
        return item
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
                    <SBLink :link="link" :active="route().current(link.name) || route().current().startsWith(link.name)"
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
