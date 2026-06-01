<script setup>
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased overflow-x-hidden relative">
        
        <!-- Reusable Global Navbar -->
        <Navbar />

        <!-- Ambient Decorative Glowing Blobs (Premium Visual Aesthetics) -->
        <div class="absolute top-[-10%] left-[-20%] w-[80vw] h-[80vw] rounded-full bg-cyan-900/10 blur-[120px] pointer-events-none z-0"></div>
        <div class="absolute bottom-[20%] right-[-30%] w-[90vw] h-[90vw] rounded-full bg-blue-900/10 blur-[130px] pointer-events-none z-0"></div>

        <div class="relative z-10 flex flex-col min-h-[calc(100vh-4rem)]">
            <!-- Page Heading (Glassmorphism layout header) -->
            <header
                class="bg-slate-900/40 border-b border-slate-800/80 backdrop-blur-sm relative py-6"
                v-if="$slots.header"
            >
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
