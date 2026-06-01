<script setup>
import { ref, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
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
});

// Access auth state globally
const { props: pageProps } = usePage();
const authUser = pageProps.auth?.user;

// Reactive state for filters
const searchInput = ref(props.filters.search || '');
const activeInstrument = ref(props.filters.instrument || '');
const activeZone = ref(props.filters.zone || '');

// Static filter options
const instrumentsList = ['Guitarrista', 'Baterista', 'Bajista', 'Vocalista', 'Banda Completa'];
const zonesList = ['Centro', 'Zona Hotelera', 'Marina Vallarta', 'Nuevo Vallarta', 'Bucerías', 'Sayulita'];

// function to execute filtering
const applyFilters = () => {
    router.get(
        '/',
        {
            search: searchInput.value,
            instrument: activeInstrument.value,
            zone: activeZone.value,
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
    activeZone.value = '';
    applyFilters();
};

// Toggle instrument selection
const selectInstrument = (inst) => {
    activeInstrument.value = activeInstrument.value === inst ? '' : inst;
    applyFilters();
};

// Toggle zone selection
const selectZone = (zone) => {
    activeZone.value = activeZone.value === zone ? '' : zone;
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

// Helper to get initials
const getInitials = (name) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};
</script>

<template>
    <div class="bg-slate-950 text-slate-100 min-h-screen font-sans selection:bg-cyan-500 selection:text-slate-950">
        <!-- Navigation Header -->
        <Navbar />

        <!-- Main Workspace -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Hero Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-white mb-4">
                    Descubre el Talento Musical de la
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                        Bahía
                    </span>
                </h1>
                <p class="text-slate-400 text-base sm:text-lg">
                    Conecta directamente con músicos y bandas en Puerto Vallarta y Bahía de Banderas. Filtra por instrumento, zona o busca por nombre.
                </p>
            </div>

            <!-- Search and Filtering Panel -->
            <div class="bg-slate-900/60 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 mb-10 shadow-xl">
                <!-- Text Search Input -->
                <div class="relative max-w-2xl mx-auto mb-6">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="searchInput"
                        type="text"
                        placeholder="Buscar por nombre, biografía o palabras clave..."
                        class="bg-slate-950 border border-slate-800 focus:border-cyan-500 focus:ring focus:ring-cyan-950/50 focus:outline-none rounded-xl text-slate-100 placeholder-slate-500 w-full pl-10 pr-10 py-3 transition-all duration-300 shadow-inner"
                    />
                    <button
                        v-if="searchInput"
                        @click="searchInput = ''"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Instrument Filter Pills -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">
                        Filtrar por Instrumento
                    </h3>
                    <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
                        <button
                            v-for="inst in instrumentsList"
                            :key="inst"
                            @click="selectInstrument(inst)"
                            :class="[
                                'px-4 py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all duration-300 border cursor-pointer select-none',
                                activeInstrument === inst
                                    ? 'bg-cyan-500 text-slate-950 border-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.4)] font-bold scale-105'
                                    : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200'
                            ]"
                        >
                            {{ inst }}
                        </button>
                    </div>
                </div>

                <!-- Zone Filter Pills -->
                <div class="mb-2">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">
                        Filtrar por Zona de Cobertura
                    </h3>
                    <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
                        <button
                            v-for="zone in zonesList"
                            :key="zone"
                            @click="selectZone(zone)"
                            :class="[
                                'px-4 py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all duration-300 border cursor-pointer select-none',
                                activeZone === zone
                                    ? 'bg-blue-500 text-white border-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.4)] font-bold scale-105'
                                    : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700 hover:text-slate-200'
                            ]"
                        >
                            {{ zone }}
                        </button>
                    </div>
                </div>

                <!-- Clear Active Filters -->
                <div v-if="activeInstrument || activeZone || searchInput" class="flex justify-end mt-4">
                    <button
                        @click="clearFilters"
                        class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Musicians Grid -->
            <div>
                <!-- Empty State -->
                <div v-if="musicians.length === 0" class="text-center py-20 bg-slate-900/20 border border-dashed border-slate-800 rounded-2xl p-8">
                    <svg class="h-16 w-16 text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    <h3 class="text-xl font-bold text-white mb-2">No se encontraron músicos</h3>
                    <p class="text-slate-400 mb-6 max-w-md mx-auto">
                        Intenta buscando con palabras clave diferentes o desactivando algunos de los filtros seleccionados arriba.
                    </p>
                    <button
                        @click="clearFilters"
                        class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 px-5 py-2.5 rounded-full text-sm font-bold shadow-[0_0_15px_rgba(6,182,212,0.3)] transition-all duration-300"
                    >
                        Ver Todo el Directorio
                    </button>
                </div>

                <!-- Cards Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article
                        v-for="musician in musicians"
                        :key="musician.id"
                        class="bg-slate-900/60 backdrop-blur-sm border border-slate-800/80 rounded-2xl p-6 hover:border-cyan-500/40 hover:shadow-[0_4px_30px_rgba(6,182,212,0.06)] transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <!-- Header Info -->
                            <div class="flex items-start gap-4 mb-4">
                                <!-- Avatar Link -->
                                <Link
                                    :href="`/${musician.slug}`"
                                    class="w-14 h-14 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-slate-950 font-black text-lg shadow-[0_0_15px_rgba(6,182,212,0.3)] shrink-0 hover:scale-105 transition-transform duration-300 select-none"
                                >
                                    {{ getInitials(musician.name) }}
                                </Link>
                                <div class="min-w-0">
                                    <Link :href="`/${musician.slug}`" class="block">
                                        <h2 class="text-xl font-bold text-white hover:text-cyan-400 transition-colors duration-300 truncate">
                                            {{ musician.name }}
                                        </h2>
                                    </Link>
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        <span
                                            v-for="inst in musician.instruments"
                                            :key="inst"
                                            class="inline-flex items-center px-2 py-0.5 rounded bg-cyan-950/80 text-cyan-400 border border-cyan-800/30 text-[10px] sm:text-xs font-semibold"
                                        >
                                            {{ inst }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-950/80 text-blue-400 border border-blue-800/30 text-[10px] sm:text-xs font-semibold">
                                            {{ Array.isArray(musician.coverage_area) ? musician.coverage_area.join(', ') : musician.coverage_area }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio -->
                            <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed mb-6">
                                {{ musician.bio }}
                            </p>
                        </div>

                        <!-- Footer Details -->
                        <div class="border-t border-slate-800/60 pt-4 flex items-center justify-between text-xs text-slate-500">
                            <!-- Next Show indicator -->
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="flex h-2 w-2 relative shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="truncate">
                                    {{ musician.events?.length > 0 ? 'Agenda disponible' : 'Consultar disponibilidad' }}
                                </span>
                            </div>

                            <!-- View profile CTA -->
                            <Link
                                :href="`/${musician.slug}`"
                                class="text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-0.5 shrink-0 transition-colors group-hover:translate-x-0.5 duration-200"
                            >
                                Perfil
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
/* Custom horizontal scrollbar hiding */
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
