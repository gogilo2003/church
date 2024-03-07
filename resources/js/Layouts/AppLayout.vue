<script lang="ts" setup>
import { ref, watch, onBeforeMount } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '../Components/Banner.vue';
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';

defineProps({
    title: String,
});

const toggleState = ref(localStorage.getItem('toggleMenu') == '1' ? true : false)

const toggle = (val) => {
    toggleState.value = val

}

watch(() => toggleState.value, value => {
    localStorage.setItem('toggleMenu', (value ? 1 : 0).toString())
})

</script>

<template>
    <div class="relative">

        <Head :title="title" />

        <Banner />

        <div class="relative min-h-screen bg-gray-100">
            <div class="fixed top-0 left-0 pl-0 md:pl-4 overflow-hidden bg-gray-900 block transition-all duration-500"
                :class="{ 'w-80': toggleState, 'w-0 md:w-20': !toggleState }">
                <Sidebar :toggle-state="toggleState" />
            </div>
            <div :class="{ 'w-[calc(100%_-_20rem)] left-80': toggleState, 'left-0 md:left-20': !toggleState }"
                class="absolute min-h-screen top-0 right-0 transition-all duration-500">
                <Navbar @toggle="toggle" :page="$page.props">
                    <template #header>
                        <slot name="header" />
                    </template>
                </Navbar>
                <!-- Page Content -->
                <main class="px-4 md:px-0">
                    <slot />
                </main>
            </div>



        </div>
    </div>
</template>
