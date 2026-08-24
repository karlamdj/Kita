<script setup>
import { ref, watch, computed } from 'vue';
import { router, Link, usePage, Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    musicians: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '', instrument: '', zone: '' }),
    },
    instrumentsList: {
        type: Array,
        default: () => ['Guitarrista', 'Baterista', 'Bajista', 'Vocalista', 'Banda Completa', 'Tecladista', 'Percusionista'],
    },
});

// Access auth state globally
const { props: pageProps } = usePage();
const authUser = pageProps.auth?.user;

// Reactive state for filters
const searchInput = ref(props.filters.search || '');
const activeInstrument = ref(props.filters.instrument || '');
const selectedSort = ref('featured');

// View layout state (grid or list)
const currentView = ref('grid');

// Persistent Favorites state
const favorites = ref(JSON.parse(localStorage.getItem('kita_favorites') || '[]'));

const toggleFavorite = (id) => {
    if (favorites.value.includes(id)) {
        favorites.value = favorites.value.filter(fid => fid !== id);
    } else {
        favorites.value.push(id);
    }
    localStorage.setItem('kita_favorites', JSON.stringify(favorites.value));
};

// Dynamic filter options
const instrumentsList = computed(() => props.instrumentsList);

// Function to execute filtering
const applyFilters = () => {
    router.get(
        '/',
        {
            search: searchInput.value,
            instrument: activeInstrument.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['musicians', 'filters'],
        }
    );
};

// Clear all active filters
const clearFilters = () => {
    searchInput.value = '';
    activeInstrument.value = '';
    applyFilters();
};

// Watch text search input with a slight debounce
let timeout = null;
watch(searchInput, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        applyFilters();
    }, 400);
});

// Watch dropdown select elements to auto-submit filters
watch(activeInstrument, () => {
    applyFilters();
});

// Helper to get initials
const getInitials = (name) => {
    if (!name) return 'KM';
    return name
        .split(' ')
        .filter(n => n.length > 0)
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

// Dynamic image resolver
const getMusicianPhoto = (musician) => {
    if (musician.profile_photo_path) {
        return '/' + musician.profile_photo_path;
    }
    // Premium background image fallback representing live music
    return 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3';
};

// Helper to resolve dynamic music genres / styles pills for each musician
const getMusicianTags = (musician) => {
    // Only return genres established by the musician in their TPV / profile
    const genres = Array.isArray(musician.genres) ? musician.genres.filter(g => typeof g === 'string' && g.trim().length > 0) : [];
    
    return {
        display: genres.slice(0, 4),
        extra: genres.length > 4 ? genres.length - 4 : 0
    };
};

// Sorted musicians list computed in frontend for instant reaction
const sortedMusicians = computed(() => {
    let list = [...props.musicians];
    if (selectedSort.value === 'name-asc') {
        list.sort((a, b) => a.name.localeCompare(b.name));
    } else if (selectedSort.value === 'name-desc') {
        list.sort((a, b) => b.name.localeCompare(a.name));
    }
    return list;
});
</script>

<template>
    <Head title="KITA - Tu Manager Virtual" />

    <div class="bg-[#050b14] text-slate-100 min-h-screen font-sans antialiased relative overflow-hidden selection:bg-cyan-500 selection:text-slate-950">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 inset-x-0 h-[600px] bg-gradient-to-b from-cyan-950/20 via-[#050b14] to-transparent pointer-events-none z-0"></div>
        <div class="absolute top-[20%] right-[-10%] w-[40vw] h-[40vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none z-0"></div>
        <div class="absolute bottom-[20%] left-[-10%] w-[45vw] h-[45vw] rounded-full bg-blue-600/5 blur-[140px] pointer-events-none z-0"></div>

        <!-- 1. Header & Navigation (Navbar) -->
        <Navbar />

        <!-- Main Wrapper -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16 relative z-10">
            
            <!-- 2. Hero Header Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-16">
                <!-- Hero Left Info -->
                <div class="lg:col-span-6 flex flex-col items-start text-left">
                    <!-- Cyan Badge -->
                    <span class="text-xs font-black tracking-[0.4em] text-cyan-400 mb-4 uppercase">
                        • CONECTA • COLABORA • CRECE •
                    </span>
                    <!-- Massive Title -->
                    <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-none">
                        Descubre <br />
                        <span class="bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                            Talento Musical
                        </span>
                    </h1>
                    <!-- Subtitle -->
                    <p class="text-slate-400 text-base sm:text-lg leading-relaxed max-w-xl mb-10">
                        Conecta directamente con músicos y bandas. Encuentra el talento ideal para tu próximo proyecto de forma transparente.
                    </p>

                    <!-- 3 Micro-Benefits Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 w-full border-t border-slate-900/60 pt-8">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-cyan-950/40 border border-cyan-850 shrink-0">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white leading-tight">Perfiles verificados</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Talento real y confiable</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-cyan-950/40 border border-cyan-850 shrink-0">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white leading-tight">Agenda directa</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Sin intermediarios</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-cyan-950/40 border border-cyan-850 shrink-0">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white leading-tight">Conecta y colabora</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Haz que suceda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Large Glowing Logo Graphic -->
                <div class="hidden lg:flex lg:col-span-6 relative w-full h-[320px] lg:h-[520px] items-center justify-center">
                    <div class="absolute -inset-10 bg-cyan-500/10 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="relative z-10 w-full max-w-[500px] h-full flex items-center justify-center">
                        <img src="/images/logo.svg" alt="KITA Logo" class="w-full h-auto drop-shadow-[0_0_35px_rgba(6,182,212,0.15)]" />
                    </div>
                </div>
            </div>

            <!-- 3. Search and Filtering Panel -->
            <div class="bg-slate-900/30 backdrop-blur-sm border border-slate-900 rounded-2xl p-6 mb-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    
                    <!-- Search input -->
                    <div class="relative md:col-span-5">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            v-model="searchInput"
                            type="text"
                            placeholder="Buscar por nombre o palabra clave"
                            class="bg-[#050b14]/50 border border-slate-800/80 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 focus:outline-none rounded-xl text-slate-200 placeholder-slate-500 w-full pl-10 pr-10 py-3 text-sm transition-all duration-300"
                        />
                        <button
                            v-if="searchInput"
                            @click="searchInput = ''"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Instrument select -->
                    <div class="md:col-span-4 relative">
                        <select
                            v-model="activeInstrument"
                            class="appearance-none bg-[#050b14]/50 border border-slate-800/80 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 focus:outline-none rounded-xl text-slate-300 py-3 px-4 w-full text-sm cursor-pointer"
                        >
                            <option value="">Filtrar por instrumento</option>
                            <option v-for="inst in instrumentsList" :key="inst" :value="inst">
                                {{ inst }}
                            </option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>



                    <!-- Sort select -->
                    <div class="md:col-span-3 relative">
                        <select
                            v-model="selectedSort"
                            class="appearance-none bg-[#050b14]/50 border border-slate-800/80 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 focus:outline-none rounded-xl text-slate-300 py-3 px-4 w-full text-sm cursor-pointer"
                        >
                            <option value="featured">Destacados</option>
                            <option value="name-asc">Nombre (A-Z)</option>
                            <option value="name-desc">Nombre (Z-A)</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>

                </div>

                <!-- Clear Active Filters -->
                <div v-if="activeInstrument || searchInput" class="flex justify-end mt-4">
                    <button
                        @click="clearFilters"
                        class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition-colors cursor-pointer"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- 4. Section Header with View Selector -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-900/60">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span>✨</span> Talento Destacado
                    </h2>
                </div>
                <!-- View Toggle Component -->
                <div class="flex items-center gap-3">
                    <span class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider">Vista</span>
                    <div class="flex bg-slate-950/60 p-1 rounded-xl border border-slate-900">
                        <button
                            @click="currentView = 'grid'"
                            :class="[
                                'flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-black transition-all duration-200 cursor-pointer',
                                currentView === 'grid'
                                    ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/15'
                                    : 'text-slate-500 hover:text-slate-300 border border-transparent'
                            ]"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Tarjetas
                        </button>
                        <button
                            @click="currentView = 'list'"
                            :class="[
                                'flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-black transition-all duration-200 cursor-pointer',
                                currentView === 'list'
                                    ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/15'
                                    : 'text-slate-500 hover:text-slate-300 border border-transparent'
                            ]"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Lista
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="sortedMusicians.length === 0" class="text-center py-20 bg-slate-900/10 border border-dashed border-slate-900 rounded-3xl p-8 mb-12">
                <svg class="h-16 w-16 text-slate-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
                <h3 class="text-xl font-bold text-white mb-2">No se encontraron músicos</h3>
                <p class="text-slate-400 mb-6 max-w-md mx-auto text-sm">
                    Intenta buscando con palabras clave diferentes o desactivando algunos de los filtros seleccionados arriba.
                </p>
                <button
                    @click="clearFilters"
                    class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 px-6 py-3 rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(6,182,212,0.2)] transition-all duration-300 cursor-pointer"
                >
                    Ver Todo el Directorio
                </button>
            </div>

            <!-- 5. Grid View -->
            <div v-else-if="currentView === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <article
                    v-for="musician in sortedMusicians"
                    :key="musician.id"
                    class="bg-[#0d1527]/50 border border-slate-900/80 rounded-2xl flex flex-col justify-between overflow-hidden group hover:border-cyan-500/30 hover:shadow-[0_0_30px_rgba(6,182,212,0.08)] hover:-translate-y-0.5 transition-[border-color,box-shadow,transform] duration-300"
                >
                    <!-- Card Top Header (Cover photo with floating badges) -->
                    <div class="relative h-48 w-full overflow-hidden shrink-0 select-none card-cover-photo bg-slate-900">
                        <img
                            :src="getMusicianPhoto(musician)"
                            :alt="musician.name"
                            class="w-full h-48 object-cover rounded-t-2xl group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                        


                        <!-- Favorites Heart Icon -->
                        <button
                            @click="toggleFavorite(musician.id)"
                            class="absolute top-4 right-4 p-2 rounded-lg bg-[#0d1527]/80 backdrop-blur-md border border-slate-800 text-slate-400 hover:text-red-500 transition-colors cursor-pointer"
                        >
                            <svg
                                class="w-4 h-4"
                                :class="{ 'fill-red-500 stroke-red-500 text-red-500': favorites.includes(musician.id) }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.302-1.302a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Center Info: Avatar + Artist Name & Location -->
                            <div class="flex items-center gap-4 mb-5">
                                <!-- Cyan Avatar with Initials -->
                                <div class="w-12 h-12 rounded-full bg-cyan-400 text-slate-950 font-black text-sm flex items-center justify-center shadow-[0_0_15px_rgba(6,182,212,0.3)] shrink-0 select-none">
                                    {{ getInitials(musician.name) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <Link :href="`/${musician.slug}`" class="block">
                                        <h3 class="text-lg font-black text-white hover:text-cyan-400 transition-colors leading-tight truncate">
                                            {{ musician.name }}
                                        </h3>
                                    </Link>
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded bg-cyan-950/80 text-cyan-400 border border-cyan-800/30 text-[10px] font-bold">
                                            {{ musician.instruments?.[0] || 'Músico' }}
                                        </span>
                                        <span class="text-xs text-slate-400 flex items-center gap-1 truncate">
                                            <!-- Map Pin Icon -->
                                            <svg class="h-3 w-3 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ Array.isArray(musician.coverage_area) ? musician.coverage_area[0] : musician.coverage_area }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio Extract -->
                            <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed mb-6">
                                {{ musician.bio || 'Músico profesional dedicado a ofrecer las mejores presentaciones y shows en vivo.' }}
                            </p>
                        </div>

                        <!-- Specialty Tags / Pills (Estilos de Música / Géneros) -->
                        <div v-if="getMusicianTags(musician).display.length > 0" class="flex flex-wrap gap-2 mb-6">
                            <span
                                v-for="tag in getMusicianTags(musician).display"
                                :key="tag"
                                class="genre-pill px-2.5 py-1 text-xs rounded-md font-medium"
                            >
                                {{ tag }}
                            </span>
                            <span
                                v-if="getMusicianTags(musician).extra > 0"
                                class="genre-pill-extra px-2.5 py-1 text-xs rounded-md font-semibold select-none"
                            >
                                +{{ getMusicianTags(musician).extra }}
                            </span>
                        </div>

                        <!-- Card Footer -->
                        <div class="border-t border-slate-900 pt-4 flex items-center justify-between">
                            <!-- Green Agenda Dot Indicator -->
                            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                <span class="flex h-2 w-2 relative shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="font-medium text-slate-300">Agenda disponible</span>
                            </div>

                            <!-- Link to Profile TPV -->
                            <Link
                                :href="`/${musician.slug}`"
                                class="text-cyan-400 hover:text-cyan-300 font-bold text-xs flex items-center gap-1 transition-colors select-none"
                            >
                                Ver perfil
                                <svg class="h-3 w-3 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <!-- List View alternative layout -->
            <div v-else-if="currentView === 'list'" class="flex flex-col gap-4 mb-12">
                <article
                    v-for="musician in sortedMusicians"
                    :key="musician.id"
                    class="bg-[#0d1527]/50 border border-slate-900/80 rounded-2xl flex flex-col sm:flex-row items-stretch overflow-hidden group hover:border-cyan-500/30 hover:shadow-[0_0_30px_rgba(6,182,212,0.08)] transition-[border-color,box-shadow,transform] duration-300"
                >
                    <!-- Left Photo -->
                    <div class="relative w-full sm:w-48 h-48 sm:h-auto shrink-0 select-none card-cover-photo bg-slate-900 overflow-hidden">
                        <img
                            :src="getMusicianPhoto(musician)"
                            :alt="musician.name"
                            class="w-full h-48 sm:h-full object-cover group-hover:scale-103 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none sm:hidden"></div>
                    </div>

                    <!-- Right Details Content -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-cyan-400 text-slate-950 font-black text-xs flex items-center justify-center shrink-0">
                                        {{ getInitials(musician.name) }}
                                    </div>
                                    <div>
                                        <Link :href="`/${musician.slug}`">
                                            <h3 class="text-lg font-black text-white hover:text-cyan-400 transition-colors leading-tight">
                                                {{ musician.name }}
                                            </h3>
                                        </Link>
                                        <p class="text-xs text-cyan-400 font-bold mt-0.5">
                                            {{ musician.instruments?.[0] || 'Músico' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-slate-400">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ Array.isArray(musician.coverage_area) ? musician.coverage_area.join(', ') : musician.coverage_area }}
                                    </span>
                                    <!-- Favorites -->
                                    <button
                                        @click="toggleFavorite(musician.id)"
                                        class="text-slate-500 hover:text-red-500 transition-colors cursor-pointer"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            :class="{ 'fill-red-500 stroke-red-500 text-red-500': favorites.includes(musician.id) }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.302-1.302a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-slate-400 text-sm line-clamp-2 leading-relaxed mb-4">
                                {{ musician.bio || 'Músico profesional dedicado a ofrecer las mejores presentaciones y shows en vivo.' }}
                            </p>
                        </div>

                        <!-- Lower layout in list view -->
                        <div class="border-t border-slate-900 pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div v-if="getMusicianTags(musician).display.length > 0" class="flex flex-wrap gap-1.5 items-center">
                                <span
                                    v-for="tag in getMusicianTags(musician).display"
                                    :key="tag"
                                    class="genre-pill px-2 py-0.5 text-xs rounded font-medium"
                                >
                                    {{ tag }}
                                </span>
                                <span
                                    v-if="getMusicianTags(musician).extra > 0"
                                    class="genre-pill-extra px-2 py-0.5 text-xs rounded font-semibold select-none"
                                >
                                    +{{ getMusicianTags(musician).extra }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="flex h-2 w-2 relative shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-slate-300 font-semibold">Agenda disponible</span>
                                </div>
                                <Link
                                    :href="`/${musician.slug}`"
                                    class="text-cyan-400 hover:text-cyan-300 font-bold text-xs flex items-center gap-0.5 transition-colors"
                                >
                                    Ver perfil
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

        </main>

        <!-- 6. Footer -->
        <footer class="border-t border-slate-900/60 py-10 mt-16 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center sm:justify-start">
                    <!-- Left: Copyright -->
                    <p class="text-xs text-slate-500 font-medium text-center sm:text-left select-none">
                        © 2026 KITA - Tu Manager Virtual | by VM Technologies. | Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Smooth custom scrollbars and transition highlights */
select {
    background-image: none;
}
</style>
