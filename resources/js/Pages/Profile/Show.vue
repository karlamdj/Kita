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

// ─── Theme System ─────────────────────────────────────────────────────────────
// Each theme exposes a complete set of class strings for every accent element.
// Using full class strings (not dynamic concatenation) keeps Tailwind's purge safe.
const THEMES = {
    'kita-neon': {
        // Hero elements
        instrument_bg:    'bg-pink-500/10 text-pink-300 border border-pink-500/20',
        zone_bg:          'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20',
        name_gradient:    'from-cyan-400 to-cyan-200',
        // WhatsApp CTA button
        cta_btn:          'from-cyan-600 to-cyan-500 hover:from-cyan-500 hover:to-cyan-400 shadow-[0_0_20px_rgba(6,182,212,0.4)] hover:shadow-[0_0_30px_rgba(6,182,212,0.6)]',
        // Social icons
        social_icon:      'hover:border-cyan-500/30 hover:bg-cyan-500/5',
        // Section titles
        section_accent:   'bg-cyan-500',
        section_title:    'text-cyan-400',
        // Widget card borders
        widget_card:      'border-cyan-500/10 hover:border-cyan-500/30 hover:shadow-[0_0_30px_rgba(6,182,212,0.15)]',
        // Events date badge
        event_badge:      'from-cyan-600 to-cyan-800',
        // "Adquirir Accesos" CTA
        event_cta:        'hover:border-cyan-500/50 hover:bg-cyan-950/20 text-cyan-400',
        // Vinyl / player
        vinyl:            'from-cyan-600 to-cyan-800',
        vinyl_center:     'from-cyan-500 to-cyan-300',
        play_btn:         'from-cyan-500 to-cyan-400 hover:from-cyan-400 hover:to-cyan-300 shadow-[0_0_20px_rgba(6,182,212,0.45)]',
        progress_bar:     'from-cyan-500 to-cyan-300 shadow-[0_0_10px_rgba(6,182,212,0.8)]',
        spotify_link:     'text-cyan-400 hover:text-cyan-300',
        // Gallery play circle
        play_circle:      'from-cyan-500 to-cyan-400',
        // Lightbox border
        lightbox_border:  'border-cyan-500/20 shadow-[0_0_50px_rgba(6,182,212,0.35)]',
        // Ambient glows (kept original purple/pink for depth)
        glow_a:           'bg-purple-900/10',
        glow_b:           'bg-pink-900/10',
        stage_a:          'bg-purple-600/10',
        stage_b:          'bg-pink-600/10',
        hero_aura:        'from-cyan-500/50 to-blue-600/30',
        // Custom theme additions
        edit_btn:         'text-cyan-400 hover:border-cyan-500/50 hover:shadow-[0_0_15px_rgba(6,182,212,0.3)]',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(6,182,212,0.5)]',
        placeholder_avatar: 'from-cyan-500 to-blue-500 shadow-[0_0_35px_rgba(6,182,212,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(6,182,212,0.4)]',
    },
    'cyber-purple': {
        instrument_bg:    'bg-pink-500/10 text-pink-300 border border-pink-500/20',
        zone_bg:          'bg-purple-500/10 text-purple-300 border border-purple-500/20',
        name_gradient:    'from-purple-400 to-pink-400',
        cta_btn:          'from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 shadow-[0_0_20px_rgba(168,85,247,0.4)] hover:shadow-[0_0_30px_rgba(236,72,153,0.6)]',
        social_icon:      'hover:border-purple-500/30 hover:bg-purple-500/5',
        section_accent:   'bg-purple-500',
        section_title:    'text-purple-400',
        widget_card:      'border-purple-500/10 hover:border-purple-500/30 hover:shadow-[0_0_30px_rgba(168,85,247,0.15)]',
        event_badge:      'from-purple-600 to-pink-600',
        event_cta:        'hover:border-purple-500/50 hover:bg-purple-950/20 text-purple-400',
        vinyl:            'from-purple-600 to-pink-600',
        vinyl_center:     'from-purple-500 to-pink-500',
        play_btn:         'from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 shadow-[0_0_20px_rgba(168,85,247,0.45)]',
        progress_bar:     'from-purple-500 to-pink-500 shadow-[0_0_10px_rgba(168,85,247,0.8)]',
        spotify_link:     'text-pink-400 hover:text-pink-300',
        play_circle:      'from-purple-500 to-pink-500',
        lightbox_border:  'border-purple-500/20 shadow-[0_0_50px_rgba(168,85,247,0.35)]',
        glow_a:           'bg-purple-900/10',
        glow_b:           'bg-pink-900/10',
        stage_a:          'bg-purple-600/10',
        stage_b:          'bg-pink-600/10',
        hero_aura:        'from-purple-600/30 to-pink-500/20',
        edit_btn:         'text-purple-400 hover:border-purple-500/50 hover:shadow-[0_0_15px_rgba(168,85,247,0.3)]',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(168,85,247,0.5)]',
        placeholder_avatar: 'from-purple-500 to-pink-500 shadow-[0_0_35px_rgba(168,85,247,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(168,85,247,0.4)]',
    },
    'volt-orange': {
        instrument_bg:    'bg-orange-500/10 text-orange-300 border border-orange-500/20',
        zone_bg:          'bg-amber-500/10 text-amber-300 border border-amber-500/20',
        name_gradient:    'from-orange-400 to-amber-300',
        cta_btn:          'from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 shadow-[0_0_20px_rgba(249,115,22,0.4)] hover:shadow-[0_0_30px_rgba(251,191,36,0.6)]',
        social_icon:      'hover:border-orange-500/30 hover:bg-orange-500/5',
        section_accent:   'bg-orange-500',
        section_title:    'text-orange-400',
        widget_card:      'border-orange-500/10 hover:border-orange-500/30 hover:shadow-[0_0_30px_rgba(249,115,22,0.15)]',
        event_badge:      'from-orange-600 to-amber-600',
        event_cta:        'hover:border-orange-500/50 hover:bg-orange-950/20 text-orange-400',
        vinyl:            'from-orange-600 to-amber-600',
        vinyl_center:     'from-orange-500 to-amber-400',
        play_btn:         'from-orange-500 to-amber-400 hover:from-orange-400 hover:to-amber-300 shadow-[0_0_20px_rgba(249,115,22,0.45)]',
        progress_bar:     'from-orange-500 to-amber-400 shadow-[0_0_10px_rgba(249,115,22,0.8)]',
        spotify_link:     'text-orange-400 hover:text-orange-300',
        play_circle:      'from-orange-500 to-amber-400',
        lightbox_border:  'border-orange-500/20 shadow-[0_0_50px_rgba(249,115,22,0.35)]',
        glow_a:           'bg-orange-900/10',
        glow_b:           'bg-amber-900/10',
        stage_a:          'bg-orange-600/10',
        stage_b:          'bg-amber-600/10',
        hero_aura:        'from-orange-600/30 to-amber-500/20',
        edit_btn:         'text-orange-400 hover:border-orange-500/50 hover:shadow-[0_0_15px_rgba(249,115,22,0.3)]',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(249,115,22,0.5)]',
        placeholder_avatar: 'from-orange-500 to-amber-500 shadow-[0_0_35px_rgba(249,115,22,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(249,115,22,0.4)]',
    },
    'electric-red': {
        instrument_bg:    'bg-red-500/10 text-red-300 border border-red-500/20',
        zone_bg:          'bg-red-500/10 text-red-400 border border-red-500/20',
        name_gradient:    'from-red-500 to-orange-500',
        cta_btn:          'from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 shadow-[0_0_20px_rgba(220,38,38,0.3)] hover:shadow-[0_0_30px_rgba(220,38,38,0.5)]',
        social_icon:      'hover:border-red-500/30 hover:bg-red-500/5',
        section_accent:   'bg-red-500',
        section_title:    'text-red-500',
        widget_card:      'border-red-500/10 hover:border-red-500/30 hover:shadow-[0_0_30px_rgba(239,68,68,0.15)]',
        event_badge:      'from-red-600 to-orange-500',
        event_cta:        'hover:border-red-500/50 hover:bg-red-950/20 text-red-400',
        vinyl:            'from-red-600 to-orange-600',
        vinyl_center:     'from-red-500 to-orange-500',
        play_btn:         'from-red-500 to-orange-500 hover:from-red-400 hover:to-orange-400 shadow-[0_0_20px_rgba(239,68,68,0.45)]',
        progress_bar:     'from-red-500 to-orange-500 shadow-[0_0_10px_rgba(239,68,68,0.8)]',
        spotify_link:     'text-red-400 hover:text-red-300',
        play_circle:      'from-red-500 to-orange-500',
        lightbox_border:  'border-red-500/20 shadow-[0_0_50px_rgba(239,68,68,0.35)]',
        glow_a:           'bg-red-900/10',
        glow_b:           'bg-orange-900/10',
        stage_a:          'bg-red-600/10',
        stage_b:          'bg-orange-600/10',
        hero_aura:        'from-red-600/30 to-orange-500/20',
        edit_btn:         'text-red-400 hover:border-red-500/50 hover:shadow-[0_0_15px_rgba(220,38,38,0.3)]',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(239,68,68,0.5)]',
        placeholder_avatar: 'from-red-500 to-orange-500 shadow-[0_0_35px_rgba(239,68,68,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(239,68,68,0.4)]',
    },
};

// Active theme classes — falls back to kita-neon if unknown value
const tc = computed(() => THEMES[props.profile.theme] ?? THEMES['kita-neon']);
// ─────────────────────────────────────────────────────────────────────────────

// Helper to get initials
const getInitials = (name) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

// ─── Media Platform Detection ─────────────────────────────────────────────────

// Detect platform from URL
const detectPlatform = (url) => {
    if (!url) return 'unknown';
    if (/youtube\.com|youtu\.be/.test(url)) return 'youtube';
    if (/facebook\.com|fb\.watch/.test(url)) return 'facebook';
    if (/instagram\.com/.test(url)) return 'instagram';
    if (/tiktok\.com/.test(url)) return 'tiktok';
    return 'unknown';
};

// Extract YouTube video ID
const getYouTubeId = (url) => {
    if (!url) return null;
    const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return match && match[2].length === 11 ? match[2] : null;
};

// Get YouTube embed URL with autoplay
const getYouTubeEmbedUrl = (url, autoplay = false) => {
    const id = getYouTubeId(url);
    return id ? `https://www.youtube.com/embed/${id}${autoplay ? '?autoplay=1&rel=0' : '?rel=0'}` : null;
};

// Get YouTube thumbnail
const getYouTubeThumbnail = (url) => {
    const id = getYouTubeId(url);
    return id ? `https://img.youtube.com/vi/${id}/hqdefault.jpg` : null;
};

// Determine if a media item is a vertical video format (TikTok / Instagram Reels)
const isVerticalVideo = (item) => {
    const platform = getPlatform(item);
    return platform === 'tiktok' || platform === 'instagram';
};

// Unified platform getter for media items (supports old type field + URL detection)
const getPlatform = (item) => {
    if (item.type === 'photo') return 'photo';
    // First try URL-based detection for new items
    const urlPlatform = detectPlatform(item.url);
    if (urlPlatform !== 'unknown') return urlPlatform;
    // Fallback to type field for legacy items
    if (item.type === 'youtube') return 'youtube';
    if (item.type === 'vimeo') return 'vimeo';
    return 'unknown';
};

// Extract TikTok video ID from URL
const getTikTokId = (url) => {
    if (!url) return null;
    // Matches https://www.tiktok.com/@user/video/1234567890
    const match = url.match(/\/video\/(\d+)/);
    return match ? match[1] : null;
};

// Get embed URL for any platform (lightbox use)
const getEmbedUrl = (item, autoplay = true) => {
    const url = item.url;
    const platform = getPlatform(item);
    switch (platform) {
        case 'youtube':
            return getYouTubeEmbedUrl(url, autoplay);
        case 'tiktok': {
            // Official TikTok iframe embed — no SDK needed
            const id = getTikTokId(url);
            return id ? `https://www.tiktok.com/embed/v2/${id}` : null;
        }
        case 'facebook':
        case 'instagram':
            // These platforms don't support reliable cross-origin iframe embeds.
            // Return null — the lightbox shows a styled redirect card instead.
            return null;
        default:
            return null;
    }
};

// Get thumbnail/cover for any media item
const getThumbnail = (item) => {
    if (item.type === 'photo') {
        return item.path ? (item.path.startsWith('http') ? item.path : '/' + item.path) : null;
    }
    if (item.path) {
        return item.path.startsWith('http') ? item.path : '/' + item.path;
    }
    const platform = getPlatform(item);
    if (platform === 'youtube') return getYouTubeThumbnail(item.url);
    // Facebook and Instagram can display branded covers
    if (platform === 'facebook') return null;
    if (platform === 'instagram') return null;
    if (platform === 'tiktok') return null;
    return null;
};

// Platform icon SVG paths (used in thumbnails overlays)
const platformIcon = (item) => {
    const p = getPlatform(item);
    if (p === 'youtube') return { color: '#FF0000', label: 'YouTube' };
    if (p === 'facebook') return { color: '#1877F2', label: 'Facebook' };
    if (p === 'instagram') return { color: '#E1306C', label: 'Instagram' };
    if (p === 'tiktok') return { color: '#010101', label: 'TikTok' };
    return { color: '#94a3b8', label: 'Video' };
};

// ─── Media Lists ────────────────────────────────────────────────────────────────
const photos = computed(() => props.profile.media?.filter(item => item.type === 'photo') || []);
const upcomingEvents = computed(() => props.profile.events || []);

// All media unified for the grid (photos + social videos)
const allMedia = computed(() => props.profile.media?.filter(item => {
    // Only include photos and social video links (no local video files)
    if (item.type === 'photo') return true;
    const platform = detectPlatform(item.url);
    return platform !== 'unknown';
}) || []);

// Filtered media by aspect format for carousels
const verticalMedia = computed(() => allMedia.value.filter(item => {
    const platform = getPlatform(item);
    return platform === 'tiktok' || platform === 'instagram';
}));

const horizontalMedia = computed(() => allMedia.value.filter(item => {
    const platform = getPlatform(item);
    return platform === 'youtube' || platform === 'facebook' || platform === 'vimeo' || platform === 'photo';
}));

// ─── Lightbox ──────────────────────────────────────────────────────────────────
const activeLightboxItem = ref(null);
const openLightbox = (item) => { activeLightboxItem.value = item; };
const closeLightbox = () => { activeLightboxItem.value = null; };

// ─── Profile Photo ─────────────────────────────────────────────────────────────
const mainPhoto = computed(() => {
    if (props.profile.profile_photo_path) {
        return '/' + props.profile.profile_photo_path;
    }
    return null;
});

// Split name for high-impact typography
const nameParts = computed(() => {
    const name = props.profile.name || '';
    const parts = name.split(' ');
    if (parts.length > 1) {
        return { first: parts[0], rest: parts.slice(1).join(' ') };
    }
    return { first: name, rest: '' };
});



// State to track playing video ID (legacy compat)
const activePlayingVideoId = ref(null);
const playVideo = (videoId) => { activePlayingVideoId.value = videoId; };

// Build WhatsApp Link
const getWhatsAppUrl = (eventTitle = null) => {
    const phone = props.profile.widget_status?.whatsapp || '';
    const formattedPhone = phone.replace(/[^0-9]/g, '');
    const targetPhone = formattedPhone || '523221234567';
    
    let text = '';
    if (eventTitle) {
        text = `Hola ${props.profile.name}, vi en KITA que tienes un evento proximo en ${eventTitle} quisiera adquirir accesos`;
    } else {
        text = `Hola ${props.profile.name}, te contacto desde tu perfil de KITA. Me gustaría cotizar un show!`;
    }
    
    return `https://wa.me/${targetPhone}?text=${encodeURIComponent(text)}`;
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
                :class="['bg-slate-900/90 hover:bg-slate-800 border border-slate-800 px-4 py-2.5 rounded-xl text-xs font-black flex items-center gap-1.5 transition-all duration-300 hover:scale-105 cursor-pointer shadow-md justify-center', tc.edit_btn]"
            >
                <span>✏️</span> Editar Perfil
            </button>
        </div>

        <!-- Ambient Decorative Glowing Blobs (theme-driven) -->
        <div :class="['absolute top-[10%] left-[-10%] w-[60vw] h-[60vw] rounded-full blur-[130px] pointer-events-none z-0', tc.glow_a]"></div>
        <div :class="['absolute bottom-[10%] right-[-10%] w-[70vw] h-[70vw] rounded-full blur-[140px] pointer-events-none z-0', tc.glow_b]"></div>

        <!-- Stage Lights (theme-driven) -->
        <div :class="['absolute top-10 right-10 w-[450px] h-[450px] rounded-full blur-[140px] pointer-events-none z-0', tc.stage_a]"></div>
        <div :class="['absolute bottom-20 left-10 w-[500px] h-[500px] rounded-full blur-[140px] pointer-events-none z-0', tc.stage_b]"></div>

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
                            :class="['px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm', tc.instrument_bg]"
                        >
                            {{ inst }}
                        </span>
                        
                        <template v-if="Array.isArray(profile.coverage_area)">
                            <span
                                v-for="zone in profile.coverage_area"
                                :key="zone"
                                :class="['px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                            >
                                📍 {{ zone }}
                            </span>
                        </template>
                        <span
                            v-else-if="profile.coverage_area"
                            :class="['px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                        >
                            📍 {{ profile.coverage_area }}
                        </span>
                    </div>

                    <!-- Giant typography Name Hierarchy -->
                    <div class="flex flex-col leading-none">
                        <h1 class="text-white font-black text-6xl sm:text-7xl lg:text-8xl tracking-wider uppercase leading-none">
                            {{ nameParts.first }}
                        </h1>
                        <span v-if="nameParts.rest" :class="['bg-gradient-to-r text-transparent bg-clip-text text-6xl sm:text-7xl lg:text-8xl font-black uppercase leading-none mt-2', tc.name_gradient]">
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
                            :class="['w-full sm:w-auto bg-gradient-to-r text-white font-extrabold py-4 px-8 rounded-2xl flex items-center justify-center gap-2.5 transition-all duration-300 hover:scale-[1.03] cursor-pointer text-sm tracking-wider uppercase', tc.cta_btn]"
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
                                :class="['text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                    <div :class="['absolute -inset-12 bg-gradient-to-tr blur-[130px] pointer-events-none z-0', tc.hero_aura]"></div>

                    <!-- Frameless image container with mask gradient -->
                    <div :class="['relative z-10 w-full h-full rounded-[36px] overflow-hidden bg-[#0d1527]/40 backdrop-blur-md border transition-all duration-300', tc.widget_card]">
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
                        <div v-else class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 flex flex-col items-center justify-center p-8 text-center rounded-[36px]">
                            <div :class="['w-24 h-24 rounded-full bg-gradient-to-tr flex items-center justify-center text-slate-950 font-black text-4xl mb-4', tc.placeholder_avatar]">
                                {{ getInitials(profile.name) }}
                            </div>
                            <h3 class="text-2xl font-black text-white uppercase tracking-wider">{{ profile.name }}</h3>
                            <p :class="['text-xs font-semibold mt-1', tc.section_title]">Tarjeta de Presentación Virtual</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. WIDGETS SECTION (Deep-blue space glass bento cards below the Hero) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start mt-4">
                
                <!-- WIDGETS LEFT: Music widget (top) + Gallery widget (below) — col-span-7 -->
                <div class="grid grid-cols-1 gap-8 lg:col-span-7">
                    
                    <!-- MUSIC WIDGET (Deep space glassmorphism) -->
                    <section
                        v-if="profile.widget_status?.spotify"
                        :class="['bg-[#0d1527]/40 backdrop-blur-md rounded-3xl p-6 shadow-xl relative overflow-hidden group transition-all duration-300 border', tc.widget_card]"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span :class="['w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                            <h3 :class="['text-xs font-black tracking-widest uppercase', tc.section_title]">
                                Música Destacada
                            </h3>
                        </div>

                        <!-- Custom Premium Player Card UI -->
                        <div class="bg-slate-950/80 border border-slate-850 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-5 shadow-inner relative group/player overflow-hidden">
                            <!-- Overlay light source -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-purple-500/5 via-transparent to-transparent opacity-0 group-hover/player:opacity-100 transition-opacity duration-500"></div>

                            <!-- Vinyl artwork -->
                            <div :class="['w-24 h-24 rounded-2xl bg-gradient-to-tr flex items-center justify-center shrink-0 relative overflow-hidden group-hover/player:scale-105 transition-all duration-500 select-none', tc.vinyl, tc.vinyl_shadow]">
                                <div class="absolute inset-2 rounded-full border border-white/20 flex items-center justify-center bg-slate-950/95 animate-[spin_10s_linear_infinite]" :class="{ 'paused': !isMusicPlaying }">
                                    <!-- Center Vinyl label -->
                                    <div :class="['w-6 h-6 rounded-full bg-gradient-to-tr flex items-center justify-center text-[8px] font-black text-slate-950', tc.vinyl_center]">
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
                                            :class="['h-full bg-gradient-to-r rounded-full', tc.progress_bar, isMusicPlaying ? 'w-[45%] transition-all duration-[8000ms] ease-out' : 'w-[12%] transition-all duration-500']"
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
                                :class="['w-14 h-14 rounded-full bg-gradient-to-r text-slate-950 flex items-center justify-center shrink-0 hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer select-none group/btn', tc.play_btn]"
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
                                :class="['text-xs font-bold flex items-center gap-1.5 transition-colors', tc.spotify_link]"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.372 0 12s5.373 12 12 12 12-5.372 12-12S18.627 0 12 0zm5.49 17.3c-.22.36-.685.478-1.045.258-2.868-1.752-6.48-2.15-10.732-1.176-.41.096-.82-.163-.918-.574-.097-.41.162-.82.573-.917 4.653-1.064 8.628-.606 11.865 1.373.36.22.477.685.257 1.045zm1.464-3.262c-.277.45-.86.598-1.31.32-3.284-2.02-8.293-2.607-12.177-1.428-.506.153-1.04-.136-1.194-.643-.154-.506.136-1.04.643-1.194 4.432-1.345 9.947-.694 13.718 1.63.45.276.598.86.32 1.31zm.126-3.414C15.114 8.27 8.57 8.053 4.78 9.203c-.59.18-1.21-.15-1.39-.74-.18-.59.15-1.21.74-1.39 4.35-1.32 11.56-1.07 16.1 1.62.53.31.7.99.39 1.52-.31.53-.99.7-1.52.39z"/>
                                </svg>
                                Escuchar perfil en Spotify completo
                            </a>
                        </div>
                    </section>


                </div>

                <!-- WIDGETS RIGHT: Próximas Presentaciones — col-span-5, to the RIGHT of Music -->
                <div class="grid grid-cols-1 gap-8 lg:col-span-5">
                    
                    <!-- SHOWS WIDGET (Deep space glassmorphism) -->
                    <section
                        v-if="profile.widget_status?.agenda !== false && upcomingEvents.length > 0"
                        :class="['bg-[#0d1527]/40 backdrop-blur-md rounded-3xl p-6 shadow-xl relative overflow-hidden group transition-all duration-300 border', tc.widget_card]"
                    >
                        <div class="flex items-center gap-3 mb-6">
                            <span :class="['w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                            <h3 :class="['text-xs font-black tracking-widest uppercase', tc.section_title]">
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
                                <div :class="['bg-gradient-to-br text-white w-14 py-3 rounded-2xl shrink-0 flex flex-col items-center justify-center font-black leading-none text-center shadow-lg relative', tc.event_badge]">
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
                                        :href="getWhatsAppUrl(event.title)"
                                        target="_blank"
                                        :class="['bg-slate-900 border border-slate-800 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-all duration-300 cursor-pointer shadow-md select-none w-full sm:w-auto text-center', tc.event_cta]"
                                    >
                                        Adquirir Accesos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- 3. GALLERY & VIDEOS CAROUSEL SECTION (Full Width, split by layout orientation) -->
            <section
                v-if="profile.widget_status?.media !== false && allMedia.length > 0"
                :class="['bg-[#0d1527]/40 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden group transition-all duration-300 border w-full z-10', tc.widget_card]"
            >
                <div class="flex items-center gap-3 mb-8">
                    <span :class="['w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                    <h3 :class="['text-xs font-black tracking-widest uppercase', tc.section_title]">Galería &amp; Videos</h3>
                </div>

                <!-- Vertical Carousel: TikTok & Instagram (Aspect 9:16) -->
                <div v-if="verticalMedia.length > 0" class="mb-10 relative">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                        <span>📱</span> Reels &amp; TikToks
                    </h4>
                    <!-- Fade borders for streaming feel -->
                    <div class="relative w-full">
                        <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-slate-950/20 to-transparent pointer-events-none z-10"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-slate-950/20 to-transparent pointer-events-none z-10"></div>

                        <div class="flex gap-5 overflow-x-auto py-2 scroll-smooth snap-x snap-mandatory scrollbar-hide">
                            <div
                                v-for="item in verticalMedia"
                                :key="item.id"
                                @click="openLightbox(item)"
                                class="snap-start shrink-0 w-[180px] sm:w-[220px] aspect-[9/16] relative rounded-2xl overflow-hidden border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-300 shadow-md group cursor-pointer bg-slate-900/60 backdrop-blur-md"
                            >
                                <!-- ── INSTAGRAM cover ── -->
                                <template v-if="getPlatform(item) === 'instagram'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="Instagram Reel"
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-tr from-pink-950/60 via-purple-950/40 to-orange-950/40 flex flex-col items-center justify-center gap-3 p-4">
                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none">
                                            <defs><linearGradient id="ig-grad-new" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#f09433"/><stop offset="25%" stop-color="#e6683c"/><stop offset="50%" stop-color="#dc2743"/><stop offset="75%" stop-color="#cc2366"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs>
                                            <rect width="24" height="24" rx="6" fill="url(#ig-grad-new)"/>
                                            <path d="M12 8.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7zm0 5.75A2.25 2.25 0 1112 9.75a2.25 2.25 0 010 4.5zM16 8a.75.75 0 100-1.5.75.75 0 000 1.5z" fill="white"/>
                                            <path fill-rule="evenodd" d="M8 3h8a5 5 0 015 5v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8a5 5 0 015-5zm0 1.5A3.5 3.5 0 004.5 8v8A3.5 3.5 0 008 19.5h8a3.5 3.5 0 003.5-3.5V8A3.5 3.5 0 0016 4.5H8z" fill="white"/>
                                        </svg>
                                        <p class="text-[9px] text-slate-300 font-semibold text-center truncate w-full">{{ item.title || 'Reel de Instagram' }}</p>
                                    </div>
                                    <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-pink-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                                            <svg class="h-5 w-5 fill-white translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-pink-600/90 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">IG</div>
                                    <div v-if="getThumbnail(item)" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                        <p class="text-[9px] font-bold text-white truncate">{{ item.title || 'Reel de Instagram' }}</p>
                                    </div>
                                </template>

                                <!-- ── TIKTOK cover ── -->
                                <template v-else-if="getPlatform(item) === 'tiktok'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="TikTok Video"
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-b from-slate-950 to-slate-900 flex flex-col items-center justify-center gap-3 p-4">
                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-1.02-.1z" class="fill-[#69C9D0]"/>
                                            <path d="M15.82 2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-3.79-4.79z" class="fill-[#EE1D52]"/>
                                        </svg>
                                        <p class="text-[9px] text-slate-300 font-semibold text-center truncate w-full">{{ item.title || 'Video de TikTok' }}</p>
                                    </div>
                                    <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-slate-800 border border-[#EE1D52]/50 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                                            <svg class="h-5 w-5 fill-white translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-slate-900/90 text-[#EE1D52] border border-[#EE1D52]/50 text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">TT</div>
                                    <div v-if="getThumbnail(item)" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                        <p class="text-[9px] font-bold text-white truncate">{{ item.title || 'Video de TikTok' }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Carousel: YouTube, Facebook & Photos (Aspect 16:9) -->
                <div v-if="horizontalMedia.length > 0" class="relative">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                        <span>🎬</span> Videos &amp; Galería
                    </h4>
                    <!-- Fade borders for streaming feel -->
                    <div class="relative w-full">
                        <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-slate-950/20 to-transparent pointer-events-none z-10"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-slate-950/20 to-transparent pointer-events-none z-10"></div>

                        <div class="flex gap-5 overflow-x-auto py-2 scroll-smooth snap-x snap-mandatory scrollbar-hide">
                            <div
                                v-for="item in horizontalMedia"
                                :key="item.id"
                                @click="openLightbox(item)"
                                class="snap-start shrink-0 w-[260px] sm:w-[320px] aspect-video relative rounded-2xl overflow-hidden border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-300 shadow-md group cursor-pointer bg-slate-900/60 backdrop-blur-md"
                            >
                                <!-- ── PHOTO ── -->
                                <template v-if="getPlatform(item) === 'photo'">
                                    <img
                                        :src="getThumbnail(item)"
                                        :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <p class="text-[9px] font-bold text-white truncate text-center">{{ item.title }}</p>
                                    </div>
                                </template>

                                <!-- ── YOUTUBE thumbnail ── -->
                                <template v-else-if="getPlatform(item) === 'youtube'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="YouTube"
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-tr from-slate-900 to-red-950/30 flex items-center justify-center">
                                        <svg class="h-10 w-10 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 00-2.1 2.1C0 8 0 12 0 12s0 4 .5 5.8a3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1C24 16 24 12 24 12s0-4-.5-5.8zm-14 9.4V8.4l6.3 3.6-6.3 3.6z"/></svg>
                                    </div>
                                    <!-- Play overlay -->
                                    <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                                        <div :class="['w-10 h-10 rounded-full bg-gradient-to-r flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300', tc.play_circle, tc.play_circle_shadow]">
                                            <svg class="h-5 w-5 fill-slate-950 translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <!-- Platform badge -->
                                    <div class="absolute top-2 right-2 bg-red-600/90 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">YT</div>
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                        <p class="text-[9px] font-bold text-white truncate">{{ item.title }}</p>
                                    </div>
                                </template>

                                <!-- ── FACEBOOK video cover ── -->
                                <template v-else-if="getPlatform(item) === 'facebook'">
                                    <img
                                        v-if="getThumbnail(item)"
                                        :src="getThumbnail(item)"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="Facebook Video"
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-tr from-blue-950/60 to-slate-900 flex flex-col items-center justify-center gap-3 p-4">
                                        <svg class="h-10 w-10 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        <p class="text-[9px] text-slate-300 font-semibold text-center truncate w-full">{{ item.title || 'Video de Facebook' }}</p>
                                    </div>
                                    <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-[#1877F2]/80 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                                            <svg class="h-5 w-5 fill-white translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <div class="absolute top-2 right-2 bg-[#1877F2]/90 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">FB</div>
                                    <div v-if="getThumbnail(item)" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                        <p class="text-[9px] font-bold text-white truncate">{{ item.title || 'Video de Facebook' }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <!-- ─── LIGHTBOX MODAL ──────────────────────────────────────────────────── -->
        <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
        <div
            v-if="activeLightboxItem"
            class="fixed inset-0 bg-black/92 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
            @click="closeLightbox"
        >
            <!-- Close Button -->
            <button
                @click.stop="closeLightbox"
                class="absolute top-5 right-5 text-white/70 hover:text-white bg-slate-900/70 hover:bg-slate-800 border border-slate-700/50 p-2.5 rounded-full transition-all duration-300 hover:scale-105 z-10 flex items-center justify-center text-base font-black cursor-pointer shadow-xl"
                title="Cerrar"
            >✕</button>

            <!-- Lightbox inner panel (click inside won't close) -->
            <div
                :class="[
                    'w-full flex items-center justify-center',
                    activeLightboxItem && (getPlatform(activeLightboxItem) === 'tiktok' || getPlatform(activeLightboxItem) === 'instagram')
                        ? 'max-h-[85vh] max-w-xs'
                        : 'max-h-[85vh] max-w-4xl'
                ]"
                @click.stop
            >
                <!-- ── PHOTO ── -->
                <img
                    v-if="getPlatform(activeLightboxItem) === 'photo'"
                    :src="getThumbnail(activeLightboxItem)"
                    :alt="activeLightboxItem.title"
                    :class="['max-w-full max-h-[82vh] object-contain rounded-2xl border shadow-2xl', tc.lightbox_border]"
                />

                <!-- ── YOUTUBE iframe ── -->
                <iframe
                    v-else-if="getPlatform(activeLightboxItem) === 'youtube'"
                    :src="getEmbedUrl(activeLightboxItem, true)"
                    :class="['w-full aspect-video rounded-2xl border shadow-2xl max-h-[80vh]', tc.lightbox_border]"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>

                <!-- ── FACEBOOK redirect card ── -->
                <div
                    v-else-if="getPlatform(activeLightboxItem) === 'facebook'"
                    :class="['w-full max-w-sm rounded-2xl border overflow-hidden shadow-2xl bg-slate-900/80 backdrop-blur-md', tc.lightbox_border]"
                >
                    <div class="flex flex-col items-center justify-center p-8 gap-5">
                        <!-- Facebook logo -->
                        <div class="w-20 h-20 rounded-2xl bg-[#1877F2] flex items-center justify-center shadow-[0_0_30px_rgba(24,119,242,0.5)]">
                            <svg class="h-10 w-10 fill-white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-bold text-sm mb-1">{{ activeLightboxItem.title || 'Video de Facebook' }}</p>
                            <p class="text-slate-400 text-xs">Los videos de Facebook se reproducen en su plataforma original.</p>
                        </div>
                        <a
                            :href="activeLightboxItem.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white text-xs font-black uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:scale-[1.02]"
                        >
                            <svg class="h-4 w-4 fill-white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Ver en Facebook
                        </a>
                    </div>
                </div>

                <!-- ── INSTAGRAM redirect card ── -->
                <div
                    v-else-if="getPlatform(activeLightboxItem) === 'instagram'"
                    :class="['w-full max-w-sm rounded-2xl border overflow-hidden shadow-2xl bg-slate-900/80 backdrop-blur-md', tc.lightbox_border]"
                >
                    <div class="flex flex-col items-center justify-center p-8 gap-5">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-orange-500 via-pink-600 to-purple-700 flex items-center justify-center shadow-[0_0_30px_rgba(236,72,153,0.4)]">
                            <svg class="h-10 w-10 fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-bold text-sm mb-1">{{ activeLightboxItem.title || 'Reel de Instagram' }}</p>
                            <p class="text-slate-400 text-xs">Los Reels de Instagram se reproducen en su plataforma original.</p>
                        </div>
                        <a
                            :href="activeLightboxItem.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-pink-600 hover:from-orange-400 hover:to-pink-500 text-white text-xs font-black uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:scale-[1.02]"
                        >
                            <svg class="h-4 w-4 fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                            Ver en Instagram
                        </a>
                    </div>
                </div>

                <!-- ── TIKTOK iframe embed (oficial embed/v2) ── -->
                <div
                    v-else-if="getPlatform(activeLightboxItem) === 'tiktok'"
                    :class="['rounded-2xl border overflow-hidden shadow-2xl bg-slate-950', tc.lightbox_border]"
                    style="width:325px; height:580px;"
                >
                    <iframe
                        v-if="getTikTokId(activeLightboxItem.url)"
                        :src="getEmbedUrl(activeLightboxItem)"
                        style="width:325px; height:580px;"
                        frameborder="0"
                        allow="fullscreen"
                        allowfullscreen
                        scrolling="no"
                    ></iframe>
                    <!-- Fallback if no video ID detected -->
                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-4 p-6">
                        <svg class="h-12 w-12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-1.02-.1z" fill="#69C9D0"/>
                            <path d="M15.82 2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.19 8.19 0 004.79 1.54V6.79a4.85 4.85 0 01-3.79-4.79z" fill="#EE1D52"/>
                        </svg>
                        <a :href="activeLightboxItem.url" target="_blank" rel="noopener noreferrer"
                           class="bg-slate-800 border border-[#EE1D52]/40 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-slate-700 transition-all">
                            Ver en TikTok
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </Transition>
        </Teleport>
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
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
