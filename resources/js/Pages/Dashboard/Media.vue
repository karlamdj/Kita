<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    media: {
        type: Array,
        required: true,
    },
});

// Photo Form State
const fileInput = ref(null);
const previewUrl = ref(null);
const isDragging = ref(false);

const photoForm = useForm({
    type: 'photo',
    file: null,
    title: '',
});

// Video Form State
const videoThumbnailInput = ref(null);

const videoForm = useForm({
    type: 'video',
    url: '',
    title: '',
    thumbnail: null,
});

// Handle File Selection/Previews
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        setPhotoFile(file);
    }
};

// Detect platform from URL (mirror of Show.vue)
const detectPlatform = (url) => {
    if (!url) return 'unknown';
    if (/youtube\.com|youtu\.be/.test(url)) return 'youtube';
    if (/facebook\.com|fb\.watch|fb\.gg|fb\.me/.test(url)) return 'facebook';
    if (/instagram\.com/.test(url)) return 'instagram';
    if (/tiktok\.com/.test(url)) return 'tiktok';
    if (/vimeo\.com/.test(url)) return 'vimeo';
    return 'unknown';
};

const getPlatform = (item) => {
    if (item.type === 'photo') return 'photo';
    const urlPlatform = detectPlatform(item.url);
    if (urlPlatform !== 'unknown') return urlPlatform;
    if (item.type === 'youtube') return 'youtube';
    if (item.type === 'vimeo') return 'vimeo';
    if (item.type === 'facebook') return 'facebook';
    if (item.type === 'instagram') return 'instagram';
    if (item.type === 'tiktok') return 'tiktok';
    return 'unknown';
};

const getPlatformLabel = (item) => {
    const p = getPlatform(item);
    const labels = {
        photo: 'Imagen de Promoción',
        youtube: 'YouTube',
        vimeo: 'Vimeo',
        facebook: 'Facebook Video',
        instagram: 'Instagram Reel',
        tiktok: 'TikTok Video',
    };
    return labels[p] || 'Enlace de Video';
};

// Parse Video ID and generate Embed URL
const getVideoEmbedUrl = (item) => {
    const url = item.url;
    if (!url) return null;
    const platform = getPlatform(item);
    if (platform === 'youtube') {
        const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11
            ? `https://www.youtube.com/embed/${match[2]}`
            : null;
    }
    if (platform === 'tiktok') {
        const match = url.match(/\/video\/(\d+)/);
        return match ? `https://www.tiktok.com/embed/v2/${match[1]}` : null;
    }
    if (platform === 'instagram') {
        const match = url.match(/\/(?:p|reel|reels|share\/reel|tv)\/([A-Za-z0-9_-]+)/i);
        return match ? `https://www.instagram.com/p/${match[1]}/embed` : null;
    }
    if (platform === 'facebook') {
        let cleanUrl = url.trim();

        // 1. Replace mobile subdomain m.facebook.com -> www.facebook.com
        cleanUrl = cleanUrl.replace(/^https?:\/\/m\.facebook\.com/i, 'https://www.facebook.com');

        // 2. Handle fb.watch shortlinks (e.g. https://fb.watch/abcdef123/)
        if (/fb\.watch/i.test(cleanUrl)) {
            const match = cleanUrl.match(/fb\.watch\/(?:v\/)?([A-Za-z0-9_-]+)/i);
            if (match) {
                cleanUrl = `https://www.facebook.com/watch/?v=${match[1]}`;
            }
        }

        // 3. Handle fb.gg shortlinks
        if (/fb\.gg/i.test(cleanUrl)) {
            const match = cleanUrl.match(/fb\.gg\/(?:v\/)?([A-Za-z0-9_-]+)/i);
            if (match) {
                cleanUrl = `https://www.facebook.com/watch/?v=${match[1]}`;
            }
        }

        try {
            const urlObj = new URL(cleanUrl);

            // 4. Handle /share/r/, /share/v/, /share/p/ mobile app share URLs
            if (urlObj.pathname.includes('/share/')) {
                const shareMatch = urlObj.pathname.match(/\/share\/(r|v|p)\/([A-Za-z0-9_-]+)/i);
                if (shareMatch) {
                    const type = shareMatch[1].toLowerCase();
                    const id = shareMatch[2];
                    if (type === 'r') {
                        cleanUrl = `https://www.facebook.com/reel/${id}/`;
                    } else {
                        cleanUrl = `https://www.facebook.com/share/${type}/${id}/`;
                    }
                }
            }
            // 5. Handle standard /watch URLs
            else if (urlObj.pathname.includes('/watch')) {
                const v = urlObj.searchParams.get('v');
                if (v) {
                    cleanUrl = `https://www.facebook.com/watch/?v=${v}`;
                }
            }
            // 6. Handle standard /reel/ or /reels/ URLs
            else if (urlObj.pathname.includes('/reel/') || urlObj.pathname.includes('/reels/')) {
                const match = urlObj.pathname.match(/\/(?:reel|reels)\/([A-Za-z0-9_-]+)/i);
                if (match) {
                    cleanUrl = `https://www.facebook.com/reel/${match[1]}/`;
                }
            }
            // 7. Handle /videos/ URLs (e.g. /page/videos/123456)
            else if (urlObj.pathname.includes('/videos/')) {
                const match = urlObj.pathname.match(/\/videos\/(\d+)/i);
                if (match) {
                    cleanUrl = `https://www.facebook.com/watch/?v=${match[1]}`;
                }
            } else {
                urlObj.search = '';
                cleanUrl = urlObj.toString();
            }
        } catch (e) {
            // Use raw url if parsing fails
        }

        return `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(cleanUrl)}&show_text=false&width=325`;
    }
    return null;
};

const getYouTubeId = (url) => {
    if (!url) return null;
    const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return match && match[2].length === 11 ? match[2] : null;
};

const getYouTubeThumbnail = (url) => {
    const id = getYouTubeId(url);
    return id ? `https://img.youtube.com/vi/${id}/hqdefault.jpg` : null;
};

const getThumbnail = (item) => {
    if (item.type === 'photo') {
        return item.path ? (item.path.startsWith('http') ? item.path : '/' + item.path) : null;
    }
    if (item.path) {
        return item.path.startsWith('http') ? item.path : '/' + item.path;
    }
    const platform = getPlatform(item);
    if (platform === 'youtube') return getYouTubeThumbnail(item.url);
    return null;
};

const setPhotoFile = (file) => {
    const isImage = file.type.startsWith('image/');
    if (!isImage) {
        alert('Por favor selecciona un archivo de imagen válido (JPG, PNG, GIF, WEBP).');
        return;
    }
    photoForm.file = file;
    previewUrl.value = URL.createObjectURL(file);
};

// Drag & Drop Handlers
const onDragOver = (e) => {
    e.preventDefault();
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    if (file) {
        setPhotoFile(file);
    }
};

const triggerFileSelect = () => {
    fileInput.value.click();
};

// Form Submissions
const submitPhoto = () => {
    photoForm.post('/dashboard/medios', {
        preserveScroll: true,
        onSuccess: () => {
            photoForm.reset();
            previewUrl.value = null;
            if (fileInput.value) fileInput.value.value = null;
        },
    });
};

const submitVideo = () => {
    videoForm.post('/dashboard/medios', {
        preserveScroll: true,
        onSuccess: () => {
            videoForm.reset();
            if (videoThumbnailInput.value) videoThumbnailInput.value.value = null;
        },
    });
};

// Deletion Handler
const deleteMedia = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este medio de tu galería?')) {
        router.delete(`/dashboard/medios/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Almacén de Medios - KITA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-black leading-tight text-slate-900 dark:text-white">
                Almacén de Medios
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Sube tus fotos promocionales y conecta tus videos de YouTube, Facebook, Instagram o TikTok.
            </p>
        </template>

        <div class="py-8 bg-slate-100/60 dark:bg-slate-950 min-h-[calc(100vh-4rem)] text-slate-900 dark:text-slate-100 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Upload Panel (Side by Side Grid) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    
                    <!-- Photos Upload Card -->
                    <div class="bg-white dark:bg-slate-900/80 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-xs hover:shadow-md dark:shadow-xl backdrop-blur-sm transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between mb-5 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                    <div class="p-2.5 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    Cargar Fotos
                                </h3>
                                <span class="text-[10px] font-black tracking-widest text-cyan-600 dark:text-cyan-400 uppercase px-2.5 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20">Imagen</span>
                            </div>

                            <!-- Drag and Drop Box -->
                            <div
                                @dragover="onDragOver"
                                @dragleave="onDragLeave"
                                @drop="onDrop"
                                :class="[
                                    'group border-2 border-dashed rounded-2xl p-7 text-center cursor-pointer transition-all duration-300 flex flex-col items-center justify-center min-h-[190px]',
                                    isDragging 
                                        ? 'border-cyan-500 bg-cyan-500/10 scale-[0.99]' 
                                        : 'border-slate-300 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-950/40 hover:bg-cyan-500/5 hover:border-cyan-500/60 dark:hover:border-cyan-500/60'
                                ]"
                                @click="triggerFileSelect"
                            >
                                <input
                                    ref="fileInput"
                                    type="file"
                                    class="hidden"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    @change="handleFileChange"
                                />

                                <!-- Preview or Placeholder -->
                                <template v-if="previewUrl">
                                    <img
                                        v-if="photoForm.file && photoForm.file.type.startsWith('image/')"
                                        :src="previewUrl"
                                        class="max-h-[140px] rounded-xl shadow-md mb-2 object-cover border border-slate-200 dark:border-slate-800"
                                    />
                                    <video
                                        v-else
                                        :src="previewUrl"
                                        class="max-h-[140px] rounded-xl shadow-md mb-2 border border-slate-200 dark:border-slate-800"
                                        controls
                                        muted
                                    ></video>
                                    <p class="text-xs text-cyan-600 dark:text-cyan-400 font-bold">Archivo listo para subir. Haz clic para cambiar.</p>
                                </template>
                                <template v-else>
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-center mb-3 text-cyan-600 dark:text-cyan-400 group-hover:scale-110 transition-transform">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
                                        Arrastra tu foto aquí o <span class="text-cyan-600 dark:text-cyan-400 underline underline-offset-2">haz clic para buscar</span>
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Soporta JPG, PNG, GIF, WEBP hasta 20MB</p>
                                </template>
                            </div>

                            <!-- Title Input -->
                            <div class="mt-4">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1.5">Título del Archivo (Opcional)</label>
                                <input
                                    v-model="photoForm.title"
                                    type="text"
                                    placeholder="Ej: En vivo en Malecón, Video Oficial 2026..."
                                    class="w-full bg-slate-50/70 hover:bg-white focus:bg-white dark:bg-slate-950 border border-slate-300/80 dark:border-slate-800 focus:border-cyan-500 dark:focus:border-cyan-400 rounded-xl py-2.5 px-3.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-xs"
                                />
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <form @submit.prevent="submitPhoto" class="mt-4">
                            <button
                                type="submit"
                                :disabled="!photoForm.file || photoForm.processing"
                                class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-extrabold py-3 px-6 rounded-xl text-sm transition-all duration-300 shadow-md shadow-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/30 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:shadow-none cursor-pointer"
                            >
                                <svg v-if="!photoForm.processing" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span v-else class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ photoForm.processing ? 'Subiendo...' : 'Subir Archivo' }}
                            </button>
                        </form>
                    </div>

                    <!-- Video Upload Card -->
                    <div class="bg-white dark:bg-slate-900/80 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-xs hover:shadow-md dark:shadow-xl backdrop-blur-sm transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between mb-5 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                    <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    Conectar Enlace de Video
                                </h3>
                                <span class="text-[10px] font-black tracking-widest text-blue-600 dark:text-blue-400 uppercase px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20">Video</span>
                            </div>

                            <form @submit.prevent="submitVideo" class="flex flex-col gap-4">
                                <div>
                                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1.5">Título del Video (Opcional)</label>
                                    <input
                                        v-model="videoForm.title"
                                        type="text"
                                        placeholder="Ej: Solo de guitarra, Cover Acústico..."
                                        class="w-full bg-slate-50/70 hover:bg-white focus:bg-white dark:bg-slate-950 border border-slate-300/80 dark:border-slate-800 focus:border-cyan-500 dark:focus:border-cyan-400 rounded-xl py-2.5 px-3.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-xs"
                                    />
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1.5">URL del Video Social</label>
                                    <input
                                        v-model="videoForm.url"
                                        type="url"
                                        required
                                        placeholder="https://www.youtube.com/watch?v=... o Facebook, Instagram, TikTok"
                                        class="w-full bg-slate-50/70 hover:bg-white focus:bg-white dark:bg-slate-950 border border-slate-300/80 dark:border-slate-800 focus:border-cyan-500 dark:focus:border-cyan-400 rounded-xl py-2.5 px-3.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-xs"
                                    />
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Acepta enlaces públicos de YouTube • Facebook • Instagram • TikTok.</p>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1.5">Sobrescribir Miniatura (Opcional)</label>
                                    <input
                                        ref="videoThumbnailInput"
                                        type="file"
                                        accept="image/jpeg,image/png,image/gif,image/webp"
                                        @change="e => videoForm.thumbnail = e.target.files[0]"
                                        class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3.5 file:py-2 file:px-4 file:rounded-xl file:border file:border-cyan-500/20 file:text-xs file:font-bold file:bg-cyan-500/10 hover:file:bg-cyan-500/20 file:text-cyan-700 dark:file:text-cyan-300 cursor-pointer transition-colors"
                                    />
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Las miniaturas de YouTube, TikTok, Facebook e Instagram se generan automáticamente. Sube una imagen solo si deseas cambiarla por otra.</p>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="!videoForm.url || videoForm.processing"
                                    class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-extrabold py-3 px-6 rounded-xl text-sm transition-all duration-300 shadow-md shadow-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/30 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:shadow-none cursor-pointer"
                                >
                                    <svg v-if="!videoForm.processing" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span v-else class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                    Guardar Enlace de Video
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Media Gallery Section -->
                <div>
                    <!-- Section Header -->
                    <div class="flex items-center justify-between mb-6 border-b border-slate-200/80 dark:border-slate-800 pb-4">
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                            <div class="p-2.5 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            Tu Galería de Medios
                        </h3>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800/80 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700 shadow-xs">
                            {{ media.length }} {{ media.length === 1 ? 'elemento' : 'elementos' }}
                        </span>
                    </div>

                    <!-- Empty Gallery State -->
                    <div v-if="media.length === 0" class="text-center py-16 bg-white dark:bg-slate-900/40 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mx-auto mb-3">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">Tu galería está vacía</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs max-w-sm mx-auto">
                            Comienza subiendo tus fotos oficiales o enlazando videos promocionales de tus espectáculos.
                        </p>
                    </div>

                    <!-- Cards Grid -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <article
                            v-for="item in media"
                            :key="item.id"
                            class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden hover:border-cyan-500/50 hover:shadow-lg dark:hover:border-cyan-400/50 transition-all duration-300 flex flex-col justify-between group shadow-xs dark:shadow-md"
                        >
                            <!-- Media Visual Content -->
                            <div class="relative bg-slate-100 dark:bg-slate-950 aspect-video flex items-center justify-center overflow-hidden">
                                
                                <!-- YouTube: static image card -->
                                <template v-if="getPlatform(item) === 'youtube'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="YouTube"
                                    />
                                    <div v-else class="text-slate-500 text-xs text-center px-4">
                                        <svg class="h-10 w-10 text-red-500/70 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.517 0-9.388.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.507 9.388.507 9.388.507s7.517 0 9.388-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Enlace de YouTube guardado
                                    </div>
                                    <!-- Play overlay -->
                                    <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center pointer-events-none">
                                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                                            <svg class="h-5 w-5 fill-white translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <!-- Platform badge -->
                                    <div class="absolute top-2 right-2 bg-red-600/90 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">YT</div>
                                </template>

                                <!-- Facebook / Instagram / TikTok: external link card -->
                                <template v-else-if="getPlatform(item) === 'facebook' || getPlatform(item) === 'instagram' || getPlatform(item) === 'tiktok'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        :alt="item.title"
                                    />
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-2 bg-slate-100 dark:bg-slate-950 p-4">
                                        <!-- FB icon -->
                                        <svg v-if="getPlatform(item) === 'facebook'" class="h-8 w-8 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        <!-- IG icon -->
                                        <svg v-else-if="getPlatform(item) === 'instagram'" class="h-8 w-8" viewBox="0 0 24 24" fill="none"><defs><linearGradient id="ig-db" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#f09433"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs><circle cx="12" cy="12" r="3.5" stroke="white" stroke-width="1.5" fill="none"/><circle cx="16.5" cy="7.5" r="1" fill="white"/><rect x="3" y="3" width="18" height="18" rx="5" stroke="white" stroke-width="1.5" fill="none"/></svg>
                                        <!-- TT icon -->
                                        <svg v-else class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-1.02-.1z" fill="#69C9D0"/><path d="M15.82 2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-3.79-4.79z" fill="#EE1D52"/></svg>
                                        <a :href="item.url" target="_blank" class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-semibold underline text-center px-2 truncate w-full">Abrir enlace</a>
                                    </div>
                                    <!-- Play Overlay for when there is a thumbnail -->
                                    <template v-if="getThumbnail(item)">
                                        <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center pointer-events-none">
                                            <div 
                                                :class="[
                                                    'w-10 h-10 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300',
                                                    getPlatform(item) === 'facebook' ? 'bg-[#1877F2]/80' : 
                                                    getPlatform(item) === 'instagram' ? 'bg-gradient-to-tr from-orange-500 to-pink-600' : 
                                                    'bg-slate-800 border border-[#EE1D52]/50'
                                                ]"
                                            >
                                                <svg class="h-5 w-5 fill-white translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </div>
                                        <!-- Platform badge -->
                                        <div 
                                            :class="[
                                                'absolute top-2 right-2 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider',
                                                getPlatform(item) === 'facebook' ? 'bg-[#1877F2]/90' : 
                                                getPlatform(item) === 'instagram' ? 'bg-pink-600/90' : 
                                                'bg-slate-900/90 text-[#EE1D52] border border-[#EE1D52]/50'
                                            ]"
                                        >
                                            {{ getPlatform(item) === 'facebook' ? 'FB' : getPlatform(item) === 'instagram' ? 'IG' : 'TT' }}
                                        </div>
                                    </template>
                                </template>

                                <!-- Photo -->
                                <template v-else>
                                    <img
                                        :src="getThumbnail(item)"
                                        :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </template>
                            </div>

                            <!-- Details and Actions -->
                            <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ item.title }}</h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold mt-0.5">
                                        {{ getPlatformLabel(item) }}
                                    </p>
                                </div>

                                <!-- Trash Delete Icon Button -->
                                <button
                                    @click="deleteMedia(item.id)"
                                    class="p-2 rounded-xl bg-slate-100/80 dark:bg-slate-950 text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 border border-slate-200/80 dark:border-slate-800 hover:border-red-200 dark:hover:border-red-900/40 transition-all duration-300 shrink-0 cursor-pointer"
                                    title="Eliminar elemento"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </article>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom tweaks */
</style>
