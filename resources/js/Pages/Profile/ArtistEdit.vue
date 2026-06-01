<script setup>
import { ref, computed, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import html2canvas from 'html2canvas';

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
    theme: props.profile.theme || 'kita-neon',
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

// QR Modal state
const showQrModal = ref(false);
const isDownloading = ref(false);

// Computed public TPV URL for QR code
const tpvPublicUrl = computed(() => {
    if (props.profile.slug) {
        return window.location.origin + '/' + props.profile.slug;
    }
    return window.location.origin;
});

// Format phone for display (keeps only digits then formats)
const displayPhone = computed(() => {
    const raw = form.widget_status.whatsapp || '';
    const digits = raw.replace(/\D/g, '');
    if (!digits) return null;
    // Show as +52 322 123 4567 style if length is 12 (Mexico)
    if (digits.length === 12) {
        return `+${digits.slice(0, 2)} ${digits.slice(2, 5)} ${digits.slice(5, 8)} ${digits.slice(8)}`;
    }
    return `+${digits}`;
});

// Download card as PNG using html2canvas
const downloadCardPng = async () => {
    isDownloading.value = true;
    await nextTick();
    try {
        const el = document.getElementById('mini-tarjeta-qr');
        if (!el) return;
        const canvas = await html2canvas(el, {
            backgroundColor: '#0f172a',
            useCORS: true,
            scale: 2,
            logging: false,
        });
        const link = document.createElement('a');
        link.download = `mi-tarjeta-kita-${form.slug || 'artista'}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (e) {
        console.error('Error generando PNG:', e);
    } finally {
        isDownloading.value = false;
    }
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

                            <!-- QR / Tarjeta Digital CTA -->
                            <div class="md:col-span-2 flex justify-end pb-1.5" v-if="props.profile.slug">
                                <button
                                    type="button"
                                    @click="showQrModal = true"
                                    class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5 transition-all duration-200 cursor-pointer bg-transparent border-none p-0 group"
                                >
                                    <!-- QR code SVG icon -->
                                    <svg class="h-4 w-4 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                                        <rect x="5" y="5" width="3" height="3" fill="currentColor" stroke="none"/>
                                        <rect x="16" y="5" width="3" height="3" fill="currentColor" stroke="none"/>
                                        <rect x="5" y="16" width="3" height="3" fill="currentColor" stroke="none"/>
                                        <path d="M14 14h2v2h-2zM18 14h3v3h-3zM14 18h3v3h-3zM19 21h2"/>
                                    </svg>
                                    Ver Mi QR / Tarjeta
                                </button>
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

                    <!-- 5. Color Theme Picker -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            Personaliza el Color de tu TPV
                        </h3>
                        <p class="text-xs text-slate-500 mb-6">Elige la paleta de color que representa mejor tu estilo artístico. El cambio se aplicará en tu TPV pública.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            <!-- Tema: KITA Neón -->
                            <button
                                type="button"
                                @click="form.theme = 'kita-neon'"
                                :class="[
                                    'relative flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer group',
                                    form.theme === 'kita-neon'
                                        ? 'border-cyan-400 bg-cyan-500/10 shadow-[0_0_20px_rgba(34,211,238,0.2)]'
                                        : 'border-slate-800 bg-slate-950/40 hover:border-slate-700 hover:bg-slate-900/60'
                                ]"
                            >
                                <!-- Color swatch -->
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-300 to-cyan-600 shadow-[0_0_20px_rgba(34,211,238,0.5)] group-hover:scale-110 transition-transform duration-300"></div>
                                    <div v-if="form.theme === 'kita-neon'" class="absolute -top-1 -right-1 w-5 h-5 bg-cyan-400 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="h-3 w-3 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-bold" :class="form.theme === 'kita-neon' ? 'text-cyan-300' : 'text-slate-300'">KITA Neón</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Predeterminado</p>
                                </div>
                                <!-- Mini preview strip -->
                                <div class="flex gap-1 mt-1">
                                    <span class="w-6 h-1.5 rounded-full bg-cyan-400"></span>
                                    <span class="w-4 h-1.5 rounded-full bg-cyan-600/60"></span>
                                    <span class="w-3 h-1.5 rounded-full bg-cyan-800/40"></span>
                                </div>
                            </button>

                            <!-- Tema: Cyber Púrpura -->
                            <button
                                type="button"
                                @click="form.theme = 'cyber-purple'"
                                :class="[
                                    'relative flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer group',
                                    form.theme === 'cyber-purple'
                                        ? 'border-purple-500 bg-purple-500/10 shadow-[0_0_20px_rgba(168,85,247,0.2)]'
                                        : 'border-slate-800 bg-slate-950/40 hover:border-slate-700 hover:bg-slate-900/60'
                                ]"
                            >
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-700 shadow-[0_0_20px_rgba(168,85,247,0.5)] group-hover:scale-110 transition-transform duration-300"></div>
                                    <div v-if="form.theme === 'cyber-purple'" class="absolute -top-1 -right-1 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-bold" :class="form.theme === 'cyber-purple' ? 'text-purple-300' : 'text-slate-300'">Cyber Púrpura</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Elegante &amp; Místico</p>
                                </div>
                                <div class="flex gap-1 mt-1">
                                    <span class="w-6 h-1.5 rounded-full bg-purple-500"></span>
                                    <span class="w-4 h-1.5 rounded-full bg-purple-700/60"></span>
                                    <span class="w-3 h-1.5 rounded-full bg-purple-900/40"></span>
                                </div>
                            </button>

                            <!-- Tema: Volt Naranja -->
                            <button
                                type="button"
                                @click="form.theme = 'volt-orange'"
                                :class="[
                                    'relative flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all duration-300 cursor-pointer group',
                                    form.theme === 'volt-orange'
                                        ? 'border-orange-500 bg-orange-500/10 shadow-[0_0_20px_rgba(249,115,22,0.2)]'
                                        : 'border-slate-800 bg-slate-950/40 hover:border-slate-700 hover:bg-slate-900/60'
                                ]"
                            >
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-300 to-orange-600 shadow-[0_0_20px_rgba(249,115,22,0.5)] group-hover:scale-110 transition-transform duration-300"></div>
                                    <div v-if="form.theme === 'volt-orange'" class="absolute -top-1 -right-1 w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-bold" :class="form.theme === 'volt-orange' ? 'text-orange-300' : 'text-slate-300'">Volt Naranja</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Energía &amp; Fuego</p>
                                </div>
                                <div class="flex gap-1 mt-1">
                                    <span class="w-6 h-1.5 rounded-full bg-orange-500"></span>
                                    <span class="w-4 h-1.5 rounded-full bg-orange-700/60"></span>
                                    <span class="w-3 h-1.5 rounded-full bg-orange-900/40"></span>
                                </div>
                            </button>

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

        <!-- ====== QR / Digital Business Card Modal ====== -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="showQrModal"
                    class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                >
                    <!-- Backdrop -->
                    <div
                        class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                        @click="showQrModal = false"
                    ></div>

                    <!-- Modal Card -->
                    <div class="relative z-10 w-full max-w-sm">
                        <!-- Close button -->
                        <button
                            type="button"
                            @click="showQrModal = false"
                            class="absolute -top-4 -right-4 z-20 w-9 h-9 rounded-full bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-xl text-lg font-bold"
                        >
                            ✕
                        </button>

                        <!-- Tarjeta de presentación digital -->
                        <div class="bg-gradient-to-b from-slate-900 to-slate-950 border border-white/10 rounded-3xl overflow-hidden shadow-[0_0_80px_rgba(168,85,247,0.3)] relative">

                            <!-- Ambient glows -->
                            <div class="absolute top-0 right-0 w-48 h-48 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-40 h-40 bg-pink-600/15 rounded-full blur-3xl pointer-events-none"></div>

                            <!-- Top gradient strip -->
                            <div class="h-1 w-full bg-gradient-to-r from-purple-500 via-pink-500 to-purple-600"></div>

                            <!-- Card body (printable area) -->
                            <div id="mini-tarjeta-qr" class="p-7 flex flex-col items-center gap-5 relative z-10">

                                <!-- Profile photo / initials avatar -->
                                <div class="relative">
                                    <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-purple-500/30 shadow-[0_0_25px_rgba(168,85,247,0.35)] shrink-0 bg-slate-900 flex items-center justify-center">
                                        <img
                                            v-if="photoPreviewUrl"
                                            :src="photoPreviewUrl"
                                            alt="Foto de Perfil"
                                            class="w-full h-full object-cover"
                                        />
                                        <span v-else class="text-purple-400 font-black text-2xl uppercase">
                                            {{ getInitials(form.name || 'KI') }}
                                        </span>
                                    </div>
                                    <!-- KITA badge -->
                                    <span class="absolute -bottom-2 -right-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg uppercase tracking-wider">KITA</span>
                                </div>

                                <!-- Name & instruments -->
                                <div class="text-center">
                                    <h2 class="text-white font-black text-xl tracking-wide leading-tight">
                                        {{ form.name || 'Tu Nombre Artístico' }}
                                    </h2>
                                    <p class="text-purple-400 text-xs font-semibold mt-1 tracking-wider">
                                        {{ form.instruments.length > 0 ? form.instruments.join(' · ') : 'Artista Musical' }}
                                    </p>
                                    <p v-if="form.coverage_area.length > 0" class="text-slate-500 text-[10px] mt-0.5">
                                        📍 {{ form.coverage_area.slice(0, 2).join(', ') }}
                                    </p>
                                    <!-- Phone number -->
                                    <div v-if="displayPhone" class="flex items-center justify-center gap-1.5 mt-1.5">
                                        <svg class="h-3 w-3 text-green-400/70 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.588 2.001 14.156.98 11.602.98c-5.439 0-9.867 4.37-9.871 9.803-.001 1.73.461 3.42 1.336 4.908l-.994 3.633 3.734-.972zm12.355-6.852c-.3-.15-1.77-.875-2.046-.975-.276-.1-.477-.15-.677.15-.2.3-.777.975-.951 1.174-.174.2-.35.225-.65.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.487-1.777-1.663-2.074-.176-.3-.019-.461.13-.611.135-.135.3-.35.45-.525.15-.175.2-.3.3-.5s.05-.375-.025-.525-.675-1.625-.925-2.225c-.244-.589-.491-.51-.677-.52l-.576-.007c-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.112 4.525.714.309 1.272.494 1.707.632.715.227 1.365.195 1.88.117.574-.088 1.77-.725 2.02-1.425.25-.7.25-1.3.175-1.425-.076-.125-.276-.2-.576-.35z"/>
                                        </svg>
                                        <span class="text-slate-400 text-[10px] font-mono tracking-wide">{{ displayPhone }}</span>
                                    </div>
                                </div>

                                <!-- Divider line -->
                                <div class="w-full h-px bg-white/5"></div>

                                <!-- QR Code block -->
                                <div class="flex flex-col items-center gap-3">
                                    <div class="bg-white p-3 rounded-2xl shadow-[0_0_30px_rgba(168,85,247,0.25)]">
                                        <QrcodeVue
                                            :value="tpvPublicUrl"
                                            :size="160"
                                            level="M"
                                            render-as="canvas"
                                        />
                                    </div>
                                    <p class="text-slate-500 text-[10px] font-semibold tracking-wider text-center">
                                        Escanea para ver mi TPV completa
                                    </p>
                                </div>

                                <!-- ★ Brand signature — last element inside printable area ★ -->
                                <p class="text-[9px] font-light text-purple-500/40 tracking-[0.3em] uppercase text-center mt-1 select-none">
                                    Powered by <span class="font-semibold text-purple-400/60">KITA</span>
                                </p>

                            </div><!-- /#mini-tarjeta-qr ends here — PNG boundary -->

                            <!-- URL chip — visible in modal but excluded from PNG capture -->
                            <div class="px-7 pb-3 -mt-1">
                                <div class="w-full bg-slate-950/60 border border-slate-800/60 rounded-xl px-3 py-2 flex items-center gap-2">
                                    <svg class="h-3 w-3 text-cyan-400/70 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span class="text-cyan-400/70 text-[10px] font-mono truncate select-all">{{ tpvPublicUrl }}</span>
                                </div>
                            </div>

                            <!-- Action buttons row (outside printable area) -->
                            <div class="w-full flex gap-2 px-7 pb-7 pt-2">
                                <a
                                    :href="tpvPublicUrl"
                                    target="_blank"
                                    class="flex-1 text-center py-2.5 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-purple-500/40 text-slate-300 hover:text-white text-xs font-bold transition-all duration-200"
                                >
                                    🔗 Ver TPV
                                </a>
                                <!-- Download PNG button -->
                                <button
                                    type="button"
                                    @click="downloadCardPng"
                                    :disabled="isDownloading"
                                    class="flex-1 py-2.5 rounded-xl bg-slate-800/60 border border-slate-700 hover:border-cyan-500/50 hover:bg-cyan-500/10 text-cyan-400 hover:text-cyan-300 text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    <svg v-if="!isDownloading" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span v-if="isDownloading" class="w-3.5 h-3.5 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin shrink-0"></span>
                                    {{ isDownloading ? 'Generando...' : 'Guardar PNG' }}
                                </button>
                                <button
                                    type="button"
                                    @click="showQrModal = false"
                                    class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white text-xs font-black transition-all duration-200 shadow-[0_0_15px_rgba(168,85,247,0.3)] cursor-pointer"
                                >
                                    ✓ Cerrar
                                </button>
                            </div>

                            <!-- Bottom gradient strip -->
                            <div class="h-0.5 w-full bg-gradient-to-r from-transparent via-purple-500/30 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AuthenticatedLayout>
</template>
