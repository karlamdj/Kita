<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
</script>

<template>
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50 px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
            <!-- Left Side: Logo & Subtitle linking to Home -->
            <Link href="/" class="flex items-center gap-2 group cursor-pointer select-none">
                <span class="text-2xl font-extrabold tracking-wider bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent group-hover:opacity-90 transition-opacity">
                    KITA
                </span>
                <span class="hidden sm:inline-block text-xs font-semibold tracking-wider text-slate-400 uppercase border-l border-slate-700 pl-2">
                    Tu Manager Virtual
                </span>
            </Link>

            <!-- Right Side: Dynamic Navigation Actions based on Auth -->
            <div class="flex items-center gap-4">
                <template v-if="authUser">
                    <!-- Desktop Private Navigation links inside the shared Navbar -->
                    <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-400">
                        <Link
                            href="/dashboard/tpv"
                            :class="[
                                'hover:text-white transition-colors',
                                $page.url.startsWith('/dashboard/tpv') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Mi TPV
                        </Link>
                        <Link
                            href="/dashboard/medios"
                            :class="[
                                'hover:text-white transition-colors',
                                $page.url.startsWith('/dashboard/medios') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Almacén
                        </Link>
                        <Link
                            href="/dashboard/calendario"
                            :class="[
                                'hover:text-white transition-colors',
                                $page.url.startsWith('/dashboard/calendario') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Calendario
                        </Link>
                    </nav>
                    
                    <!-- User Controls -->
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 hidden sm:inline">
                            Hola, <strong class="text-white font-bold">{{ authUser.name }}</strong>
                        </span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="bg-slate-950 border border-slate-850 hover:border-slate-750 text-slate-400 hover:text-white px-4 py-2 rounded-full text-xs font-semibold transition-all duration-300 hover:scale-105 cursor-pointer"
                        >
                            Salir
                        </Link>
                    </div>
                </template>
                <template v-else>
                    <!-- Guest Options -->
                    <Link
                        href="/login"
                        class="text-slate-400 hover:text-white px-3 py-2 text-sm font-semibold transition-colors duration-200"
                    >
                        Iniciar Sesión
                    </Link>
                    <Link
                        href="/register"
                        class="border border-cyan-500/50 hover:border-cyan-400 hover:shadow-[0_0_15px_rgba(6,182,212,0.4)] text-cyan-400 hover:text-white px-4 py-2 rounded-full transition-all duration-300 text-sm font-semibold hover:scale-105"
                    >
                        Registrarse
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
