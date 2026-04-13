<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center bg-primary-50 dark:bg-slate-950 pt-6 sm:justify-center sm:pt-0 font-sans"
    >

        <!-- Background Decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-primary-400/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-80 h-80 bg-secondary-400/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10">
            <Link href="/">
                <ApplicationLogo class="scale-125 md:scale-150 mb-4" />
            </Link>
        </div>

        <div
            class="relative z-10 mt-6 w-full overflow-hidden bg-white px-8 py-8 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] sm:max-w-md sm:rounded-3xl border border-primary-200"
        >
            <slot />
        </div>
    </div>
</template>
