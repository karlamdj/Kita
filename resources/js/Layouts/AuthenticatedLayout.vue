<script setup>
import { ref, computed, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const page = usePage();

// Computed property activeTheme to listen to active profile's theme
const activeTheme = computed(() => {
    return page.props.auth?.active_profile?.theme || 'kita-neon';
});

const themeOverride = ref(null);
const currentTheme = computed(() => themeOverride.value || activeTheme.value);
const profileThemeClass = computed(() => 'theme-' + currentTheme.value);

provide('setThemeOverride', (theme) => {
    themeOverride.value = theme;
});
</script>

<template>
    <div :class="[profileThemeClass, 'bg-slate-950 text-slate-100 min-h-screen font-sans antialiased overflow-x-hidden relative']">
        
        <!-- Glowing Top Border Decorator -->
        <div class="h-[2px] w-full bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-400 shadow-[0_1px_10px_rgba(var(--color-cyan-500),0.4)] z-[60] sticky top-0"></div>

        <!-- Reusable Global Navbar -->
        <Navbar />

        <!-- Ambient Decorative Glowing Blobs (Dynamic Colors) -->
        <div class="absolute top-[-10%] left-[-20%] w-[80vw] h-[80vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none z-0 transition-all duration-700"></div>
        <div class="absolute bottom-[20%] right-[-30%] w-[90vw] h-[90vw] rounded-full bg-blue-500/5 blur-[130px] pointer-events-none z-0 transition-all duration-700"></div>

        <div class="relative z-10 flex flex-col min-h-[calc(100vh-4rem)]">
            <!-- Page Heading (Glassmorphism layout header) -->
            <header
                class="bg-slate-900/30 border-b border-slate-900/60 backdrop-blur-sm relative py-6"
                v-if="$slots.header"
            >
                <!-- Double Neon Line Accents on Header -->
                <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-cyan-500/10 to-transparent"></div>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
