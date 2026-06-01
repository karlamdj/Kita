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
const videoForm = useForm({
    type: 'video',
    url: '',
    title: '',
});

// Handle File Selection/Previews
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        setPhotoFile(file);
    }
};

const setPhotoFile = (file) => {
    const isImage = file.type.startsWith('image/');
    const isVideo = file.type.startsWith('video/') || ['mp4', 'mov', 'webm'].includes(file.name.split('.').pop().toLowerCase());

    if (!isImage && !isVideo) {
        alert('Por favor selecciona un archivo de imagen o video válido (.mp4, .mov, .webm).');
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

// Parse Video ID and generate Embed URL
const getVideoEmbedUrl = (item) => {
    const url = item.url;
    if (!url) return null;

    if (item.type === 'youtube') {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11
            ? `https://www.youtube.com/embed/${match[2]}`
            : null;
    } else if (item.type === 'vimeo') {
        const regExp = /vimeo\.com\/(?:video\/)?([0-9]+)/;
        const match = url.match(regExp);
        return match
            ? `https://player.vimeo.com/video/${match[1]}`
            : null;
    }
    return null;
};
</script>

<template>
    <Head title="Almacén de Medios - KITA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-white">
                Almacén de Medios
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Sube tus fotos promocionales y conecta tus mejores videos de YouTube o Vimeo.
            </p>
        </template>

        <div class="py-8 bg-slate-950 min-h-[calc(100vh-4rem)] text-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Upload Panel (Side by Side Grid) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    
                    <!-- Photos/Videos Upload Area -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Cargar Fotos o Videos Locales
                            </h3>
                            <span class="text-xs text-slate-500 font-semibold uppercase">Multimedia</span>
                        </div>

                        <!-- Drag and Drop Box -->
                        <div
                            @dragover="onDragOver"
                            @dragleave="onDragLeave"
                            @drop="onDrop"
                            :class="[
                                'border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all duration-300 flex flex-col items-center justify-center min-h-[180px]',
                                isDragging ? 'border-cyan-500 bg-cyan-950/20' : 'border-slate-800 bg-slate-950/50 hover:border-slate-700'
                            ]"
                            @click="triggerFileSelect"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                class="hidden"
                                accept="image/*,video/mp4,video/quicktime,video/webm"
                                @change="handleFileChange"
                            />

                            <!-- Preview or Placeholder -->
                            <template v-if="previewUrl">
                                <img
                                    v-if="photoForm.file && photoForm.file.type.startsWith('image/')"
                                    :src="previewUrl"
                                    class="max-h-[140px] rounded-lg shadow-md mb-2 object-cover border border-slate-800"
                                />
                                <video
                                    v-else
                                    :src="previewUrl"
                                    class="max-h-[140px] rounded-lg shadow-md mb-2 border border-slate-800"
                                    controls
                                    muted
                                ></video>
                                <p class="text-xs text-cyan-400 font-bold">Archivo listo para subir. Haz clic para cambiar.</p>
                            </template>
                            <template v-else>
                                <svg class="h-10 w-10 text-slate-600 mb-2 group-hover:text-slate-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-sm text-slate-300 font-semibold mb-1">Arrastra tu archivo aquí o haz clic para buscar</p>
                                <p class="text-xs text-slate-500">Soporta fotos y videos nativos de hasta 100MB</p>
                            </template>
                        </div>

                        <!-- Title and Upload Button -->
                        <form @submit.prevent="submitPhoto" class="mt-4 flex flex-col gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-400 block mb-1">Título del Archivo (Opcional)</label>
                                <input
                                    v-model="photoForm.title"
                                    type="text"
                                    placeholder="Ej: En vivo en Malecón, Video Oficial 2026..."
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="!photoForm.file || photoForm.processing"
                                class="bg-cyan-500 disabled:bg-slate-800 disabled:text-slate-500 hover:bg-cyan-400 text-slate-950 font-bold py-2.5 rounded-lg text-sm transition-all duration-300 shadow-[0_0_15px_rgba(6,182,212,0.2)] hover:scale-[1.01] flex items-center justify-center gap-1.5"
                            >
                                <span v-if="photoForm.processing" class="animate-spin h-4 w-4 border-2 border-slate-950 border-t-transparent rounded-full"></span>
                                {{ photoForm.processing ? 'Subiendo...' : 'Subir Archivo' }}
                            </button>
                        </form>
                    </div>

                    <!-- Video Upload Area -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Conectar Enlace de Video
                                </h3>
                                <span class="text-xs text-slate-500 font-semibold uppercase">Video</span>
                            </div>

                            <form @submit.prevent="submitVideo" class="flex flex-col gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">Título del Video (Opcional)</label>
                                    <input
                                        v-model="videoForm.title"
                                        type="text"
                                        placeholder="Ej: Solo de guitarra, Cover Acústico..."
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-400 block mb-1">URL de YouTube o Vimeo</label>
                                    <input
                                        v-model="videoForm.url"
                                        type="url"
                                        required
                                        placeholder="https://www.youtube.com/watch?v=..."
                                        class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2.5 px-3 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-950"
                                    />
                                    <p class="text-[10px] text-slate-500 mt-1">Inserta la URL directa del video de tu show para previsualizarlo de forma interactiva.</p>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="!videoForm.url || videoForm.processing"
                                    class="bg-cyan-500 disabled:bg-slate-800 disabled:text-slate-500 hover:bg-cyan-400 text-slate-950 font-bold py-2.5 rounded-lg text-sm transition-all duration-300 shadow-[0_0_15px_rgba(6,182,212,0.2)] hover:scale-[1.01] flex items-center justify-center gap-1.5 mt-2"
                                >
                                    <span v-if="videoForm.processing" class="animate-spin h-4 w-4 border-2 border-slate-950 border-t-transparent rounded-full"></span>
                                    Guardar Enlace de Video
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Media Gallery Section -->
                <div>
                    <h3 class="text-2xl font-black text-white mb-6 border-b border-slate-800 pb-3 flex items-center gap-2">
                        <svg class="h-6 w-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Tu Galería de Medios
                    </h3>

                    <!-- Empty Gallery State -->
                    <div v-if="media.length === 0" class="text-center py-16 bg-slate-900/20 border border-dashed border-slate-800 rounded-2xl p-6">
                        <svg class="h-12 w-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h4 class="text-base font-bold text-white mb-1">Tu galería está vacía</h4>
                        <p class="text-slate-500 text-xs max-w-sm mx-auto">
                            Comienza subiendo tus fotos oficiales o enlazando videos promocionales de tus espectáculos.
                        </p>
                    </div>

                    <!-- Cards Grid -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <article
                            v-for="item in media"
                            :key="item.id"
                            class="bg-slate-900 border border-slate-850 rounded-xl overflow-hidden hover:border-cyan-500/30 transition-all duration-300 flex flex-col justify-between group shadow-lg"
                        >
                            <!-- Media Visual Content -->
                            <div class="relative bg-slate-950 aspect-video flex items-center justify-center overflow-hidden">
                                
                                <!-- Render YouTube/Vimeo Iframe Embed -->
                                <template v-if="item.type === 'youtube' || item.type === 'vimeo'">
                                    <iframe
                                        v-if="getVideoEmbedUrl(item)"
                                        :src="getVideoEmbedUrl(item)"
                                        class="w-full h-full"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                    <div v-else class="text-slate-500 text-xs text-center px-4">
                                        <svg class="h-10 w-10 text-red-500/70 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.517 0-9.388.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.507 9.388.507 9.388.507s7.517 0 9.388-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Enlace de video guardado
                                    </div>
                                </template>

                                <!-- Render Photo or Local Video -->
                                <template v-else>
                                    <video
                                        v-if="item.type === 'video' && item.path"
                                        :src="'/' + item.path"
                                        class="w-full h-full object-cover"
                                        controls
                                    ></video>
                                    <img
                                        v-else
                                        :src="'/' + item.path"
                                        :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </template>
                            </div>

                            <!-- Details and Actions -->
                            <div class="p-4 bg-slate-900 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-white truncate">{{ item.title }}</h4>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-0.5">
                                        {{ item.type === 'photo' ? 'Imagen de promoción' : (item.path ? 'Video Local' : 'Video ' + item.type) }}
                                    </p>
                                </div>

                                <!-- Trash Delete Icon Button -->
                                <button
                                    @click="deleteMedia(item.id)"
                                    class="p-2 rounded-lg bg-slate-950 text-slate-500 hover:text-red-400 hover:bg-red-950/20 border border-slate-850 hover:border-red-900/30 transition-all duration-300 shrink-0"
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
/* Custom inner shadows or tweaks can go here */
</style>
