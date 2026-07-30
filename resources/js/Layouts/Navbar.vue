<script lang="ts" setup>
import { ref } from 'vue';
import Dropdown from '../Components/Dropdown.vue';
import DropdownLink from '../Components/DropdownLink.vue';
import ResponsiveNavLink from '../Components/ResponsiveNavLink.vue';
import { router, usePage } from '@inertiajs/vue3';
import Icon from '../Components/Icons/Icon.vue';
import { PageProps } from '@/types';

const props = defineProps<{
    toggleState: boolean;
}>();

const emit = defineEmits<{
    (e: 'toggle', value: boolean): void;
}>();

const page = usePage<PageProps>();

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const toggle = () => {
    emit('toggle', !props.toggleState);
};
</script>

<template>
    <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex flex-1">
                    <!-- Menu start-->
                    <Icon @click="toggle" class="w-auto h-full py-4 text-gray-400 flex-none cursor-pointer"
                        :type="toggleState ? 'times' : 'menu'" />
                    <!-- Menu end-->
                    <div class="md:flex flex-1 items-center text-gray-700 font-semibold text-xl pl-4">
                        <slot name="header" />
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-auto">
                    <!-- Settings Dropdown -->
                    <div class="ml-auto relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        :src="page.props.auth.user.profile_photo_url" :alt="page.props.auth.user.name">
                                </button>
                            </template>

                            <template #content>
                                <!-- Account Management -->
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>

                                <div class="border-t border-gray-200" />

                                <!-- Authentication -->
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        @click="showingNavigationDropdown = !showingNavigationDropdown">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path
                                :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Dashboard
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" :src="page.props.auth.user.profile_photo_url"
                            :alt="page.props.auth.user.name">
                    </div>

                    <div>
                        <div class="font-medium text-base text-gray-800">
                            {{ page.props.auth.user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ page.props.auth.user.email }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                        Profile
                    </ResponsiveNavLink>

                    <!-- Authentication -->
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </nav>
</template>
