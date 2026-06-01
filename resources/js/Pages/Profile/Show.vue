<script setup>
import { computed, ref } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    profile: {
        type: Object,
        required: true,
    },
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

// Filter photos, videos and upcoming events
const photos = computed(() => props.profile.media?.filter(item => item.type === 'photo') || []);
const videos = computed(() => props.profile.media?.filter(item => item.type === 'youtube' || item.type === 'vimeo') || []);
const upcomingEvents = computed(() => props.profile.events || []);

// Unified list of all media elements for the infinite responsive grid gallery
const allMedia = computed(() => props.profile.media || []);

// Lightbox state and functions for media preview
const activeLightboxItem = ref(null);
const openLightbox = (item) => {
    activeLightboxItem.value = item;
};
const closeLightbox = () => {
    activeLightboxItem.value = null;
};

// Main Profile Photo for Right Column (desktop) or Top Background (mobile)
const mainPhoto = computed(() => {
    if (props.profile.profile_photo_path) {
        return '/' + props.profile.profile_photo_path;
    }
    return photos.value.length > 0 ? '/' + photos.value[0].path : null;
});

// Split name for high-impact typography
const nameParts = computed(() => {
    const name = props.profile.name || '';
    const parts = name.split(' ');
    if (parts.length > 1) {
        return {
            first: parts[0],
            rest: parts.slice(1).join(' ')
        };
    }
    return {
        first: name,
        rest: ''
    };
});

// Parse Video ID and generate Embed URL
const getVideoEmbedUrl = (item) => {
    const url = item.url;
    if (!url) return null;

    if (item.type === 'youtube') {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11
            ? `https://www.youtube.com/embed/${match[2]}?autoplay=1`
            : null;
    } else if (item.type === 'vimeo') {
        const regExp = /vimeo\.com\/(?:video\/)?([0-9]+)/;
        const match = url.match(regExp);
        return match
            ? `https://player.vimeo.com/video/${match[1]}?autoplay=1`
            : null;
    }
    return null;
};

// Helper to get YouTube Thumbnail URL
const getYouTubeThumbnail = (item) => {
    const url = item.url;
    if (!url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return match && match[2].length === 11
        ? `https://img.youtube.com/vi/${match[2]}/hqdefault.jpg`
        : null;
};

// State to track playing video ID
const activePlayingVideoId = ref(null);
const playVideo = (videoId) => {
    activePlayingVideoId.value = videoId;
};

// Build WhatsApp Link
const getWhatsAppUrl = () => {
    const phone = props.profile.widget_status?.whatsapp || '';
    const formattedPhone = phone.replace(/[^0-9]/g, '');
    if (formattedPhone) {
        return `https://wa.me/${formattedPhone}?text=Hola%20${encodeURIComponent(props.profile.name)},%20te%20contacto%20desde%20tu%20perfil%20de%20KITA.%20Me%20gustaría%20cotizar%20un%20show!`;
    }
    // Default fallback
    return `https://wa.me/523221234567?text=Hola%20${encodeURIComponent(props.profile.name)},%20te%20contacto%20desde%20tu%20perfil%20de%20KITA.%20Me%20gustaría%20cotizar%20un%20show!`;
};

// Format show dates
const formatEventDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-MX', {
        day: '2-digit',
        month: 'short',
    }).toUpperCase();
};

const formatEventTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

// Owner preview state
const { props: pageProps } = usePage();
const authUser = pageProps.auth?.user;
const isOwner = computed(() => {
    return authUser && authUser.id === props.profile.user_id;
});

const goToEdit = () => {
    router.get('/dashboard/tpv/editar');
};

// Mock Music Player state
const isMusicPlaying = ref(false);
const toggleMusicPlay = () => {
    isMusicPlaying.value = !isMusicPlaying.value;
};
</script>

<template>
    <Head :title="`${profile.name} - Tarjeta de Presentación Virtual`" />

    <div class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased overflow-x-hidden relative selection:bg-purple-500 selection:text-white">
        
        <!-- Reusable Global Navbar -->
        <Navbar />

        <!-- Conditional Edit Profile Button for TPV Owner (Placed in the top right corner, below the Navbar) -->
        <div v-if="isOwner" class="absolute top-20 right-4 z-40 sm:right-6 lg:right-8">
            <button
                @click="goToEdit"
                class="bg-slate-900/90 hover:bg-slate-800 text-purple-400 border border-slate-800 hover:border-purple-500/50 hover:shadow-[0_0_15px_rgba(168,85,247,0.3)] px-4 py-2.5 rounded-xl text-xs font-black flex items-center gap-1.5 transition-all duration-300 hover:scale-105 cursor-pointer shadow-md justify-center"
            >
                <span>✏️</span> Editar Perfil
            </button>
        </div>

        <!-- Ambient Decorative Glowing Blobs (Electric Purple & Pink Aesthetics) -->
        <div class="absolute top-[10%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-900/10 blur-[130px] pointer-events-none z-0"></div>
        <div class="absolute bottom-[10%] right-[-10%] w-[70vw] h-[70vw] rounded-full bg-pink-900/10 blur-[140px] pointer-events-none z-0"></div>

        <!-- Stage Lights / Floating stage lights neon (depth & tridimensionality) -->
        <div class="absolute top-10 right-10 w-[450px] h-[450px] bg-purple-600/10 rounded-full blur-[140px] pointer-events-none z-0"></div>
        <div class="absolute bottom-20 left-10 w-[500px] h-[500px] bg-pink-600/10 rounded-full blur-[140px] pointer-events-none z-0"></div>

        <!-- Mobile Profile Photo Hero (Visible only on Mobile) -->
        <div v-if="mainPhoto" class="lg:hidden w-full h-[55vh] relative overflow-hidden z-0">
            <img
                :src="mainPhoto"
                :alt="profile.name"
                class="w-full h-full object-cover object-center"
            />
            <!-- Dark integration gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
        </div>

        <!-- Main Responsive Content Wrapper -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-16 relative z-10 flex flex-col gap-12 lg:gap-16">
            
            <!-- 1. HERO SUPERIOR SECTION (Splitted text left / glowing photo cover right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center min-h-[50vh] lg:min-h-[75vh]">
                
                <!-- HERO LEFT: Floating Name, bio & contact links (No box, no borders) -->
                <div class="lg:col-span-7 flex flex-col gap-6 justify-center">
                    
                    <!-- Instruments & Multi-zones Tags -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            v-for="inst in profile.instruments"
                            :key="inst"
                            class="px-3 py-1 rounded-md bg-pink-500/10 text-pink-300 border border-pink-500/20 text-xs font-bold uppercase tracking-wider shadow-sm"
                        >
                            {{ inst }}
                        </span>
                        
                        <template v-if="Array.isArray(profile.coverage_area)">
                            <span
                                v-for="zone in profile.coverage_area"
                                :key="zone"
                                class="px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-semibold shadow-sm"
                            >
                                📍 {{ zone }}
                            </span>
                        </template>
                        <span
                            v-else-if="profile.coverage_area"
                            class="px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-semibold shadow-sm"
                        >
                            📍 {{ profile.coverage_area }}
                        </span>
                    </div>

                    <!-- Giant typography Name Hierarchy -->
                    <div class="flex flex-col leading-none">
                        <h1 class="text-white font-black text-6xl sm:text-7xl lg:text-8xl tracking-wider uppercase leading-none">
                            {{ nameParts.first }}
                        </h1>
                        <span v-if="nameParts.rest" class="bg-gradient-to-r from-purple-500 to-pink-500 text-transparent bg-clip-text text-6xl sm:text-7xl lg:text-8xl font-black uppercase leading-none mt-2">
                            {{ nameParts.rest }}
                        </span>
                    </div>

                    <!-- Biography Professional bio (No background box) -->
                    <div v-if="profile.bio" class="text-slate-300 text-base md:text-lg leading-relaxed max-w-2xl mt-2">
                        <p class="whitespace-pre-line font-medium">{{ profile.bio }}</p>
                    </div>

                    <!-- Contact & Aligned Socials block (Stacked vertically) -->
                    <div class="flex flex-col items-start gap-4 mt-4">
                        <!-- WhatsApp button with pink glowing shadow -->
                        <a
                            :href="getWhatsAppUrl()"
                            target="_blank"
                            class="w-full sm:w-auto bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-extrabold py-4 px-8 rounded-2xl flex items-center justify-center gap-2.5 transition-all duration-300 shadow-[0_0_20px_rgba(219,39,119,0.4)] hover:shadow-[0_0_30px_rgba(236,72,153,0.6)] hover:scale-[1.03] cursor-pointer text-sm tracking-wider uppercase"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.588 2.001 14.156.98 11.602.98c-5.439 0-9.867 4.37-9.871 9.803-.001 1.73.461 3.42 1.336 4.908l-.994 3.633 3.734-.972zm12.355-6.852c-.3-.15-1.77-.875-2.046-.975-.276-.1-.477-.15-.677.15-.2.3-.777.975-.951 1.174-.174.2-.35.225-.65.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.487-1.777-1.663-2.074-.176-.3-.019-.461.13-.611.135-.135.3-.35.45-.525.15-.175.2-.3.3-.5s.05-.375-.025-.525-.675-1.625-.925-2.225c-.244-.589-.491-.51-.677-.52l-.576-.007c-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.112 4.525.714.309 1.272.494 1.707.632.715.227 1.365.195 1.88.117.574-.088 1.77-.725 2.02-.1425.25-.7.25-1.3.175-1.425-.076-.125-.276-.2-.576-.35z"/>
                            </svg>
                            Reservar Fecha por WhatsApp
                        </a>

                        <!-- Social Networks outline icons aligned below -->
                        <div v-if="profile.widget_status?.instagram || profile.widget_status?.spotify || profile.widget_status?.facebook || profile.widget_status?.youtube" class="flex items-center gap-4 pt-1 pb-2">
                            <!-- Instagram icon -->
                            <a
                                v-if="profile.widget_status?.instagram"
                                :href="profile.widget_status.instagram"
                                target="_blank"
                                class="text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 hover:border-purple-500/30 rounded-xl bg-slate-900/20 hover:bg-purple-500/5"
                                title="Instagram"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>

                            <!-- Spotify icon -->
                            <a
                                v-if="profile.widget_status?.spotify"
                                :href="profile.widget_status.spotify"
                                target="_blank"
                                class="text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 hover:border-purple-500/30 rounded-xl bg-slate-900/20 hover:bg-purple-500/5"
                                title="Spotify"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.372 0 12s5.373 12 12 12 12-5.372 12-12S18.627 0 12 0zm5.49 17.3c-.22.36-.685.478-1.045.258-2.868-1.752-6.48-2.15-10.732-1.176-.41.096-.82-.163-.918-.574-.097-.41.162-.82.573-.917 4.653-1.064 8.628-.606 11.865 1.373.36.22.477.685.257 1.045zm1.464-3.262c-.277.45-.86.598-1.31.32-3.284-2.02-8.293-2.607-12.177-1.428-.506.153-1.04-.136-1.194-.643-.154-.506.136-1.04.643-1.194 4.432-1.345 9.947-.694 13.718 1.63.45.276.598.86.32 1.31zm.126-3.414C15.114 8.27 8.57 8.053 4.78 9.203c-.59.18-1.21-.15-1.39-.74-.18-.59.15-1.21.74-1.39 4.35-1.32 11.56-1.07 16.1 1.62.53.31.7.99.39 1.52-.31.53-.99.7-1.52.39z"/>
                                </svg>
                            </a>

                            <!-- Facebook icon -->
                            <a
                                v-if="profile.widget_status?.facebook"
                                :href="profile.widget_status.facebook"
                                target="_blank"
                                class="text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 hover:border-purple-500/30 rounded-xl bg-slate-900/20 hover:bg-purple-500/5"
                                title="Facebook"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- YouTube icon -->
                            <a
                                v-if="profile.widget_status?.youtube"
                                :href="profile.widget_status.youtube"
                                target="_blank"
                                class="text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 hover:border-purple-500/30 rounded-xl bg-slate-900/20 hover:bg-purple-500/5"
                                title="YouTube"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.517 0-9.388.507a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.871.507 9.388.507 9.388.507s7.517 0 9.388-.507a3.003 3.003 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- HERO RIGHT: Cover Image with bottom gradient mask & backstage glows (Visible only on Desktop) -->
                <div class="hidden lg:block lg:col-span-5 relative w-full h-[70vh] lg:h-[80vh] select-none flex items-center justify-center">
                    
                    <!-- Neon Concert backstage lighting aura -->
                    <div class="absolute -inset-12 bg-gradient-to-tr from-purple-600/30 to-pink-500/20 blur-[130px] pointer-events-none z-0"></div>

                    <!-- Frameless image container with mask gradient -->
                    <div class="relative z-10 w-full h-full rounded-[36px] overflow-hidden">
                        <template v-if="mainPhoto">
                            <img
                                :src="mainPhoto"
                                :alt="profile.name"
                                class="w-full h-full object-cover object-center rounded-[36px]"
                            />
                            
                            <!-- Master 3D bottom gradient mask: blends perfectly into the deep black bg -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent pointer-events-none z-10"></div>
                        </template>
                        
                        <!-- Placeholder cover if profile has no photos -->
                        <div v-else class="w-full h-full bg-gradient-to-br from-slate-900 via-purple-950/30 to-pink-950/20 flex flex-col items-center justify-center p-8 text-center rounded-[36px]">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center text-slate-950 font-black text-4xl shadow-[0_0_35px_rgba(168,85,247,0.5)] mb-4">
                                {{ getInitials(profile.name) }}
                            </div>
                            <h3 class="text-2xl font-black text-white uppercase tracking-wider">{{ profile.name }}</h3>
                            <p class="text-xs text-purple-400 font-semibold mt-1">Tarjeta de Presentación Virtual</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. WIDGETS SECTION (Deep-blue space glass bento cards below the Hero) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start mt-4">
                
                <!-- WIDGETS LEFT: Music & Gallery Widgets (Spans 7 columns on Desktop) -->
                <div class="grid grid-cols-1 gap-8 lg:col-span-7">
                    
                    <!-- MUSIC WIDGET (Deep space glassmorphism) -->
                    <section
                        v-if="profile.widget_status?.spotify"
                        class="bg-[#0d1527]/40 backdrop-blur-md border border-purple-500/10 rounded-3xl p-6 shadow-xl relative overflow-hidden group hover:border-purple-500/30 transition-all duration-300 hover:shadow-[0_0_30px_rgba(168,85,247,0.15)]"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-0.5 bg-purple-500 rounded-full"></span>
                            <h3 class="text-xs font-black tracking-widest text-purple-400 uppercase">
                                Música Destacada
                            </h3>
                        </div>

                        <!-- Custom Premium Player Card UI -->
                        <div class="bg-slate-950/80 border border-slate-850 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-5 shadow-inner relative group/player overflow-hidden">
                            <!-- Overlay light source -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-purple-500/5 via-transparent to-transparent opacity-0 group-hover/player:opacity-100 transition-opacity duration-500"></div>

                            <!-- Vinyl artwork -->
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-600 shadow-[0_0_20px_rgba(168,85,247,0.4)] flex items-center justify-center shrink-0 relative overflow-hidden group-hover/player:scale-105 transition-all duration-500 select-none">
                                <div class="absolute inset-2 rounded-full border border-white/20 flex items-center justify-center bg-slate-950/95 animate-[spin_10s_linear_infinite]" :class="{ 'paused': !isMusicPlaying }">
                                    <!-- Center Vinyl label -->
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center text-[8px] font-black text-slate-950">
                                        {{ getInitials(profile.name) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Track details -->
                            <div class="flex-1 w-full min-w-0">
                                <h4 class="text-base font-extrabold text-white truncate leading-tight group-hover/player:text-purple-300 transition-colors">
                                    {{ profile.name }} Showcase
                                </h4>
                                <p class="text-xs text-purple-400/80 font-bold mt-1 truncate">Música original y covers destacados</p>
                                
                                <!-- Progress Bar -->
                                <div class="mt-4">
                                    <div class="w-full h-1 bg-slate-900 border border-slate-800 rounded-full overflow-hidden relative">
                                        <div 
                                            class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full shadow-[0_0_10px_rgba(168,85,247,0.8)]"
                                            :class="isMusicPlaying ? 'w-[45%] transition-all duration-[8000ms] ease-out' : 'w-[12%] transition-all duration-500'"
                                        ></div>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-500 font-bold mt-1.5">
                                        <span>{{ isMusicPlaying ? '01:34' : '00:15' }}</span>
                                        <span>03:45</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Circular Glowing Play Button -->
                            <button
                                @click="toggleMusicPlay"
                                class="w-14 h-14 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 text-slate-950 shadow-[0_0_20px_rgba(168,85,247,0.45)] flex items-center justify-center shrink-0 hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer select-none group/btn"
                            >
                                <svg v-if="!isMusicPlaying" class="h-6 w-6 fill-slate-950 translate-x-0.5 group-hover/btn:scale-105 transition-transform" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <svg v-else class="h-6 w-6 fill-slate-950 group-hover/btn:scale-105 transition-transform" viewBox="0 0 24 24">
                                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Spotify complete link -->
                        <div class="mt-4 flex justify-end">
                            <a
                                :href="profile.widget_status.spotify"
                                target="_blank"
                                class="text-xs font-bold text-pink-400 hover:text-pink-300 flex items-center gap-1.5 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.372 0 12s5.373 12 12 12 12-5.372 12-12S18.627 0 12 0zm5.49 17.3c-.22.36-.685.478-1.045.258-2.868-1.752-6.48-2.15-10.732-1.176-.41.096-.82-.163-.918-.574-.097-.41.162-.82.573-.917 4.653-1.064 8.628-.606 11.865 1.373.36.22.477.685.257 1.045zm1.464-3.262c-.277.45-.86.598-1.31.32-3.284-2.02-8.293-2.607-12.177-1.428-.506.153-1.04-.136-1.194-.643-.154-.506.136-1.04.643-1.194 4.432-1.345 9.947-.694 13.718 1.63.45.276.598.86.32 1.31zm.126-3.414C15.114 8.27 8.57 8.053 4.78 9.203c-.59.18-1.21-.15-1.39-.74-.18-.59.15-1.21.74-1.39 4.35-1.32 11.56-1.07 16.1 1.62.53.31.7.99.39 1.52-.31.53-.99.7-1.52.39z"/>
                                </svg>
                                Escuchar perfil en Spotify completo
                            </a>
                        </div>
                    </section>

                    <!-- GALLERY WIDGET (Deep space glassmorphism) -->
                    <section
                        v-if="profile.widget_status?.media !== false && allMedia.length > 0"
                        class="bg-[#0d1527]/40 backdrop-blur-md border border-purple-500/10 rounded-3xl p-6 shadow-xl relative overflow-hidden group hover:border-purple-500/30 transition-all duration-300 hover:shadow-[0_0_30px_rgba(168,85,247,0.15)]"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-0.5 bg-purple-500 rounded-full"></span>
                            <h3 class="text-xs font-black tracking-widest text-purple-400 uppercase">
                                Galería & Videos
                            </h3>
                        </div>

                        <!-- Infinite Dynamic Responsive Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div
                                v-for="item in allMedia"
                                :key="item.id"
                                class="relative aspect-video rounded-2xl overflow-hidden border border-purple-500/15 hover:border-purple-500/35 transition-all duration-300 shadow-md group cursor-pointer bg-slate-950"
                            >
                                <!-- YouTube / Vimeo thumbnail -> Click to open in Lightbox -->
                                <template v-if="item.type === 'youtube' || item.type === 'vimeo'">
                                    <div class="w-full h-full relative" @click="openLightbox(item)">
                                        <img
                                            v-if="getYouTubeThumbnail(item)"
                                            :src="getYouTubeThumbnail(item)"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            alt="Video Cover"
                                        />
                                        <div v-else class="w-full h-full bg-gradient-to-tr from-slate-900 to-purple-950/40 flex items-center justify-center">
                                            <span class="text-[9px] text-purple-400 font-bold uppercase tracking-widest">Video Demo</span>
                                        </div>
                                        <!-- Play icon overlay -->
                                        <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/15 transition-colors flex items-center justify-center">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-slate-950 shadow-[0_0_15px_rgba(168,85,247,0.5)] group-hover:scale-115 transition-all duration-300">
                                                <svg class="h-4.5 w-4.5 fill-slate-950 translate-x-0.5" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- Title Overlay -->
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                            <p class="text-[9px] font-bold text-white truncate">{{ item.title }}</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Local Video Player -->
                                <template v-else-if="item.type === 'video' && item.path">
                                    <div class="w-full h-full relative" @click="openLightbox(item)">
                                        <video
                                            :src="'/' + item.path"
                                            class="w-full h-full object-cover pointer-events-none"
                                            preload="metadata"
                                            muted
                                        ></video>
                                        <!-- Play icon overlay -->
                                        <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/15 transition-colors flex items-center justify-center">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-slate-950 shadow-[0_0_15px_rgba(168,85,247,0.5)] group-hover:scale-115 transition-all duration-300">
                                                <svg class="h-4.5 w-4.5 fill-slate-950 translate-x-0.5" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- Title Overlay -->
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                            <p class="text-[9px] font-bold text-white truncate">{{ item.title }}</p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Photo / Image -->
                                <template v-else>
                                    <div class="w-full h-full relative" @click="openLightbox(item)">
                                        <img
                                            :src="'/' + item.path"
                                            :alt="item.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        />
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <p class="text-[9px] font-bold text-white truncate text-center">{{ item.title }}</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- WIDGETS RIGHT: Shows/Events Widget (Spans 5 columns on Desktop) -->
                <div class="grid grid-cols-1 gap-8 lg:col-span-5">
                    
                    <!-- SHOWS WIDGET (Deep space glassmorphism) -->
                    <section
                        v-if="profile.widget_status?.agenda !== false && upcomingEvents.length > 0"
                        class="bg-[#0d1527]/40 backdrop-blur-md border border-purple-500/10 rounded-3xl p-6 shadow-xl relative overflow-hidden group hover:border-purple-500/30 transition-all duration-300 hover:shadow-[0_0_30px_rgba(168,85,247,0.15)]"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-0.5 bg-purple-500 rounded-full"></span>
                            <h3 class="text-xs font-black tracking-widest text-purple-400 uppercase">
                                Próximas Presentaciones
                            </h3>
                        </div>

                        <!-- Ticket-style List of Shows -->
                        <div class="flex flex-col gap-4">
                            <div
                                v-for="event in upcomingEvents"
                                :key="event.id"
                                class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-slate-950/80 border border-slate-850 p-4 rounded-2xl overflow-hidden group/gig hover:border-purple-500/30 transition-all duration-300"
                            >
                                <!-- Date Badge (Concert ticket notch feel) -->
                                <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white w-14 py-3 rounded-2xl shrink-0 flex flex-col items-center justify-center font-black leading-none text-center shadow-lg relative">
                                    <!-- Ticket notches -->
                                    <div class="absolute top-1/2 -left-1.5 w-3 h-3 bg-slate-950 border-r border-slate-850 rounded-full shrink-0"></div>
                                    <div class="absolute top-1/2 -right-1.5 w-3 h-3 bg-slate-950 border-l border-slate-850 rounded-full shrink-0"></div>
                                    
                                    <span class="text-[9px] uppercase font-bold text-white/80 tracking-wider">
                                        {{ formatEventDate(event.start_time).split(' ')[1] }}
                                    </span>
                                    <span class="text-xl font-black mt-0.5">
                                        {{ formatEventDate(event.start_time).split(' ')[0] }}
                                    </span>
                                </div>

                                <!-- Gigs Details -->
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-base font-extrabold text-white leading-tight truncate group-hover/gig:text-purple-300 transition-colors">
                                        {{ event.title }}
                                    </h4>
                                    
                                    <div class="flex flex-wrap items-center gap-3 mt-1.5">
                                        <p class="text-xs text-slate-400 truncate flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5 text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ event.location }}
                                        </p>
                                        <p class="text-xs text-slate-500 font-semibold flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5 text-pink-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatEventTime(event.start_time) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Action redirection (Ticket Redirection CTA) -->
                                <div class="w-full sm:w-auto flex justify-end shrink-0">
                                    <a
                                        :href="getWhatsAppUrl()"
                                        target="_blank"
                                        class="bg-slate-900 border border-slate-800 hover:border-purple-500/50 hover:bg-purple-950/20 text-purple-400 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-all duration-300 cursor-pointer shadow-md select-none w-full sm:w-auto text-center"
                                    >
                                        Adquirir Accesos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

        <!-- Lightbox Modal for Photo/Video Preview -->
        <div v-if="activeLightboxItem" class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" @click="closeLightbox">
            <!-- Close Button -->
            <button
                @click="closeLightbox"
                class="absolute top-6 right-6 text-white/80 hover:text-white bg-slate-900/60 hover:bg-slate-800/80 p-3 rounded-full transition-all duration-300 hover:scale-105 z-55 flex items-center justify-center text-xl font-bold cursor-pointer"
                title="Cerrar vista"
            >
                ✕
            </button>

            <!-- Lightbox Content Container -->
            <div class="max-w-5xl max-h-[85vh] w-full flex items-center justify-center" @click.stop>
                <!-- YouTube or Vimeo iframe inside Lightbox -->
                <iframe
                    v-if="activeLightboxItem.type === 'youtube' || activeLightboxItem.type === 'vimeo'"
                    :src="getVideoEmbedUrl(activeLightboxItem)"
                    class="w-full aspect-video rounded-2xl shadow-[0_0_50px_rgba(168,85,247,0.35)] border border-purple-500/20 max-w-4xl max-h-[75vh]"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>

                <!-- Local Video Player -->
                <video
                    v-else-if="activeLightboxItem.type === 'video' && activeLightboxItem.path"
                    :src="'/' + activeLightboxItem.path"
                    class="max-w-full max-h-[80vh] rounded-2xl shadow-[0_0_50px_rgba(168,85,247,0.3)] border border-purple-500/20"
                    controls
                    autoplay
                ></video>

                <!-- Photo Image -->
                <img
                    v-else-if="activeLightboxItem.type === 'photo'"
                    :src="'/' + activeLightboxItem.path"
                    :alt="activeLightboxItem.title"
                    class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-[0_0_50px_rgba(168,85,247,0.2)] border border-white/10"
                />
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
.bg-slate-950 {
    scrollbar-width: none;
}
.bg-slate-950::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(168, 85, 247, 0.2);
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(168, 85, 247, 0.4);
}
.paused {
    animation-play-state: paused !important;
}
</style>
