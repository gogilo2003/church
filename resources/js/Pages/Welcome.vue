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

    <Head title="Church Management System - Focus Less on Admin, More on Ministry" />

    <div
        class="relative min-h-screen bg-gray-950 text-white font-sans overflow-x-hidden bg-[url('/images/church_hero.jpg')] bg-cover bg-bottom bg-fixed">
        <!-- Background Ambient Glow Overlay -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/90 via-gray-950/75 to-gray-950"></div>
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-indigo-600/20 blur-[120px] pointer-events-none rounded-full"></div>
            <div class="absolute top-1/3 left-1/3 w-[300px] h-[250px] bg-amber-500/10 blur-[100px] pointer-events-none rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigation Header -->
            <header class="flex items-center justify-between py-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l-4 4m4-4l4 4" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white">Church SaaS</span>
                </div>

                <div class="flex items-center gap-4">
                    <Link :href="route('central.register-tenant.create')"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-0.5">
                        Register Church
                    </Link>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="py-16 sm:py-24 text-center max-w-4xl mx-auto">
                <span
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-indigo-950/80 border border-indigo-500/30 text-indigo-200 mb-8 backdrop-blur-md shadow-inner">
                    <span class="text-amber-400">🕊️</span>
                    Built for Pastors, Finance Officers & Ministry Leaders
                </span>
                
                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.15]">
                    Focus Less on Admin, <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-amber-200 via-indigo-200 to-indigo-400 bg-clip-text text-transparent">
                        More on Shepherding Your People
                    </span>
                </h1>
                
                <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-normal">
                    Replace chaotic spreadsheets and fragmented paper files. Effortlessly manage your member directory, track tithes & offerings, and send instant SMS updates—all in one warm, intuitive workspace.
                </p>

                <!-- Primary Self-Onboarding CTA -->
                <div class="mt-10 flex flex-col items-center justify-center gap-3">
                    <Link :href="route('central.register-tenant.create')"
                        class="w-full sm:w-auto px-9 py-4 rounded-2xl text-base font-bold bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white transition-all shadow-xl shadow-indigo-600/40 hover:shadow-indigo-600/60 hover:-translate-y-0.5 flex items-center justify-center gap-3">
                        <span>Start Your Free Church Workspace</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                    <p class="text-xs text-gray-400 flex items-center gap-1.5 mt-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Set up your dedicated subdomain in under 60 seconds • No credit card required
                    </p>
                </div>
            </section>

            <!-- Problem-to-Solution Feature Section -->
            <section class="py-16 border-t border-white/10">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">Designed to Solve Daily Ministry Challenges</h2>
                    <p class="text-base text-gray-400 mt-3">Simple, intentional tools that help your team stay organized so your congregation can thrive.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="bg-gray-900/60 border border-white/10 rounded-2xl p-7 backdrop-blur-md transition-all hover:border-indigo-500/40 hover:bg-gray-900/80 hover:-translate-y-1 shadow-lg">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 mb-5 text-xl font-bold">
                            📋
                        </div>
                        <h3 class="text-xl font-bold text-white">Centralized Member Directory</h3>
                        <p class="text-sm text-gray-300 mt-2.5 leading-relaxed">
                            No more missing records. Keep full member profiles, family groups, baptism dates, and ministry departments structured and instantly searchable.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="bg-gray-900/60 border border-white/10 rounded-2xl p-7 backdrop-blur-md transition-all hover:border-indigo-500/40 hover:bg-gray-900/80 hover:-translate-y-1 shadow-lg">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-5 text-xl font-bold">
                            💰
                        </div>
                        <h3 class="text-xl font-bold text-white">Tithes & Giving Clarity</h3>
                        <p class="text-sm text-gray-300 mt-2.5 leading-relaxed">
                            Record tithes, Sunday offerings, and building fund contributions accurately. Generate transparent financial reports for leadership in clicks.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="bg-gray-900/60 border border-white/10 rounded-2xl p-7 backdrop-blur-md transition-all hover:border-indigo-500/40 hover:bg-gray-900/80 hover:-translate-y-1 shadow-lg">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 mb-5 text-xl font-bold">
                            💬
                        </div>
                        <h3 class="text-xl font-bold text-white">Instant Congregation Reach</h3>
                        <p class="text-sm text-gray-300 mt-2.5 leading-relaxed">
                            Send targeted SMS broadcasts for Sunday reminders, prayer requests, or emergency updates directly to specific departments or the whole church.
                        </p>
                    </div>
                </div>

                <!-- Callout Banner -->
                <div
                    class="mt-14 bg-gradient-to-r from-indigo-950/80 via-gray-900 to-indigo-950/80 border border-indigo-500/30 rounded-3xl p-8 sm:p-10 text-center max-w-3xl mx-auto shadow-2xl backdrop-blur-md">
                    <h3 class="text-2xl font-extrabold text-white">Ready to simplify your church administration?</h3>
                    <p class="text-sm sm:text-base text-gray-300 mt-3 max-w-xl mx-auto">Create your church's dedicated database and workspace right now. It takes less than a minute.</p>
                    <div class="mt-7">
                        <Link :href="route('central.register-tenant.create')"
                            class="inline-flex items-center px-7 py-3.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-lg shadow-indigo-600/30 hover:-translate-y-0.5">
                            Set Up Your Church Workspace &rarr;
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-8 text-center text-xs text-gray-400 border-t border-white/10">
                &copy; {{ new Date().getFullYear() }} Church SaaS Platform. Built for thriving ministries.
            </footer>
        </div>
    </div>
</template>
