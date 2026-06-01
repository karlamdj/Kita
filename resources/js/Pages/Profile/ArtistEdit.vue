<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    profile: {
        type: Object,
        required: true,
    },
});

// Setup common instruments list for selection
const commonInstruments = ['Guitarrista', 'Baterista', 'Bajista', 'Vocalista', 'Banda Completa', 'Tecladista', 'Percusionista'];

// Setup common zones list for selection
const commonZones = ['Centro', 'Zona Hotelera', 'Marina Vallarta', 'Nuevo Vallarta', 'Bucerías', 'Sayulita'];

// Helper to get initials
const getInitials = (name) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

// Initialize Form with profile data
const form = useForm({
    _method: 'PUT', // Use method spoofing so Inertia can upload files via POST under Laravel PUT validation
    name: props.profile.name || '',
    slug: props.profile.slug || '',
    coverage_area: Array.isArray(props.profile.coverage_area)
        ? props.profile.coverage_area
        : (props.profile.coverage_area ? [props.profile.coverage_area] : []),
    bio: props.profile.bio || '',
    instruments: props.profile.instruments || [],
    profile_photo: null,
    widget_status: {
        agenda: props.profile.widget_status?.agenda !== false,
        media: props.profile.widget_status?.media !== false,
        whatsapp: props.profile.widget_status?.whatsapp || '',
        instagram: props.profile.widget_status?.instagram || '',
        spotify: props.profile.widget_status?.spotify || '',
        facebook: props.profile.widget_status?.facebook || '',
        youtube: props.profile.widget_status?.youtube || '',
    },
});

// Toggle instrument selection
const toggleInstrument = (inst) => {
    const index = form.instruments.indexOf(inst);
    if (index > -1) {
        form.instruments.splice(index, 1);
    } else {
        form.instruments.push(inst);
    }
};

// Handle sluggify on input
const onSlugInput = (e) => {
    form.slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-');
};

// Local ref for the tag input text
const tagInput = ref('');

// Add tag to coverage_area
const addZoneTag = () => {
    const value = tagInput.value.trim();
    if (value) {
        // Handle comma-separated values
        const zones = value.split(',').map(z => z.trim()).filter(z => z.length > 0);
        zones.forEach(zone => {
            if (!form.coverage_area.includes(zone)) {
                form.coverage_area.push(zone);
            }
        });
        tagInput.value = '';
    }
};

// Remove tag from coverage_area
const removeZoneTag = (zone) => {
    const index = form.coverage_area.indexOf(zone);
    if (index > -1) {
        form.coverage_area.splice(index, 1);
    }
};

// Photo Upload Interactive Preview
const photoPreviewUrl = ref(props.profile.profile_photo_path ? '/' + props.profile.profile_photo_path : null);
const onPhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.profile_photo = file;
        photoPreviewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post('/dashboard/tpv/editar', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Configurar Mi TPV - KITA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-white">
                Mi Tarjeta de Presentación Virtual (TPV)
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Personaliza la información de tu portafolio público compartible.
            </p>
        </template>

        <div class="py-8 bg-slate-950 min-h-[calc(100vh-4rem)] text-slate-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Success Notification -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800/40 text-emerald-400 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-lg">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-8">
                    
                    <!-- 1. Basic Info Section -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Información Básica
                        </h3>

                        <!-- Profile Photo Upload Block -->
                        <div class="flex flex-col sm:flex-row items-center gap-6 mb-8 border-b border-slate-800/60 pb-6">
                            <!-- Image Preview Circle -->
                            <div class="relative w-28 h-28 rounded-2xl overflow-hidden border-2 border-purple-500/20 bg-slate-950 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(168,85,247,0.15)] group">
                                <img
                                    v-if="photoPreviewUrl"
                                    :src="photoPreviewUrl"
                                    alt="Profile Preview"
                                    class="w-full h-full object-cover animate-fade-in"
                                />
                                <div v-else class="text-purple-400 font-black text-2xl uppercase">
                                    {{ getInitials(form.name) }}
                                </div>
                            </div>
                            
                            <!-- Upload Controls -->
                            <div class="flex-1 text-center sm:text-left">
                                <h4 class="text-sm font-bold text-white mb-1">Foto de Perfil Principal</h4>
                                <p class="text-xs text-slate-500 mb-3">Esta foto será la que se muestre en tamaño grande en la columna derecha de tu TPV pública.</p>
                                
                                <label class="inline-flex items-center gap-2 bg-slate-950 border border-slate-800 hover:border-purple-500/40 text-purple-400 hover:text-purple-300 px-4 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer shadow-md select-none">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Seleccionar Foto de Perfil
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onPhotoChange"
                                        class="hidden"
                                    />
                                </label>
                                <span v-if="form.errors.profile_photo" class="text-xs text-red-400 mt-2 block">{{ form.errors.profile_photo }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Nombre Artístico o Banda</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Ej: Sunset Groove, Carlos Ramírez..."
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                                <span v-if="form.errors.name" class="text-xs text-red-400 mt-1 block">{{ form.errors.name }}</span>
                            </div>

                            <!-- Slug / Unique Link -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Enlace Compartible (Slug Único)</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-850 bg-slate-950 text-slate-500 text-xs sm:text-sm">
                                        kita.app/
                                    </span>
                                    <input
                                        v-model="form.slug"
                                        type="text"
                                        required
                                        @input="onSlugInput"
                                        placeholder="juan-perez-guitarrista"
                                        class="w-full bg-slate-950 border border-slate-850 rounded-r-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                </div>
                                <span v-if="form.errors.slug" class="text-xs text-red-400 mt-1 block">{{ form.errors.slug }}</span>
                                <p class="text-[10px] text-slate-500 mt-1">Este slug definirá tu URL pública. Solo letras, números y guiones.</p>
                            </div>

                            <!-- Zone / Coverage area (Tag Input) -->
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Zonas de Cobertura / Municipios</label>
                                <div class="flex flex-col gap-3">
                                    <input
                                        v-model="tagInput"
                                        type="text"
                                        @keydown.enter.prevent="addZoneTag"
                                        @keydown.comma.prevent="addZoneTag"
                                        @blur="addZoneTag"
                                        placeholder="Escribe una zona (ej: Puerto Vallarta, Jalisco) y presiona Enter o coma..."
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                    
                                    <!-- Rendered tags -->
                                    <div class="flex flex-wrap gap-2 min-h-[32px] items-center">
                                        <span v-if="form.coverage_area.length === 0" class="text-xs text-slate-500 italic">No se han agregado zonas de cobertura aún.</span>
                                        <span
                                            v-for="zone in form.coverage_area"
                                            :key="zone"
                                            class="inline-flex items-center gap-1.5 bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 px-3 py-1 rounded-full text-xs font-semibold select-none shadow-sm hover:bg-cyan-500/20 transition-all"
                                        >
                                            {{ zone }}
                                            <button
                                                type="button"
                                                @click="removeZoneTag(zone)"
                                                class="text-cyan-400/60 hover:text-cyan-400 focus:outline-none text-xs font-black p-0.5 rounded-full hover:bg-cyan-500/20 shrink-0 cursor-pointer"
                                            >
                                                &times;
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <span v-if="form.errors.coverage_area" class="text-xs text-red-400 mt-1 block">{{ form.errors.coverage_area }}</span>
                            </div>

                            <!-- View Live Link CTA -->
                            <div class="md:col-span-2 flex justify-end pb-1.5" v-if="props.profile.slug">
                                <a
                                    :href="'/' + form.slug"
                                    target="_blank"
                                    class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5 transition-colors duration-200"
                                >
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ver Mi TPV Pública en Vivo
                                </a>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mt-6">
                            <label class="text-xs font-semibold text-slate-400 block mb-1">Biografía Profesional</label>
                            <textarea
                                v-model="form.bio"
                                rows="4"
                                placeholder="Escribe tu trayectoria, estilos musicales y lo que ofreces en tus shows..."
                                class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                            ></textarea>
                            <span v-if="form.errors.bio" class="text-xs text-red-400 mt-1 block">{{ form.errors.bio }}</span>
                        </div>
                    </div>

                    <!-- 2. Instruments / Categories Selection -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            Instrumento o Tipo de Show
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">Selecciona los roles o instrumentos principales que desempeñas en tu perfil artístico.</p>

                        <div class="flex flex-wrap gap-2.5">
                            <button
                                v-for="inst in commonInstruments"
                                :key="inst"
                                type="button"
                                @click="toggleInstrument(inst)"
                                :class="[
                                    'px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-300 border cursor-pointer select-none',
                                    form.instruments.includes(inst)
                                        ? 'bg-cyan-500 text-slate-950 border-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.3)] font-bold scale-[1.02]'
                                        : 'bg-slate-950 text-slate-400 border-slate-850 hover:border-slate-700 hover:text-slate-200'
                                ]"
                            >
                                {{ inst }}
                            </button>
                        </div>
                        <span v-if="form.errors.instruments" class="text-xs text-red-400 mt-2 block">{{ form.errors.instruments }}</span>
                    </div>

                    <!-- 3. Contact & Social Networks Section -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Botón de Contacto y Redes Sociales (Widgets)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- WhatsApp Phone -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">WhatsApp para Contratos (Con Clave de País)</label>
                                <input
                                    v-model="form.widget_status.whatsapp"
                                    type="text"
                                    placeholder="Ej: 523221234567"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                                <p class="text-[9px] text-slate-500 mt-1">Inserta tu número con lada internacional, sin espacios ni guiones (Ej: 52 para México).</p>
                            </div>

                            <!-- Instagram Link -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Enlace de Instagram</label>
                                <input
                                    v-model="form.widget_status.instagram"
                                    type="url"
                                    placeholder="https://instagram.com/miusuario"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                            </div>

                            <!-- Spotify Link -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Enlace de Spotify</label>
                                <input
                                    v-model="form.widget_status.spotify"
                                    type="url"
                                    placeholder="https://open.spotify.com/artist/..."
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                            </div>

                            <!-- Facebook Link -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Enlace de Facebook</label>
                                <input
                                    v-model="form.widget_status.facebook"
                                    type="url"
                                    placeholder="https://facebook.com/miusuario"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                            </div>

                            <!-- YouTube Link -->
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Enlace de YouTube (Canal/Video)</label>
                                <input
                                    v-model="form.widget_status.youtube"
                                    type="url"
                                    placeholder="https://youtube.com/@miusuario"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 4. Widgets Visibility Toggles -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Visibilidad de Widgets en la TPV
                        </h3>
                        <p class="text-xs text-slate-500 mb-6">Elige qué secciones de tu Tarjeta de Presentación Virtual deseas mostrar u ocultar públicamente.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Toggle Gallery Widget -->
                            <div class="flex items-center justify-between p-4 bg-slate-950/40 border border-slate-850 rounded-2xl">
                                <div>
                                    <h4 class="text-sm font-bold text-white mb-0.5">Widget Multimedia (Galería)</h4>
                                    <p class="text-[11px] text-slate-500">Muestra tus fotos promocionales y vídeos incrustados en la TPV pública.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input
                                        v-model="form.widget_status.media"
                                        type="checkbox"
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500 peer-checked:after:bg-slate-950 peer-checked:after:border-cyan-400"></div>
                                </label>
                            </div>

                            <!-- Toggle Gigs Widget -->
                            <div class="flex items-center justify-between p-4 bg-slate-950/40 border border-slate-850 rounded-2xl">
                                <div>
                                    <h4 class="text-sm font-bold text-white mb-0.5">Widget de Agenda (Shows)</h4>
                                    <p class="text-[11px] text-slate-500">Muestra tus próximas fechas confirmadas en el formato de entradas de conciertos.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input
                                        v-model="form.widget_status.agenda"
                                        type="checkbox"
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500 peer-checked:after:bg-slate-950 peer-checked:after:border-cyan-400"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black py-3.5 rounded-2xl text-sm transition-all duration-300 shadow-[0_0_20px_rgba(6,182,212,0.2)] hover:scale-[1.01] flex items-center justify-center gap-1.5"
                    >
                        <span v-if="form.processing" class="animate-spin h-5 w-5 border-2 border-slate-950 border-t-transparent rounded-full"></span>
                        {{ form.processing ? 'Guardando Cambios...' : 'Guardar Cambios en Mi TPV' }}
                    </button>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
