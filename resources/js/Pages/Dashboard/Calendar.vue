<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
});

// Months and Weekdays constants
const months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];
const weekdays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

// Reactive Calendar states
const currentYear = ref(new Date().getFullYear());
const currentMonth = ref(new Date().getMonth()); // 0-11
const selectedDate = ref(new Date().toISOString().split('T')[0]);

// Navigation
const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

// Generate Calendar Days list for the grid
const calendarDays = computed(() => {
    const days = [];
    const firstDayIndex = new Date(currentYear.value, currentMonth.value, 1).getDay();
    const totalDays = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();

    // Fill blank padding days for the previous month
    for (let i = 0; i < firstDayIndex; i++) {
        days.push({ day: null, dateString: null });
    }

    // Fill days of the current month
    for (let d = 1; d <= totalDays; d++) {
        const monthStr = String(currentMonth.value + 1).padStart(2, '0');
        const dayStr = String(d).padStart(2, '0');
        const dateString = `${currentYear.value}-${monthStr}-${dayStr}`;
        days.push({ day: d, dateString });
    }

    return days;
});

// Filter events by specific date
const getEventsForDate = (dateString) => {
    if (!dateString) return [];
    return props.events.filter(event => {
        const eventDate = new Date(event.start_time).toISOString().split('T')[0];
        return eventDate === dateString;
    });
};

// Selection click
const selectDay = (day) => {
    if (day.dateString) {
        selectedDate.value = day.dateString;
        form.date = day.dateString;
    }
};

// Active list of events for the selected day
const selectedDayEvents = computed(() => {
    return getEventsForDate(selectedDate.value);
});

// Setup Form for quick adding
const form = useForm({
    title: '',
    type: 'concierto',
    date: selectedDate.value,
    start_time: '19:00',
    end_time: '21:00',
    location: '',
    description: '',
});

const submitEvent = () => {
    form.post('/dashboard/calendario/eventos', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('title', 'location', 'description');
        },
    });
};

const deleteEvent = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta fecha de tu agenda?')) {
        router.delete(`/dashboard/calendario/eventos/${id}`, {
            preserveScroll: true,
        });
    }
};

// Map event type to colors
const getTypeColor = (type) => {
    switch (type) {
        case 'concierto':
            return {
                bg: 'bg-rose-500/20 border-rose-500/40 text-rose-300',
                pill: 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.4)]',
            };
        case 'ensayo':
            return {
                bg: 'bg-purple-500/20 border-purple-500/40 text-purple-300',
                pill: 'bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.4)]',
            };
        case 'disponible':
            return {
                bg: 'bg-cyan-500/20 border-cyan-500/40 text-cyan-300',
                pill: 'bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.4)]',
            };
        default:
            return {
                bg: 'bg-slate-800 border-slate-700 text-slate-400',
                pill: 'bg-slate-600',
            };
    }
};

// Format show dates nicely
const formatEventTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};
</script>

<template>
    <Head title="Calendario y Disponibilidad - KITA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-white">
                Control de Disponibilidad y Fechas
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Bloquea fechas ocupadas o abre espacios disponibles para que tus clientes te coticen de inmediato.
            </p>
        </template>

        <div class="py-8 bg-slate-950 min-h-[calc(100vh-4rem)] text-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Layout Grid (Calendar + Controls) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left & Mid Column: Monthly Calendar -->
                    <div class="lg:col-span-2 bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <!-- Calendar Header Navigator -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-black text-white">
                                    {{ months[currentMonth] }}
                                    <span class="text-cyan-400 font-medium ml-1">{{ currentYear }}</span>
                                </h3>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button
                                    @click="prevMonth"
                                    class="p-2 bg-slate-950 border border-slate-850 hover:border-slate-750 text-slate-400 hover:text-white rounded-lg transition-all"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button
                                    @click="nextMonth"
                                    class="p-2 bg-slate-950 border border-slate-850 hover:border-slate-750 text-slate-400 hover:text-white rounded-lg transition-all"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Days of Week Initials Header -->
                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3 border-b border-slate-800/60 pb-2">
                            <span v-for="day in weekdays" :key="day">{{ day }}</span>
                        </div>

                        <!-- Monthly Days Grid -->
                        <div class="grid grid-cols-7 gap-2">
                            <div
                                v-for="(day, index) in calendarDays"
                                :key="index"
                                @click="selectDay(day)"
                                :class="[
                                    'aspect-square border rounded-xl p-1.5 flex flex-col justify-between transition-all duration-200 select-none relative',
                                    day.day ? 'cursor-pointer' : 'opacity-0 pointer-events-none',
                                    selectedDate === day.dateString
                                        ? 'bg-slate-950 border-cyan-500 shadow-[0_0_15px_rgba(6,182,212,0.15)] scale-[1.02]'
                                        : 'bg-slate-950/40 border-slate-850/60 hover:border-slate-750'
                                ]"
                            >
                                <!-- Day Number -->
                                <span
                                    :class="[
                                        'text-xs font-bold font-mono shrink-0 flex items-center justify-center w-5 h-5 rounded-full',
                                        selectedDate === day.dateString ? 'bg-cyan-500 text-slate-950 font-black' : 'text-slate-400'
                                    ]"
                                >
                                    {{ day.day }}
                                </span>

                                <!-- Indicators Pills -->
                                <div v-if="day.day && getEventsForDate(day.dateString).length > 0" class="flex flex-wrap gap-1 mt-1 justify-end max-w-full overflow-hidden">
                                    <span
                                        v-for="event in getEventsForDate(day.dateString).slice(0, 3)"
                                        :key="event.id"
                                        :class="['h-2 w-2 rounded-full shrink-0', getTypeColor(event.type).pill]"
                                        :title="event.title"
                                    ></span>
                                </div>
                            </div>
                        </div>

                        <!-- Color Legends/Glossary -->
                        <div class="flex flex-wrap gap-4 mt-6 border-t border-slate-800/80 pt-4 text-xs font-semibold text-slate-400">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.5)]"></span>
                                Disponible / Abierto
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                                Concierto / Show Ocupado
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></span>
                                Ensayo / Bloqueo Interno
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Event Form and Lists -->
                    <div class="flex flex-col gap-6">
                        
                        <!-- Form Card -->
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                            <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                                <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Añadir Fecha / Bloqueo
                            </h3>

                            <form @submit.prevent="submitEvent" class="flex flex-col gap-3">
                                <!-- Title input -->
                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">Título o Notas</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        placeholder="Ej: Boda en Sayulita, Disponible, Ensayo..."
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                    <span v-if="form.errors.title" class="text-xs text-red-400 mt-1 block">{{ form.errors.title }}</span>
                                </div>

                                <!-- Date input -->
                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">Fecha</label>
                                    <input
                                        v-model="form.date"
                                        type="date"
                                        required
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                    <span v-if="form.errors.date" class="text-xs text-red-400 mt-1 block">{{ form.errors.date }}</span>
                                </div>

                                <!-- Hours Inputs -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-semibold text-slate-400 block mb-1">Hora Inicio</label>
                                        <input
                                            v-model="form.start_time"
                                            type="time"
                                            required
                                            class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                        />
                                        <span v-if="form.errors.start_time" class="text-xs text-red-400 mt-1 block">{{ form.errors.start_time }}</span>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-slate-400 block mb-1">Hora Fin</label>
                                        <input
                                            v-model="form.end_time"
                                            type="time"
                                            required
                                            class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                        />
                                        <span v-if="form.errors.end_time" class="text-xs text-red-400 mt-1 block">{{ form.errors.end_time }}</span>
                                    </div>
                                </div>

                                <!-- Type Select -->
                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">Tipo de Registro</label>
                                    <select
                                        v-model="form.type"
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    >
                                        <option value="concierto">Concierto / Show (Ocupado)</option>
                                        <option value="ensayo">Ensayo / Bloqueo Interno</option>
                                        <option value="disponible">Abierto / Disponible</option>
                                    </select>
                                    <span v-if="form.errors.type" class="text-xs text-red-400 mt-1 block">{{ form.errors.type }}</span>
                                </div>

                                <!-- Location input -->
                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">Ubicación / Lugar (Opcional)</label>
                                    <input
                                        v-model="form.location"
                                        type="text"
                                        placeholder="Ej: El Shamrock, Sheraton Hotel..."
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                    <span v-if="form.errors.location" class="text-xs text-red-400 mt-1 block">{{ form.errors.location }}</span>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-cyan-500 disabled:bg-slate-800 disabled:text-slate-500 hover:bg-cyan-400 text-slate-950 font-black py-2.5 rounded-lg text-sm transition-all duration-300 shadow-[0_0_15px_rgba(6,182,212,0.2)] hover:scale-[1.01] flex items-center justify-center gap-1.5 mt-2"
                                >
                                    <span v-if="form.processing" class="animate-spin h-4 w-4 border-2 border-slate-950 border-t-transparent rounded-full"></span>
                                    Guardar Fecha
                                </button>
                            </form>
                        </div>

                        <!-- Selected Date Gigs List -->
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-md font-bold text-white mb-4 border-b border-slate-800 pb-2.5 flex items-center justify-between">
                                    <span>Agendado el {{ selectedDate.split('-')[2] }}/{{ selectedDate.split('-')[1] }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-950 border border-slate-850 rounded-full text-slate-500">
                                        {{ selectedDayEvents.length }} {{ selectedDayEvents.length === 1 ? 'fecha' : 'fechas' }}
                                    </span>
                                </h3>

                                <div v-if="selectedDayEvents.length === 0" class="text-center py-8 text-slate-500 text-xs">
                                    No hay eventos registrados para este día.
                                </div>
                                <div v-else class="flex flex-col gap-2.5 max-h-[220px] overflow-y-auto pr-1">
                                    <div
                                        v-for="event in selectedDayEvents"
                                        :key="event.id"
                                        :class="['p-3 border rounded-xl flex items-center justify-between gap-3', getTypeColor(event.type).bg]"
                                    >
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold leading-tight truncate">{{ event.title }}</h4>
                                            <p class="text-[10px] opacity-70 mt-1 truncate">
                                                {{ formatEventTime(event.start_time) }} - {{ formatEventTime(event.end_time) }}
                                            </p>
                                            <p v-if="event.location" class="text-[9px] opacity-60 mt-0.5 truncate">
                                                📍 {{ event.location }}
                                            </p>
                                        </div>

                                        <button
                                            @click="deleteEvent(event.id)"
                                            class="p-1.5 rounded-lg bg-slate-950/60 text-slate-400 hover:text-red-400 hover:bg-red-950/20 border border-slate-850 hover:border-red-900/30 transition-all duration-300 shrink-0"
                                            title="Eliminar fecha"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Scrollbar custom for selection list */
.max-h-\[220px\]::-webkit-scrollbar {
    width: 4px;
}
.max-h-\[220px\]::-webkit-scrollbar-track {
    background: transparent;
}
.max-h-\[220px\]::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 9999px;
}
</style>
