<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion?: string;
    phpVersion?: string;
}>();
</script>

<template>
    <Head title="Church Management SaaS - Streamline Your Ministry" />

    <div class="relative min-h-screen bg-gray-950 text-white font-sans overflow-x-hidden">
        <!-- Background Image with Dark Overlay -->
        <div class="absolute inset-0 z-0">
            <img
                src="/images/church_hero.jpg"
                alt="Church background"
                class="w-full h-full object-cover object-center filter brightness-[0.35]"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-gray-950/40"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigation Header -->
            <header class="flex items-center justify-between py-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l-4 4m4-4l4 4" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white">Church SaaS</span>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        :href="route('central.register-tenant.create')"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/30"
                    >
                        Register Church
                    </Link>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="py-16 sm:py-24 text-center max-w-4xl mx-auto">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-900/60 border border-indigo-500/40 text-indigo-300 mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Modern Multi-Tenant Church Management System
                </span>
                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-tight">
                    Empowering Churches with Modern Technology
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                    Streamline membership management, tithes & offerings accounting, service attendance, and congregation SMS communications in one unified platform.
                </p>

                <!-- Primary Self-Onboarding CTA -->
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="route('central.register-tenant.create')"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-xl shadow-indigo-600/40 flex items-center justify-center gap-2"
                    >
                        <span>Start Free Self-Onboarding</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                </div>
            </section>

            <!-- Feature Highlights Section -->
            <section class="py-16 border-t border-gray-800/80">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Everything Your Church Needs to Thrive</h2>
                    <p class="text-sm text-gray-400 mt-2">Comprehensive tools built for pastors, finance officers, and ministry leaders.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-6 backdrop-blur-sm transition hover:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-blue-900/50 border border-blue-500/30 flex items-center justify-center text-blue-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Member & Family Directory</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Maintain comprehensive member profiles, departmental enrollments, photo IDs, and family group connections seamlessly.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-6 backdrop-blur-sm transition hover:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-green-900/50 border border-green-500/30 flex items-center justify-center text-green-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Tithes & Offerings Accounting</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Record tithes, Sunday service offerings, special building funds, and custom project contributions with detailed reports.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-6 backdrop-blur-sm transition hover:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-purple-900/50 border border-purple-500/30 flex items-center justify-center text-purple-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">SMS Broadcast & Messaging</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Send bulk announcements, service reminders, and emergency updates to all church members or specific ministry groups.
                        </p>
                    </div>
                </div>

                <div class="mt-12 bg-indigo-950/40 border border-indigo-500/30 rounded-2xl p-8 text-center max-w-3xl mx-auto">
                    <h3 class="text-xl font-bold text-white">Ready to Setup Your Church Workspace?</h3>
                    <p class="text-sm text-gray-300 mt-2">Create your dedicated database and subdomain in under a minute with no credit card required.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('central.register-tenant.create')"
                            class="inline-flex items-center px-6 py-3 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/30"
                        >
                            Register Your Church Workspace &rarr;
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-8 text-center text-xs text-gray-500 border-t border-gray-800">
                &copy; {{ new Date().getFullYear() }} Church SaaS Platform. All rights reserved.
            </footer>
        </div>
    </div>
</template>
