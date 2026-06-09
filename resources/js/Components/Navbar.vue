<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

// Switcher dropdown state & logic
const isDropdownOpen = ref(false);
const activeProfile = computed(() => page.props.auth?.active_profile);
const userProfiles = computed(() => page.props.auth?.user_profiles || []);

// Mobile menu state
const isMobileMenuOpen = ref(false);

const otherProfiles = computed(() => {
    if (!activeProfile.value) return userProfiles.value;
    return userProfiles.value.filter(p => p.id !== activeProfile.value.id);
});

const getInitials = (name) => {
    if (!name) return 'KM';
    return name
        .split(' ')
        .filter(n => n.length > 0)
        .map(n => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};
</script>

<template>
    <div>
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50 px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
            <!-- Left: Logo & Mobile Menu Button -->
            <div class="flex items-center gap-3">
                <!-- Mobile Hamburger Button (Only visible on mobile when authenticated) -->
                <button
                    v-if="authUser"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all duration-200"
                    aria-label="Toggle menu"
                >
                    <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group cursor-pointer select-none">
                    <img src="/images/navbar.svg" alt="KITA Logo" class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black tracking-wider text-white">
                            KITA
                        </span>
                        <span class="hidden sm:inline-block text-[9px] font-bold tracking-[0.3em] text-slate-500 uppercase border-l border-slate-800 pl-2">
                            TU MANAGER VIRTUAL
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Right Side: Dynamic Navigation Actions based on Auth -->
            <div class="flex items-center gap-4">
                <template v-if="authUser">
                    <!-- Desktop Private Navigation links inside the shared Navbar -->
                    <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-400">
                        <Link
                            href="/dashboard/tpv"
                            :class="[
                                'hover:text-white transition-colors duration-200 relative py-1',
                                $page.url.startsWith('/dashboard/tpv') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Mi TPV
                            <span v-if="$page.url.startsWith('/dashboard/tpv')" class="absolute bottom-0 left-0 w-full h-[2px] bg-cyan-400 rounded shadow-[0_0_8px_rgba(6,182,212,0.6)]"></span>
                        </Link>
                        <Link
                            href="/dashboard/medios"
                            :class="[
                                'hover:text-white transition-colors duration-200 relative py-1',
                                $page.url.startsWith('/dashboard/medios') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Almacén
                            <span v-if="$page.url.startsWith('/dashboard/medios')" class="absolute bottom-0 left-0 w-full h-[2px] bg-cyan-400 rounded shadow-[0_0_8px_rgba(6,182,212,0.6)]"></span>
                        </Link>
                        <Link
                            href="/dashboard/calendario"
                            :class="[
                                'hover:text-white transition-colors duration-200 relative py-1',
                                $page.url.startsWith('/dashboard/calendario') ? 'text-cyan-400 font-bold' : ''
                            ]"
                        >
                            Calendario
                            <span v-if="$page.url.startsWith('/dashboard/calendario')" class="absolute bottom-0 left-0 w-full h-[2px] bg-cyan-400 rounded shadow-[0_0_8px_rgba(6,182,212,0.6)]"></span>
                        </Link>
                    </nav>
                    
                    <!-- Switcher Dropdown & User Controls -->
                    <div class="flex items-center gap-4 relative">
                        <!-- Active Profile Trigger (Glassmorphism) -->
                        <button
                            v-if="activeProfile"
                            @click="isDropdownOpen = !isDropdownOpen"
                            class="bg-slate-950/60 backdrop-blur-md border border-slate-850 hover:border-cyan-500/40 rounded-full px-3 py-1.5 flex items-center gap-2.5 cursor-pointer transition-all duration-300 hover:shadow-[0_0_15px_rgba(var(--color-cyan-500),0.1)] group"
                        >
                            <!-- Profile Avatar with Neon Ring -->
                            <img
                                v-if="activeProfile.avatar_path || activeProfile.profile_photo_path"
                                :src="'/' + (activeProfile.avatar_path || activeProfile.profile_photo_path)"
                                :alt="activeProfile.name"
                                class="w-7 h-7 rounded-full object-cover ring-2 ring-cyan-500/40 ring-offset-1 ring-offset-slate-950"
                            />
                            <div
                                v-else
                                class="w-7 h-7 rounded-full bg-cyan-500 text-slate-950 flex items-center justify-center text-xs font-black ring-2 ring-cyan-500/40 ring-offset-1 ring-offset-slate-950"
                            >
                                {{ getInitials(activeProfile.name) }}
                            </div>
                            <span class="text-xs font-bold text-slate-200 max-w-[120px] truncate group-hover:text-white transition-colors">
                                {{ activeProfile.name }}
                            </span>
                            <!-- Chevron icon -->
                            <svg
                                class="w-3.5 h-3.5 text-slate-400 transition-transform duration-300"
                                :class="{ 'rotate-180 text-cyan-400': isDropdownOpen }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <span v-else class="text-xs text-slate-400 hidden sm:inline">
                            Hola, <strong class="text-white font-bold">{{ authUser.name }}</strong>
                        </span>

                        <!-- Click Outside Backdrop Overlay -->
                        <div v-if="isDropdownOpen" @click="isDropdownOpen = false" class="fixed inset-0 z-40 cursor-default"></div>

                        <!-- Dropdown Menu (Glassmorphic & Glowing) -->
                        <transition
                            enter-active-class="transition ease-out duration-150"
                            enter-from-class="opacity-0 scale-95 -translate-y-2"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-100"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 -translate-y-2"
                        >
                            <div
                                v-if="isDropdownOpen"
                                class="absolute right-0 top-full mt-3 w-64 bg-slate-900/90 backdrop-blur-xl border border-slate-800/80 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.5),0_0_20px_rgba(var(--color-cyan-500),0.1)] z-50 text-left"
                            >
                                <!-- Active Profile details -->
                                <div class="px-2 py-1.5" v-if="activeProfile">
                                    <span class="text-[9px] font-bold text-cyan-400 uppercase tracking-widest block mb-1">Perfil Activo</span>
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="activeProfile.avatar_path || activeProfile.profile_photo_path"
                                            :src="'/' + (activeProfile.avatar_path || activeProfile.profile_photo_path)"
                                            class="w-10 h-10 rounded-full object-cover ring-2 ring-cyan-500/30"
                                        />
                                        <div
                                            v-else
                                            class="w-10 h-10 rounded-full bg-cyan-500 text-slate-950 flex items-center justify-center text-sm font-black"
                                        >
                                            {{ getInitials(activeProfile.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-black text-white truncate leading-tight">{{ activeProfile.name }}</h4>
                                            <span class="text-[10px] text-slate-500 truncate block mt-0.5">{{ activeProfile.slug }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-px bg-slate-800/60 my-3" v-if="activeProfile"></div>

                                <!-- Other Profiles list -->
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block px-2 mb-1">Mis Perfiles</span>
                                    
                                    <div v-if="otherProfiles.length === 0" class="text-xs text-slate-500 px-2 py-1 italic">
                                        No tienes otros perfiles.
                                    </div>
                                    
                                    <Link
                                        v-for="profile in otherProfiles"
                                        :key="profile.id"
                                        :href="route('profile.switch', { id: profile.id })"
                                        method="post"
                                        as="button"
                                        class="w-full text-left flex items-center gap-3 px-2 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800/40 rounded-xl transition-all duration-200 cursor-pointer"
                                        @click="isDropdownOpen = false"
                                    >
                                        <img
                                            v-if="profile.avatar_path || profile.profile_photo_path"
                                            :src="'/' + (profile.avatar_path || profile.profile_photo_path)"
                                            class="w-6 h-6 rounded-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="w-6 h-6 rounded-full bg-slate-800 text-slate-200 flex items-center justify-center text-[10px] font-bold"
                                        >
                                            {{ getInitials(profile.name) }}
                                        </div>
                                        <span class="truncate font-semibold">{{ profile.name }}</span>
                                    </Link>
                                </div>

                                <div class="h-px bg-slate-800/60 my-3"></div>

                                <!-- Action buttons -->
                                <div class="space-y-2">
                                    <Link
                                        href="/dashboard/tpv/crear"
                                        class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 hover:border-cyan-500/40 font-bold text-xs tracking-wider transition-all duration-300 shadow-[0_0_15px_rgba(var(--color-cyan-500),0.02)] cursor-pointer"
                                        @click="isDropdownOpen = false"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Crear Perfil o Banda
                                    </Link>

                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="w-full py-2 text-center text-xs text-slate-550 hover:text-red-400 hover:bg-red-500/5 rounded-lg transition-colors cursor-pointer"
                                        @click="isDropdownOpen = false"
                                    >
                                        Cerrar Sesión
                                    </Link>
                                </div>
                            </div>
                        </transition>
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

        <!-- Mobile Sidebar Menu - Backdrop -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobileMenuOpen && authUser"
                @click="closeMobileMenu"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] md:hidden"
            ></div>
        </transition>

        <!-- Sliding Sidebar -->
        <transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition ease-in duration-200 transform"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-if="isMobileMenuOpen && authUser"
                class="fixed left-0 top-0 h-full w-80 bg-slate-900/98 backdrop-blur-xl border-r border-slate-800 shadow-2xl z-[70] md:hidden overflow-y-auto"
            >
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/50">
                    <div class="flex items-center gap-3">
                        <img src="/images/navbar.svg" alt="KITA Logo" class="h-9 w-auto" />
                        <span class="text-xl font-black text-white tracking-wide">KITA</span>
                    </div>
                    <button
                        @click="closeMobileMenu"
                        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-all duration-200"
                        aria-label="Close menu"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Active Profile Section -->
                <div class="p-5 border-b border-slate-800" v-if="activeProfile">
                    <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-widest block mb-3">Perfil Activo</span>
                    <div class="flex items-center gap-3 bg-slate-950/60 backdrop-blur-md border border-slate-850 rounded-xl p-4">
                        <img
                            v-if="activeProfile.avatar_path || activeProfile.profile_photo_path"
                            :src="'/' + (activeProfile.avatar_path || activeProfile.profile_photo_path)"
                            :alt="activeProfile.name"
                            class="w-14 h-14 rounded-full object-cover ring-2 ring-cyan-500/40"
                        />
                        <div
                            v-else
                            class="w-14 h-14 rounded-full bg-cyan-500 text-slate-950 flex items-center justify-center text-base font-black"
                        >
                            {{ getInitials(activeProfile.name) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-base font-black text-white truncate">{{ activeProfile.name }}</h4>
                            <span class="text-xs text-slate-500 truncate block mt-0.5">{{ activeProfile.slug }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-5 space-y-2">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block px-2 mb-3">Navegación</span>
                    
                    <Link
                        href="/dashboard/tpv"
                        @click="closeMobileMenu"
                        :class="[
                            'flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-200',
                            $page.url.startsWith('/dashboard/tpv')
                                ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800/40'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Mi TPV
                    </Link>

                    <Link
                        href="/dashboard/medios"
                        @click="closeMobileMenu"
                        :class="[
                            'flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-200',
                            $page.url.startsWith('/dashboard/medios')
                                ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800/40'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        Almacén
                    </Link>

                    <Link
                        href="/dashboard/calendario"
                        @click="closeMobileMenu"
                        :class="[
                            'flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-200',
                            $page.url.startsWith('/dashboard/calendario')
                                ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800/40'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Calendario
                    </Link>
                </nav>

                <!-- Other Profiles -->
                <div class="p-5 border-t border-slate-800">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block px-2 mb-3">Cambiar Perfil</span>
                    
                    <div v-if="otherProfiles.length === 0" class="text-sm text-slate-500 px-2 py-2 italic">
                        No tienes otros perfiles.
                    </div>

                    <div class="space-y-2">
                        <Link
                            v-for="profile in otherProfiles"
                            :key="profile.id"
                            :href="route('profile.switch', { id: profile.id })"
                            method="post"
                            as="button"
                            @click="closeMobileMenu"
                            class="w-full flex items-center gap-3 px-3 py-3 text-sm text-slate-400 hover:text-white hover:bg-slate-800/40 rounded-xl transition-all duration-200"
                        >
                            <img
                                v-if="profile.avatar_path || profile.profile_photo_path"
                                :src="'/' + (profile.avatar_path || profile.profile_photo_path)"
                                class="w-10 h-10 rounded-full object-cover"
                            />
                            <div
                                v-else
                                class="w-10 h-10 rounded-full bg-slate-800 text-slate-200 flex items-center justify-center text-xs font-bold"
                            >
                                {{ getInitials(profile.name) }}
                            </div>
                            <span class="truncate font-semibold">{{ profile.name }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-5 border-t border-slate-800 space-y-3 pb-6">
                    <Link
                        href="/dashboard/tpv/crear"
                        @click="closeMobileMenu"
                        class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 hover:border-cyan-500/40 font-bold text-sm tracking-wider transition-all duration-300 shadow-[0_0_15px_rgba(6,182,212,0.02)]"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        Crear Perfil o Banda
                    </Link>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        @click="closeMobileMenu"
                        class="w-full py-3 text-center text-sm text-slate-500 hover:text-red-400 hover:bg-red-500/5 rounded-xl transition-colors font-semibold"
                    >
                        Cerrar Sesión
                    </Link>
                </div>
            </aside>
        </transition>
    </div>
</template>
