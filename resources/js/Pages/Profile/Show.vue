<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
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
        // Hero elements (adaptive light/dark)
        instrument_bg:    'bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-300 border border-pink-200 dark:border-pink-500/20',
        zone_bg:          'bg-cyan-50/80 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/20',
        name_gradient:    'from-cyan-500 to-blue-600 dark:from-cyan-400 dark:to-cyan-200',
        // WhatsApp CTA button
        cta_btn:          'from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white shadow-[0_8px_25px_rgba(6,182,212,0.35)]',
        // Social icons
        social_icon:      'hover:border-cyan-500/40 hover:bg-cyan-50 dark:hover:bg-cyan-500/5',
        // Section titles
        section_accent:   'bg-cyan-500',
        section_title:    'text-cyan-600 dark:text-cyan-400',
        // Widget card borders
        widget_card:      'border-slate-200/80 dark:border-cyan-500/10 hover:border-cyan-500/30',
        // Events date badge
        event_badge:      'from-cyan-600 to-blue-700',
        // "Adquirir Accesos" CTA
        event_cta:        'hover:border-cyan-500/50 hover:bg-cyan-950/20 text-cyan-500 dark:text-cyan-400',
        // Vinyl / player
        vinyl:            'from-cyan-600 to-blue-700',
        vinyl_center:     'from-cyan-500 to-cyan-300',
        play_btn:         'from-cyan-500 to-cyan-400 hover:from-cyan-400 hover:to-cyan-300 shadow-[0_0_20px_rgba(6,182,212,0.45)]',
        progress_bar:     'from-cyan-500 to-cyan-300 shadow-[0_0_10px_rgba(6,182,212,0.8)]',
        spotify_link:     'text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300',
        // Gallery play circle
        play_circle:      'from-cyan-500 to-cyan-400',
        // Lightbox border
        lightbox_border:  'border-cyan-500/20 shadow-[0_0_50px_rgba(6,182,212,0.35)]',
        // Ambient glows
        glow_a:           'bg-cyan-400/20 dark:bg-purple-900/10',
        glow_b:           'bg-blue-400/20 dark:bg-pink-900/10',
        stage_a:          'bg-cyan-500/20 dark:bg-purple-600/10',
        stage_b:          'bg-blue-500/20 dark:bg-pink-600/10',
        hero_aura:        'from-cyan-500/30 to-blue-600/20',
        // Custom theme additions
        edit_btn:         'text-cyan-600 dark:text-cyan-400 hover:border-cyan-500/50',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(6,182,212,0.5)]',
        placeholder_avatar: 'from-cyan-500 to-blue-500 shadow-[0_0_35px_rgba(6,182,212,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(6,182,212,0.4)]',
        photo_destello:   'bg-gradient-to-t from-cyan-500 via-cyan-400 to-blue-500',
        photo_color_shadow: 'from-cyan-500/30 via-cyan-500/10 to-transparent',
        photo_tint:       'from-cyan-500/15 via-transparent to-blue-500/10',
    },
    'cyber-purple': {
        instrument_bg:    'bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-300 border border-pink-200 dark:border-pink-500/20',
        zone_bg:          'bg-purple-50/80 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-500/20',
        name_gradient:    'from-purple-600 to-pink-500 dark:from-purple-400 dark:to-pink-400',
        cta_btn:          'from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white shadow-[0_8px_25px_rgba(168,85,247,0.35)]',
        social_icon:      'hover:border-purple-500/40 hover:bg-purple-50 dark:hover:bg-purple-500/5',
        section_accent:   'bg-purple-500',
        section_title:    'text-purple-600 dark:text-purple-400',
        widget_card:      'border-slate-200/80 dark:border-purple-500/10 hover:border-purple-500/30',
        event_badge:      'from-purple-600 to-pink-600',
        event_cta:        'hover:border-purple-500/50 hover:bg-purple-950/20 text-purple-500 dark:text-purple-400',
        vinyl:            'from-purple-600 to-pink-600',
        vinyl_center:     'from-purple-500 to-pink-500',
        play_btn:         'from-purple-500 to-pink-500 hover:from-purple-400 hover:to-pink-400 shadow-[0_0_20px_rgba(168,85,247,0.45)]',
        progress_bar:     'from-purple-500 to-pink-500 shadow-[0_0_10px_rgba(168,85,247,0.8)]',
        spotify_link:     'text-pink-600 dark:text-pink-400 hover:text-pink-500 dark:hover:text-pink-300',
        play_circle:      'from-purple-500 to-pink-500',
        lightbox_border:  'border-purple-500/20 shadow-[0_0_50px_rgba(168,85,247,0.35)]',
        glow_a:           'bg-purple-400/20 dark:bg-purple-900/10',
        glow_b:           'bg-pink-400/20 dark:bg-pink-900/10',
        stage_a:          'bg-purple-500/20 dark:bg-purple-600/10',
        stage_b:          'bg-pink-500/20 dark:bg-pink-600/10',
        hero_aura:        'from-purple-600/30 to-pink-500/20',
        edit_btn:         'text-purple-600 dark:text-purple-400 hover:border-purple-500/50',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(168,85,247,0.5)]',
        placeholder_avatar: 'from-purple-500 to-pink-500 shadow-[0_0_35px_rgba(168,85,247,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(168,85,247,0.4)]',
        photo_destello:   'bg-gradient-to-t from-purple-600 via-purple-500 to-pink-500',
        photo_color_shadow: 'from-purple-600/30 via-purple-500/10 to-transparent',
        photo_tint:       'from-purple-500/15 via-transparent to-pink-500/10',
    },
    'volt-orange': {
        instrument_bg:    'bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-300 border border-orange-200 dark:border-orange-500/20',
        zone_bg:          'bg-amber-50/80 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/20',
        name_gradient:    'from-orange-500 to-amber-500 dark:from-orange-400 dark:to-amber-300',
        cta_btn:          'from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white shadow-[0_8px_25px_rgba(249,115,22,0.35)]',
        social_icon:      'hover:border-orange-500/40 hover:bg-orange-50 dark:hover:bg-orange-500/5',
        section_accent:   'bg-orange-500',
        section_title:    'text-orange-500 dark:text-orange-400',
        widget_card:      'border-slate-200/80 dark:border-orange-500/10 hover:border-orange-500/30',
        event_badge:      'from-orange-600 to-amber-600',
        event_cta:        'hover:border-orange-500/50 hover:bg-orange-950/20 text-orange-500 dark:text-orange-400',
        vinyl:            'from-orange-600 to-amber-600',
        vinyl_center:     'from-orange-500 to-amber-400',
        play_btn:         'from-orange-500 to-amber-400 hover:from-orange-400 hover:to-amber-300 shadow-[0_0_20px_rgba(249,115,22,0.45)]',
        progress_bar:     'from-orange-500 to-amber-400 shadow-[0_0_10px_rgba(249,115,22,0.8)]',
        spotify_link:     'text-orange-600 dark:text-orange-400 hover:text-orange-500 dark:hover:text-orange-300',
        play_circle:      'from-orange-500 to-amber-400',
        lightbox_border:  'border-orange-500/20 shadow-[0_0_50px_rgba(249,115,22,0.35)]',
        glow_a:           'bg-orange-400/20 dark:bg-orange-900/10',
        glow_b:           'bg-amber-400/20 dark:bg-amber-900/10',
        stage_a:          'bg-orange-500/20 dark:bg-orange-600/10',
        stage_b:          'bg-amber-500/20 dark:bg-amber-600/10',
        hero_aura:        'from-orange-600/30 to-amber-500/20',
        edit_btn:         'text-orange-500 dark:text-orange-400 hover:border-orange-500/50',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(249,115,22,0.5)]',
        placeholder_avatar: 'from-orange-500 to-amber-500 shadow-[0_0_35px_rgba(249,115,22,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(249,115,22,0.4)]',
        photo_destello:   'bg-gradient-to-t from-orange-500 via-orange-400 to-amber-500',
        photo_color_shadow: 'from-orange-500/30 via-orange-400/10 to-transparent',
        photo_tint:       'from-orange-500/15 via-transparent to-amber-400/10',
    },
    'electric-red': {
        instrument_bg:    'bg-rose-50 dark:bg-red-500/10 text-rose-600 dark:text-red-300 border border-rose-200 dark:border-red-500/20',
        zone_bg:          'bg-rose-50/60 dark:bg-red-500/10 text-rose-600 dark:text-red-400 border border-rose-200 dark:border-red-500/20',
        name_gradient:    'from-red-500 to-orange-500',
        cta_btn:          'from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white shadow-[0_8px_25px_rgba(239,68,68,0.35)]',
        social_icon:      'hover:border-red-500/40 hover:bg-rose-50 dark:hover:bg-red-500/5',
        section_accent:   'bg-red-500',
        section_title:    'text-red-500',
        widget_card:      'border-slate-200/80 dark:border-red-500/10 hover:border-red-500/30',
        event_badge:      'from-red-600 to-orange-500',
        event_cta:        'hover:border-red-500/50 hover:bg-red-950/20 text-red-500 dark:text-red-400',
        vinyl:            'from-red-600 to-orange-600',
        vinyl_center:     'from-red-500 to-orange-500',
        play_btn:         'from-red-500 to-orange-500 hover:from-red-400 hover:to-orange-400 shadow-[0_0_20px_rgba(239,68,68,0.45)]',
        progress_bar:     'from-red-500 to-orange-500 shadow-[0_0_10px_rgba(239,68,68,0.8)]',
        spotify_link:     'text-red-500 dark:text-red-400 hover:text-red-400 dark:hover:text-red-300',
        play_circle:      'from-red-500 to-orange-500',
        lightbox_border:  'border-red-500/20 shadow-[0_0_50px_rgba(239,68,68,0.35)]',
        glow_a:           'bg-red-400/20 dark:bg-red-900/10',
        glow_b:           'bg-orange-400/20 dark:bg-orange-900/10',
        stage_a:          'bg-red-500/20 dark:bg-red-600/10',
        stage_b:          'bg-orange-500/20 dark:bg-orange-600/10',
        hero_aura:        'from-red-600/30 to-orange-500/20',
        edit_btn:         'text-red-500 dark:text-red-400 hover:border-red-500/50',
        play_circle_shadow: 'shadow-[0_0_15px_rgba(239,68,68,0.5)]',
        placeholder_avatar: 'from-red-500 to-orange-500 shadow-[0_0_35px_rgba(239,68,68,0.5)]',
        vinyl_shadow:     'shadow-[0_0_20px_rgba(239,68,68,0.4)]',
        photo_destello:   'bg-gradient-to-t from-red-600 via-red-500 to-orange-500',
        photo_color_shadow: 'from-red-600/35 via-red-500/12 to-transparent',
        photo_tint:       'from-red-500/15 via-transparent to-orange-500/10',
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
    if (/facebook\.com|fb\.watch|fb\.gg|fb\.me/.test(url)) return 'facebook';
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

// Determine if a media item is a vertical video format (TikTok / Instagram Reels / Facebook Videos)
const isVerticalVideo = (item) => {
    const platform = getPlatform(item);
    return platform === 'tiktok' || platform === 'instagram' || platform === 'facebook';
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

// Extract Instagram shortcode from URL (supports mobile app share links)
const getInstagramShortcode = (url) => {
    if (!url) return null;
    // Matches https://www.instagram.com/reel/C123abc/ or /p/C123abc/ or /reels/C123abc/ or /share/reel/C123abc/ or /tv/C123abc/
    const match = url.match(/\/(?:p|reel|reels|share\/reel|tv)\/([A-Za-z0-9_-]+)/i);
    return match ? match[1] : null;
};

// Get Instagram embed URL
const getInstagramEmbedUrl = (url) => {
    const shortcode = getInstagramShortcode(url);
    return shortcode ? `https://www.instagram.com/p/${shortcode}/embed` : null;
};

// Get Facebook embed URL (supports mobile app share links, fb.watch, m.facebook.com)
const getFacebookEmbedUrl = (url) => {
    if (!url) return null;
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
                    // Mobile Reel share -> convert to standard reel URL
                    cleanUrl = `https://www.facebook.com/reel/${id}/`;
                } else {
                    // Mobile video/post share -> clean share URL without query params
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
            // General cleanup: remove tracking query params like ?mibextid=... &rdid=...
            urlObj.search = '';
            cleanUrl = urlObj.toString();
        }
    } catch (e) {
        // Use raw url if parsing fails
    }

    return `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(cleanUrl)}&show_text=false&width=325`;
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
            return getFacebookEmbedUrl(url);
        case 'instagram':
            return getInstagramEmbedUrl(url);
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

// Carousel state for events
const currentEventIndex = ref(0);

// Calculate how many events to show per slide based on screen size
const eventsPerSlide = ref(2); // Default for desktop

// Update events per slide based on screen width
const updateEventsPerSlide = () => {
    if (typeof window !== 'undefined') {
        eventsPerSlide.value = window.innerWidth < 768 ? 1 : 2; // 768px is Tailwind's 'md' breakpoint
        // Reset to first slide when changing layout
        currentEventIndex.value = 0;
    }
};

// Setup resize listener and customization parameter check
onMounted(() => {
    updateEventsPerSlide();
    window.addEventListener('resize', updateEventsPerSlide);
    if (typeof window !== 'undefined') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('customize') === '1' && isOwner.value) {
            isCustomizing.value = true;
        }
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', updateEventsPerSlide);
});

// Create slides grouping events
const eventSlides = computed(() => {
    const slides = [];
    const events = upcomingEvents.value;
    const perSlide = eventsPerSlide.value;
    
    for (let i = 0; i < events.length; i += perSlide) {
        slides.push(events.slice(i, i + perSlide));
    }
    
    return slides;
});

// Calculate total slides
const totalEventSlides = computed(() => {
    return eventSlides.value.length;
});

// Get events for current slide (kept for compatibility but now using eventSlides)
const visibleEvents = computed(() => {
    return eventSlides.value[currentEventIndex.value] || [];
});

// Navigate carousel
const nextEvents = () => {
    if (currentEventIndex.value < totalEventSlides.value - 1) {
        currentEventIndex.value++;
    } else {
        currentEventIndex.value = 0; // Loop back to start
    }
};

const prevEvents = () => {
    if (currentEventIndex.value > 0) {
        currentEventIndex.value--;
    } else {
        currentEventIndex.value = totalEventSlides.value - 1; // Loop to end
    }
};

// Go to specific slide
const goToEventSlide = (index) => {
    currentEventIndex.value = index;
};

// Media categorized by format
const verticalMedia = computed(() => {
    return (props.profile.media || []).filter(item => {
        if (item.type === 'photo') return false;
        const platform = getPlatform(item);
        return platform === 'tiktok' || platform === 'instagram' || platform === 'facebook';
    });
});

const horizontalVideos = computed(() => {
    return (props.profile.media || []).filter(item => {
        if (item.type === 'photo') return false;
        const platform = getPlatform(item);
        return platform === 'youtube' || platform === 'vimeo';
    });
});

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

// Mock Music Player state (used as fallback)
const isMusicPlaying = ref(false);
const toggleMusicPlay = () => {
    isMusicPlaying.value = !isMusicPlaying.value;
};

// ─── Spotify Official Embed Resolution ─────────────────────────────────────────
const spotifyEmbedInfo = computed(() => {
    const rawUrl = props.profile.widget_status?.spotify;
    if (!rawUrl || typeof rawUrl !== 'string') return null;
    const cleanUrl = rawUrl.trim();

    // 1. Spotify URI format: spotify:(track|artist|album|playlist|episode|show):ID
    const uriMatch = cleanUrl.match(/^spotify:(track|artist|album|playlist|episode|show):([A-Za-z0-9]+)/i);
    if (uriMatch) {
        const type = uriMatch[1].toLowerCase();
        const id = uriMatch[2];
        return {
            url: `https://open.spotify.com/embed/${type}/${id}?utm_source=generator&theme=0`,
            type,
            height: type === 'track' || type === 'episode' ? 152 : 352,
        };
    }

    // 2. Standard or international web URL: open.spotify.com/(intl-xx/)?(track|artist|album|playlist|episode|show)/ID
    const urlMatch = cleanUrl.match(/open\.spotify\.com\/(?:intl-[a-z]{2}\/)?(track|artist|album|playlist|episode|show)\/([A-Za-z0-9]+)/i);
    if (urlMatch) {
        const type = urlMatch[1].toLowerCase();
        const id = urlMatch[2];
        return {
            url: `https://open.spotify.com/embed/${type}/${id}?utm_source=generator&theme=0`,
            type,
            height: type === 'track' || type === 'episode' ? 152 : 352,
        };
    }

    // 3. Direct embed URL
    if (cleanUrl.includes('open.spotify.com/embed/')) {
        const isCompact = cleanUrl.includes('/track/') || cleanUrl.includes('/episode/');
        return {
            url: cleanUrl,
            type: isCompact ? 'track' : 'artist',
            height: isCompact ? 152 : 352,
        };
    }

    return null;
});

// ─── Bento Grid Modular Layout System ─────────────────────────────────────────
const CARD_DEFINITIONS = {
    music: {
        id: 'music',
        title: 'Música Destacada',
        icon: '🎵',
        subtitle: 'Spotify Player',
    },
    calendar: {
        id: 'calendar',
        title: 'Próximas Presentaciones',
        icon: '📅',
        subtitle: 'Fechas y Disponibilidad',
    },
    social_videos: {
        id: 'social_videos',
        title: 'Reels & TikToks',
        icon: '📱',
        subtitle: 'Videos Verticales (9:16)',
    },
    youtube_videos: {
        id: 'youtube_videos',
        title: 'Videos & En Vivo',
        icon: '🎬',
        subtitle: 'YouTube y Conciertos (16:9)',
    },
    photos: {
        id: 'photos',
        title: 'Galería Fotográfica',
        icon: '📸',
        subtitle: 'Fotos de Promoción',
    },
};

const DEFAULT_CARDS = [
    { id: 'music', width: '1/2', enabled: true },
    { id: 'calendar', width: '1/2', enabled: true },
    { id: 'social_videos', width: '1/2', enabled: true },
    { id: 'youtube_videos', width: '1/2', enabled: true },
    { id: 'photos', width: 'full', enabled: true },
];

const initializeLayout = () => {
    const saved = props.profile.widget_status?.layout;
    if (Array.isArray(saved) && saved.length > 0) {
        const savedIds = new Set(saved.map(c => c.id));
        const merged = saved.map(c => ({
            id: c.id,
            width: c.width || '1/2',
            enabled: c.enabled !== false,
        }));
        DEFAULT_CARDS.forEach(def => {
            if (!savedIds.has(def.id)) {
                merged.push({ ...def });
            }
        });
        return merged;
    }
    return DEFAULT_CARDS.map(c => ({ ...c }));
};

const currentCards = ref(initializeLayout());
const isCustomizing = ref(false);
const isSavingLayout = ref(false);

const toggleCustomizing = () => {
    isCustomizing.value = !isCustomizing.value;
};

const getCardDef = (id) => CARD_DEFINITIONS[id] || { id, title: id, icon: '📦', subtitle: '' };

const getWidthLabel = (width) => {
    switch (width) {
        case '1/3': return '1/3';
        case '1/2': return '1/2';
        case '2/3': return '2/3';
        case 'full': return '100%';
        default: return width;
    }
};

const getCardGridClass = (width) => {
    switch (width) {
        case '1/3': return 'col-span-12 md:col-span-6 lg:col-span-4';
        case '1/2': return 'col-span-12 lg:col-span-6';
        case '2/3': return 'col-span-12 lg:col-span-8';
        case 'full':
        default: return 'col-span-12';
    }
};

const cardHasContent = (cardId) => {
    switch (cardId) {
        case 'music':
            return Boolean(props.profile.widget_status?.spotify);
        case 'calendar':
            return Boolean(props.profile.widget_status?.agenda !== false && upcomingEvents.value.length > 0);
        case 'social_videos':
            return verticalMedia.value.length > 0;
        case 'youtube_videos':
            return horizontalVideos.value.length > 0;
        case 'photos':
            return photos.value.length > 0;
        default:
            return false;
    }
};

const cardShouldRender = (card) => {
    if (isCustomizing.value) return true;
    return card.enabled !== false && cardHasContent(card.id);
};

const moveCard = (index, direction) => {
    const target = index + direction;
    if (target < 0 || target >= currentCards.value.length) return;
    const cards = [...currentCards.value];
    const item = cards.splice(index, 1)[0];
    cards.splice(target, 0, item);
    currentCards.value = cards;
};

const setCardWidth = (cardId, width) => {
    const card = currentCards.value.find(c => c.id === cardId);
    if (card) {
        card.width = width;
    }
};

const toggleCardEnabled = (cardId) => {
    const card = currentCards.value.find(c => c.id === cardId);
    if (card) {
        card.enabled = !card.enabled;
    }
};

// Drag & drop support
const draggedCardIndex = ref(null);
const onCardDragStart = (index, e) => {
    draggedCardIndex.value = index;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
    }
};
const onCardDragOver = (e) => {
    e.preventDefault();
    if (e.dataTransfer) {
        e.dataTransfer.dropEffect = 'move';
    }
};
const onCardDrop = (targetIndex) => {
    if (draggedCardIndex.value === null || draggedCardIndex.value === targetIndex) return;
    const cards = [...currentCards.value];
    const moved = cards.splice(draggedCardIndex.value, 1)[0];
    cards.splice(targetIndex, 0, moved);
    currentCards.value = cards;
    draggedCardIndex.value = null;
};

const saveLayout = () => {
    isSavingLayout.value = true;
    router.patch(route('dashboard.tpv.layout'), {
        layout: currentCards.value.map(c => ({
            id: c.id,
            width: c.width || '1/2',
            enabled: c.enabled !== false,
        }))
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isSavingLayout.value = false;
            isCustomizing.value = false;
        },
        onError: () => {
            isSavingLayout.value = false;
        }
    });
};

const resetDefaultLayout = () => {
    currentCards.value = DEFAULT_CARDS.map(c => ({ ...c }));
};
</script>

<template>
    <Head :title="`${profile.name} - Tarjeta de Presentación Virtual`" />

    <div class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased overflow-x-hidden relative selection:bg-purple-500 selection:text-white">
        
        <!-- Reusable Global Navbar -->
        <Navbar />

        <!-- Conditional Owner Action Buttons (Edit Profile & Customize Cards Layout) -->
        <div v-if="isOwner" class="absolute top-20 right-4 z-40 sm:right-6 lg:right-8 flex items-center gap-2">
            <button
                @click="toggleCustomizing"
                :class="[
                    'tpv-btn-customize border px-3.5 py-2.5 rounded-xl text-xs font-black flex items-center gap-1.5 transition-all duration-300 hover:scale-105 cursor-pointer shadow-md justify-center select-none',
                    isCustomizing 
                        ? 'bg-cyan-500 text-slate-950 border-cyan-400 font-extrabold shadow-[0_0_20px_rgba(6,182,212,0.5)]' 
                        : 'bg-slate-900/90 hover:bg-slate-800 border-slate-800 text-cyan-400 hover:border-cyan-500/50'
                ]"
                :title="isCustomizing ? 'Cerrar modo personalización' : 'Organizar y redimensionar tarjetas'"
            >
                <span>{{ isCustomizing ? '✕ Salir' : '🎨 Personalizar Tarjetas' }}</span>
            </button>
            <button
                @click="goToEdit"
                :class="['tpv-btn-edit bg-slate-900/90 hover:bg-slate-800 border border-slate-800 px-4 py-2.5 rounded-xl text-xs font-black flex items-center gap-1.5 transition-all duration-300 hover:scale-105 cursor-pointer shadow-md justify-center', tc.edit_btn]"
            >
                <span>✏️</span> Editar Perfil
            </button>
        </div>

        <!-- Floating Sticky Toolbar when Customizing Layout -->
        <div v-if="isOwner && isCustomizing" class="fixed bottom-6 inset-x-4 max-w-2xl mx-auto z-50 bg-slate-900/95 backdrop-blur-xl border border-cyan-500/40 rounded-2xl p-4 shadow-[0_10px_40px_rgba(0,0,0,0.8)] flex flex-wrap items-center justify-between gap-3 animate-bounce-in">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                <div>
                    <p class="text-xs font-extrabold text-white">Modo de Organización de Tarjetas</p>
                    <p class="text-[11px] text-slate-400">Reordena con ⬅️ ➡️ y ajusta el ancho de cada tarjeta (1/3, 1/2, 2/3, Full).</p>
                </div>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <button
                    @click="resetDefaultLayout"
                    class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 transition-colors"
                >
                    Restablecer
                </button>
                <button
                    @click="saveLayout"
                    :disabled="isSavingLayout"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-black text-xs shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all hover:scale-105 flex items-center gap-1.5 cursor-pointer"
                >
                    <span v-if="isSavingLayout">Guardando...</span>
                    <span v-else>💾 Guardar Distribución</span>
                </button>
            </div>
        </div>

        <!-- Ambient Decorative Glowing Blobs (theme-driven) -->
        <div :class="['tpv-glow-left absolute top-[10%] left-[-10%] w-[60vw] h-[60vw] rounded-full blur-[130px] pointer-events-none z-0', tc.glow_a]"></div>
        <div :class="['tpv-glow-right absolute bottom-[10%] right-[-10%] w-[70vw] h-[70vw] rounded-full blur-[140px] pointer-events-none z-0', tc.glow_b]"></div>

        <!-- Stage Lights (theme-driven) -->
        <div :class="['tpv-stage-a absolute top-10 right-10 w-[450px] h-[450px] rounded-full blur-[140px] pointer-events-none z-0', tc.stage_a]"></div>
        <div :class="['tpv-stage-b absolute bottom-20 left-10 w-[500px] h-[500px] rounded-full blur-[140px] pointer-events-none z-0', tc.stage_b]"></div>

        <!-- Mobile Profile Photo Hero (Visible only on Mobile) -->
        <div v-if="mainPhoto" class="lg:hidden w-full h-[38vh] sm:h-[46vh] relative overflow-hidden z-0 bg-slate-100 dark:bg-slate-950 transition-colors duration-500">
            <img
                :src="mainPhoto"
                :alt="profile.name"
                class="w-full h-full object-cover object-center"
            />

            <!-- Destello de color atmosférico del tema (tenue y difuminado para proteger legibilidad) -->
            <div :class="['tpv-photo-destello-mobile absolute -bottom-4 inset-x-0 h-36 sm:h-44 rounded-full blur-[70px] pointer-events-none opacity-40 dark:opacity-45 transition-all duration-700 z-10', tc.photo_destello]"></div>

            <!-- Sombra de color temática tenue ascendente -->
            <div :class="['tpv-photo-color-shadow absolute inset-x-0 bottom-0 h-44 sm:h-52 bg-gradient-to-t pointer-events-none transition-all duration-500 z-10', tc.photo_color_shadow]"></div>

            <!-- Sutil difuminado de integración de borde inferior -->
            <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-slate-50/70 dark:from-slate-950/70 to-transparent pointer-events-none z-10"></div>
        </div>

        <!-- Main Responsive Content Wrapper -->
        <div :class="['max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col gap-6 sm:gap-10 lg:gap-16 pb-12 lg:py-16', mainPhoto ? '-mt-24 sm:-mt-28 lg:mt-0 pt-0 tpv-has-mobile-hero' : 'pt-6 lg:pt-16']">
            
            <!-- 1. HERO SUPERIOR SECTION (Splitted text left / glowing photo cover right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center min-h-0 lg:min-h-[75vh]">
                
                <!-- HERO LEFT: Floating Name, bio & contact links (No box, no borders) -->
                <div class="lg:col-span-7 flex flex-col gap-3.5 sm:gap-5 lg:gap-6 justify-center">

                    <!-- 1. NOMBRE -->
                    <div class="flex flex-col leading-none">
                        <h1 class="tpv-profile-name-first text-white font-black text-5xl sm:text-7xl lg:text-8xl tracking-wider uppercase leading-none drop-shadow-[0_4px_16px_rgba(0,0,0,0.65)]">
                            {{ nameParts.first }}
                        </h1>
                        <span v-if="nameParts.rest" :class="['tpv-profile-name-rest bg-gradient-to-r text-transparent bg-clip-text text-5xl sm:text-7xl lg:text-8xl font-black uppercase leading-none mt-1 sm:mt-2 drop-shadow-[0_4px_16px_rgba(0,0,0,0.6)]', tc.name_gradient]">
                            {{ nameParts.rest }}
                        </span>
                    </div>

                    <!-- 2. TIPO DE MÚSICO, 3. INSTRUMENTO, 4. REGIÓN (Orden descendente en móvil) -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:items-center gap-2 sm:gap-3">
                        <!-- Tipo de Músico (Géneros / Estilos) -->
                        <div v-if="(Array.isArray(profile.genres) && profile.genres.length > 0) || (profile.genres && !Array.isArray(profile.genres))" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <template v-if="Array.isArray(profile.genres)">
                                <span
                                    v-for="genre in profile.genres"
                                    :key="genre"
                                    :class="['tpv-tag-genre px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                                >
                                    🎸 {{ genre }}
                                </span>
                            </template>
                            <span
                                v-else
                                :class="['tpv-tag-genre px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                            >
                                🎸 {{ profile.genres }}
                            </span>
                        </div>

                        <!-- Instrumento -->
                        <div v-if="(Array.isArray(profile.instruments) && profile.instruments.length > 0) || (profile.instruments && !Array.isArray(profile.instruments))" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <template v-if="Array.isArray(profile.instruments)">
                                <span
                                    v-for="inst in profile.instruments"
                                    :key="inst"
                                    :class="['tpv-tag-instrument px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm', tc.instrument_bg]"
                                >
                                    {{ inst }}
                                </span>
                            </template>
                            <span
                                v-else
                                :class="['tpv-tag-instrument px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm', tc.instrument_bg]"
                            >
                                {{ profile.instruments }}
                            </span>
                        </div>

                        <!-- Región -->
                        <div v-if="(Array.isArray(profile.coverage_area) && profile.coverage_area.length > 0) || (profile.coverage_area && !Array.isArray(profile.coverage_area))" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <template v-if="Array.isArray(profile.coverage_area)">
                                <span
                                    v-for="zone in profile.coverage_area"
                                    :key="zone"
                                    :class="['tpv-tag-zone px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                                >
                                    📍 {{ zone }}
                                </span>
                            </template>
                            <span
                                v-else
                                :class="['tpv-tag-zone px-3 py-1 rounded-full text-xs font-semibold shadow-sm', tc.zone_bg]"
                            >
                                📍 {{ profile.coverage_area }}
                            </span>
                        </div>
                    </div>

                    <!-- 5. DESCRIPCIÓN (Bio) -->
                    <div v-if="profile.bio" class="tpv-bio text-slate-300 text-base md:text-lg leading-relaxed max-w-2xl mt-0.5 sm:mt-1">
                        <p class="whitespace-pre-line font-medium">{{ profile.bio }}</p>
                    </div>

                    <!-- Contact & Aligned Socials block (Stacked vertically) -->
                    <div class="flex flex-col items-start gap-3 sm:gap-4 mt-2 sm:mt-4">
                        <!-- WhatsApp button with glowing shadow -->
                        <a
                            :href="getWhatsAppUrl()"
                            target="_blank"
                            :class="['tpv-whatsapp-btn w-full sm:w-auto bg-gradient-to-r text-white font-extrabold py-3.5 px-6 sm:py-4 sm:px-8 rounded-2xl flex items-center justify-center gap-2.5 transition-all duration-300 hover:scale-[1.03] cursor-pointer text-sm tracking-wider uppercase', tc.cta_btn]"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.588 2.001 14.156.98 11.602.98c-5.439 0-9.867 4.37-9.871 9.803-.001 1.73.461 3.42 1.336 4.908l-.994 3.633 3.734-.972zm12.355-6.852c-.3-.15-1.77-.875-2.046-.975-.276-.1-.477-.15-.677.15-.2.3-.777.975-.951 1.174-.174.2-.35.225-.65.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.487-1.777-1.663-2.074-.176-.3-.019-.461.13-.611.135-.135.3-.35.45-.525.15-.175.2-.3.3-.5s.05-.375-.025-.525-.675-1.625-.925-2.225c-.244-.589-.491-.51-.677-.52l-.576-.007c-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.112 4.525.714.309 1.272.494 1.707.632.715.227 1.365.195 1.88.117.574-.088 1.77-.725 2.02-.1425.25-.7.25-1.3.175-1.425-.076-.125-.276-.2-.576-.35z"/>
                            </svg>
                            Reservar Fecha por WhatsApp
                        </a>

                        <!-- Social Networks outline icons aligned below -->
                        <div v-if="profile.widget_status?.instagram || profile.widget_status?.spotify || profile.widget_status?.facebook || profile.widget_status?.youtube" class="flex items-center gap-3 sm:gap-4 pt-1 pb-1 sm:pb-2">
                            <!-- Instagram icon -->
                            <a
                                v-if="profile.widget_status?.instagram"
                                :href="profile.widget_status.instagram"
                                target="_blank"
                                :class="['tpv-social-icon text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['tpv-social-icon text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['tpv-social-icon text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                                :class="['tpv-social-icon text-slate-400 hover:text-white transition-colors duration-300 p-2 border border-slate-800 rounded-xl bg-slate-900/20', tc.social_icon]"
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
                    <div :class="['tpv-hero-aura absolute -inset-12 bg-gradient-to-tr blur-[130px] pointer-events-none z-0', tc.hero_aura]"></div>

                    <!-- Frameless image container with mask gradient -->
                    <div :class="['relative z-10 w-full h-full rounded-[36px] overflow-hidden bg-[#0d1527]/40 backdrop-blur-md border transition-all duration-300', tc.widget_card]">
                        <template v-if="mainPhoto">
                            <img
                                :src="mainPhoto"
                                :alt="profile.name"
                                class="w-full h-full object-cover object-center rounded-[36px]"
                            />
                            
                            <!-- Destello de color atmosférico del tema tenue (Desktop) -->
                            <div :class="['absolute -bottom-4 inset-x-0 h-40 rounded-full blur-[70px] pointer-events-none opacity-40 dark:opacity-45 transition-all duration-700 z-10', tc.photo_destello]"></div>
                            
                            <!-- Sombra de color temática tenue ascendente (Desktop) -->
                            <div :class="['absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t pointer-events-none transition-all duration-500 z-10', tc.photo_color_shadow]"></div>

                            <!-- Sutil difuminado de integración de borde inferior -->
                            <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-slate-50/70 dark:from-slate-950/70 to-transparent pointer-events-none z-10"></div>
                        </template>
                        
                        <!-- Placeholder cover if profile has no photos -->
                        <div v-else class="tpv-placeholder-card w-full h-full bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 flex flex-col items-center justify-center p-8 text-center rounded-[36px] relative overflow-hidden select-none">
                            <!-- Silhouette in Light Mode (identical to reference image) -->
                            <div class="tpv-light-silhouette absolute inset-0 hidden flex-col items-center justify-end pb-0 pointer-events-none">
                                <div class="w-32 h-32 rounded-full bg-slate-400/80 mb-2.5 shadow-sm"></div>
                                <div class="w-64 h-36 rounded-t-[120px] bg-slate-400/80"></div>
                            </div>

                            <!-- Dark Mode Default Content -->
                            <div class="tpv-dark-placeholder-content flex flex-col items-center justify-center w-full h-full">
                                <div :class="['w-24 h-24 rounded-full bg-gradient-to-tr flex items-center justify-center text-slate-950 font-black text-4xl mb-4', tc.placeholder_avatar]">
                                    {{ getInitials(profile.name) }}
                                </div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-wider">{{ profile.name }}</h3>
                                <p :class="['text-xs font-semibold mt-1', tc.section_title]">Tarjeta de Presentación Virtual</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. MODULAR BENTO GRID SECTION (Dynamic reorderable & resizable cards) -->
            <div class="grid grid-cols-12 gap-4 sm:gap-6 lg:gap-8 items-start mt-0 sm:mt-2 lg:mt-6">
                <template v-for="(card, index) in currentCards" :key="card.id">
                    <div
                        v-if="cardShouldRender(card)"
                        :class="[getCardGridClass(card.width), 'transition-all duration-350 relative']"
                        :draggable="isCustomizing"
                        @dragstart="onCardDragStart(index, $event)"
                        @dragover="onCardDragOver($event)"
                        @drop="onCardDrop(index)"
                    >
                        <!-- Card Container with Theme-Driven Styling -->
                        <section
                            :class="[
                                'tpv-card bg-[#0d1527]/40 backdrop-blur-md rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xl relative overflow-hidden group transition-all duration-300 border flex flex-col justify-between h-full',
                                tc.widget_card,
                                isCustomizing ? 'ring-2 ring-cyan-500/50 shadow-[0_0_30px_rgba(6,182,212,0.2)]' : ''
                            ]"
                        >
                            <div>
                                <!-- Customization Tool Strip on top of card -->
                                <div v-if="isCustomizing" class="mb-5 pb-3 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-2 bg-slate-950/70 p-3 rounded-2xl border border-cyan-500/20 select-none">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">{{ getCardDef(card.id).icon }}</span>
                                        <span class="text-xs font-black text-white">{{ getCardDef(card.id).title }}</span>
                                        <span class="text-[10px] font-bold text-cyan-400 px-2 py-0.5 rounded-full bg-cyan-950/60 border border-cyan-500/30">
                                            {{ getWidthLabel(card.width) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1.5 ml-auto">
                                        <!-- Move Left / Up -->
                                        <button
                                            @click.stop="moveCard(index, -1)"
                                            :disabled="index === 0"
                                            class="p-1.5 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 disabled:opacity-25 disabled:cursor-not-allowed text-xs transition-colors border border-slate-700 font-bold cursor-pointer"
                                            title="Mover antes"
                                        >
                                            ⬅️
                                        </button>
                                        <!-- Move Right / Down -->
                                        <button
                                            @click.stop="moveCard(index, 1)"
                                            :disabled="index === currentCards.length - 1"
                                            class="p-1.5 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 disabled:opacity-25 disabled:cursor-not-allowed text-xs transition-colors border border-slate-700 font-bold cursor-pointer"
                                            title="Mover después"
                                        >
                                            ➡️
                                        </button>

                                        <!-- Width selectors -->
                                        <div class="flex items-center rounded-xl bg-slate-900 p-0.5 border border-slate-800 ml-1">
                                            <button
                                                v-for="w in ['1/3', '1/2', '2/3', 'full']"
                                                :key="w"
                                                @click.stop="setCardWidth(card.id, w)"
                                                :class="[
                                                    'px-2 py-1 text-[10px] font-black rounded-lg transition-all cursor-pointer',
                                                    card.width === w ? 'bg-cyan-500 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white'
                                                ]"
                                                :title="`Tamaño: ${getWidthLabel(w)}`"
                                            >
                                                {{ w === 'full' ? 'Full' : w }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARD 1: MUSIC -->
                                <template v-if="card.id === 'music'">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <span :class="['tpv-section-accent w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                                            <h3 :class="['tpv-section-title text-xs font-black tracking-widest uppercase', tc.section_title]">
                                                Música Destacada
                                            </h3>
                                        </div>
                                        <span v-if="spotifyEmbedInfo" class="tpv-badge-spotify inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#1DB954]/10 text-[#1DB954] border border-[#1DB954]/20 shadow-[0_0_12px_rgba(29,185,84,0.15)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#1DB954] animate-pulse"></span>
                                            Spotify Player
                                        </span>
                                    </div>

                                    <!-- Real Spotify Embed -->
                                    <div v-if="spotifyEmbedInfo" class="rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 bg-slate-950/80 transition-all duration-300">
                                        <iframe
                                            :src="spotifyEmbedInfo.url"
                                            width="100%"
                                            :height="spotifyEmbedInfo.height"
                                            frameBorder="0"
                                            allowfullscreen=""
                                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                            loading="lazy"
                                            class="w-full block"
                                            style="border-radius: 16px;"
                                        ></iframe>
                                    </div>

                                    <!-- Fallback Player if link cannot be parsed to embed -->
                                    <div v-else-if="profile.widget_status?.spotify" class="bg-slate-950/80 border border-slate-850 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-5 shadow-inner relative group/player overflow-hidden">
                                        <div :class="['w-24 h-24 rounded-2xl bg-gradient-to-tr flex items-center justify-center shrink-0 relative overflow-hidden group-hover/player:scale-105 transition-all duration-500 select-none', tc.vinyl, tc.vinyl_shadow]">
                                            <div class="absolute inset-2 rounded-full border border-white/20 flex items-center justify-center bg-slate-950/95 animate-[spin_10s_linear_infinite]" :class="{ 'paused': !isMusicPlaying }">
                                                <div :class="['w-6 h-6 rounded-full bg-gradient-to-tr flex items-center justify-center text-[8px] font-black text-slate-950', tc.vinyl_center]">
                                                    {{ getInitials(profile.name) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-1 w-full min-w-0">
                                            <h4 class="text-base font-extrabold text-white truncate leading-tight group-hover/player:text-purple-300 transition-colors">
                                                {{ profile.name }} Showcase
                                            </h4>
                                            <p class="text-xs text-purple-400/80 font-bold mt-1 truncate">Música original y covers destacados</p>
                                            <div class="mt-4">
                                                <div class="w-full h-1 bg-slate-900 border border-slate-800 rounded-full overflow-hidden relative">
                                                    <div :class="['h-full bg-gradient-to-r rounded-full', tc.progress_bar, isMusicPlaying ? 'w-[45%] transition-all duration-[8000ms] ease-out' : 'w-[12%] transition-all duration-500']"></div>
                                                </div>
                                                <div class="flex justify-between text-[10px] text-slate-500 font-bold mt-1.5">
                                                    <span>{{ isMusicPlaying ? '01:34' : '00:15' }}</span>
                                                    <span>03:45</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            @click="toggleMusicPlay"
                                            :class="['w-14 h-14 rounded-full bg-gradient-to-r text-slate-950 flex items-center justify-center shrink-0 hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer select-none group/btn', tc.play_btn]"
                                        >
                                            <svg v-if="!isMusicPlaying" class="h-6 w-6 fill-slate-950 translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            <svg v-else class="h-6 w-6 fill-slate-950" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                        </button>
                                    </div>

                                    <!-- Empty state when customizing -->
                                    <div v-else class="tpv-empty-box p-8 text-center border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                        <p class="text-xs text-slate-400">Sin enlace de Spotify configurado aún.</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Configúralo en la edición del perfil para activar el reproductor.</p>
                                    </div>

                                    <!-- Direct Spotify link -->
                                    <div v-if="profile.widget_status?.spotify" class="mt-4 flex justify-end">
                                        <a
                                            :href="profile.widget_status.spotify"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            :class="['text-xs font-bold flex items-center gap-1.5 transition-colors', tc.spotify_link]"
                                        >
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 0C5.373 0 0 5.372 0 12s5.373 12 12 12 12-5.372 12-12S18.627 0 12 0zm5.49 17.3c-.22.36-.685.478-1.045.258-2.868-1.752-6.48-2.15-10.732-1.176-.41.096-.82-.163-.918-.574-.097-.41.162-.82.573-.917 4.653-1.064 8.628-.606 11.865 1.373.36.22.477.685.257 1.045zm1.464-3.262c-.277.45-.86.598-1.31.32-3.284-2.02-8.293-2.607-12.177-1.428-.506.153-1.04-.136-1.194-.643-.154-.506.136-1.04.643-1.194 4.432-1.345 9.947-.694 13.718 1.63.45.276.598.86.32 1.31zm.126-3.414C15.114 8.27 8.57 8.053 4.78 9.203c-.59.18-1.21-.15-1.39-.74-.18-.59.15-1.21.74-1.39 4.35-1.32 11.56-1.07 16.1 1.62.53.31.7.99.39 1.52-.31.53-.99.7-1.52.39z"/>
                                            </svg>
                                            Escuchar en Spotify
                                        </a>
                                    </div>
                                </template>

                                <!-- CARD 2: CALENDAR -->
                                <template v-else-if="card.id === 'calendar'">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <span :class="['tpv-section-accent w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                                            <h3 :class="['tpv-section-title text-xs font-black tracking-widest uppercase', tc.section_title]">
                                                Próximas Presentaciones
                                            </h3>
                                        </div>
                                        <div v-if="totalEventSlides > 1" class="flex items-center gap-2">
                                            <button
                                                @click="prevEvents"
                                                :class="['p-2 rounded-lg bg-slate-900/60 border border-slate-800 hover:border-cyan-500/30 transition-all duration-300 cursor-pointer', tc.social_icon]"
                                                aria-label="Eventos anteriores"
                                            >
                                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="nextEvents"
                                                :class="['p-2 rounded-lg bg-slate-900/60 border border-slate-800 hover:border-cyan-500/30 transition-all duration-300 cursor-pointer', tc.social_icon]"
                                                aria-label="Eventos siguientes"
                                            >
                                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div v-if="upcomingEvents.length > 0" class="relative overflow-hidden -mx-2 px-2">
                                        <div 
                                            class="flex transition-transform duration-500 ease-in-out"
                                            :style="{ transform: `translateX(calc(-${currentEventIndex * 100}%))` }"
                                        >
                                            <div
                                                v-for="(slideEvents, slideIndex) in eventSlides"
                                                :key="`slide-${slideIndex}`"
                                                class="w-full flex-shrink-0 flex flex-col gap-4 px-0"
                                            >
                                                <div
                                                    v-for="event in slideEvents"
                                                    :key="event.id"
                                                    class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-slate-950/80 border border-slate-850 p-4 rounded-2xl overflow-hidden group/gig hover:border-purple-500/30 transition-all duration-300"
                                                >
                                                    <div :class="['bg-gradient-to-br text-white w-14 py-3 rounded-2xl shrink-0 flex flex-col items-center justify-center font-black leading-none text-center shadow-lg relative', tc.event_badge]">
                                                        <div class="absolute top-1/2 -left-1.5 w-3 h-3 bg-slate-950 border-r border-slate-850 rounded-full shrink-0"></div>
                                                        <div class="absolute top-1/2 -right-1.5 w-3 h-3 bg-slate-950 border-l border-slate-850 rounded-full shrink-0"></div>
                                                        <span class="text-[9px] uppercase font-bold text-white/80 tracking-wider">
                                                            {{ formatEventDate(event.start_time).split(' ')[1] }}
                                                        </span>
                                                        <span class="text-xl font-black mt-0.5">
                                                            {{ formatEventDate(event.start_time).split(' ')[0] }}
                                                        </span>
                                                    </div>
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
                                                    <div class="w-full sm:w-auto flex justify-end shrink-0">
                                                        <a
                                                            :href="getWhatsAppUrl(event.title)"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            :class="['bg-slate-900 border border-slate-800 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-all duration-300 cursor-pointer shadow-md select-none w-full sm:w-auto text-center', tc.event_cta]"
                                                        >
                                                            Adquirir Accesos
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="totalEventSlides > 1" class="flex items-center justify-center gap-2 mt-5">
                                            <button
                                                v-for="(slide, sIndex) in totalEventSlides"
                                                :key="`dot-${sIndex}`"
                                                @click="goToEventSlide(sIndex)"
                                                :class="[
                                                    'transition-all duration-300 rounded-full cursor-pointer',
                                                    currentEventIndex === sIndex 
                                                        ? 'w-8 h-2 bg-cyan-500 shadow-[0_0_10px_rgba(6,182,212,0.5)]' 
                                                        : 'w-2 h-2 bg-slate-700 hover:bg-slate-600'
                                                ]"
                                                :aria-label="`Ir a eventos ${sIndex + 1}`"
                                            ></button>
                                        </div>
                                    </div>
                                    <div v-else class="tpv-empty-box p-8 text-center border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                        <p class="text-xs text-slate-400">Sin presentaciones programadas actualmente.</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Agrega fechas en tu calendario para mostrarlas aquí.</p>
                                    </div>
                                </template>

                                <!-- CARD 3: SOCIAL VIDEOS (TikTok / Instagram / Facebook Reels - 9:16) -->
                                <template v-else-if="card.id === 'social_videos'">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <span :class="['tpv-section-accent w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                                            <h3 :class="['tpv-section-title text-xs font-black tracking-widest uppercase', tc.section_title]">
                                                Reels &amp; TikToks
                                            </h3>
                                        </div>
                                        <span v-if="verticalMedia.length > 0" class="tpv-badge-count text-[10px] font-bold text-slate-400 px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-800">
                                            {{ verticalMedia.length }} videos
                                        </span>
                                    </div>

                                    <div v-if="verticalMedia.length > 0" class="relative w-full">
                                        <div class="flex gap-4 overflow-x-auto py-2 scroll-smooth snap-x snap-mandatory scrollbar-hide">
                                            <div
                                                v-for="item in verticalMedia"
                                                :key="item.id"
                                                @click="openLightbox(item)"
                                                class="snap-start shrink-0 w-[170px] sm:w-[200px] aspect-[9/16] relative rounded-2xl overflow-hidden border border-slate-700/30 hover:border-cyan-500/40 transition-all duration-300 shadow-md group cursor-pointer bg-slate-900/60 backdrop-blur-md"
                                            >
                                                <!-- Instagram -->
                                                <template v-if="getPlatform(item) === 'instagram'">
                                                    <img v-if="getThumbnail(item)" :src="getThumbnail(item)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Instagram" />
                                                    <div v-else class="w-full h-full bg-gradient-to-tr from-pink-950/60 via-purple-950/40 to-orange-950/40 flex flex-col items-center justify-center gap-3 p-4">
                                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none">
                                                            <defs><linearGradient id="ig-grad-bento" x1="0" y1="1" x2="1" y2="0"><stop offset="0%" stop-color="#f09433"/><stop offset="50%" stop-color="#dc2743"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs>
                                                            <rect width="24" height="24" rx="6" fill="url(#ig-grad-bento)"/>
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

                                                <!-- TikTok -->
                                                <template v-else-if="getPlatform(item) === 'tiktok'">
                                                    <img v-if="getThumbnail(item)" :src="getThumbnail(item)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="TikTok" />
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

                                                <!-- Facebook -->
                                                <template v-else-if="getPlatform(item) === 'facebook'">
                                                    <img v-if="getThumbnail(item)" :src="getThumbnail(item)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Facebook" />
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
                                    <div v-else class="tpv-empty-box p-8 text-center border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                        <p class="text-xs text-slate-400">Sin reels o videos verticales aún.</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Agrega enlaces de TikTok o Instagram en tu panel de medios.</p>
                                    </div>
                                </template>

                                <!-- CARD 4: YOUTUBE VIDEOS (16:9) -->
                                <template v-else-if="card.id === 'youtube_videos'">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <span :class="['tpv-section-accent w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                                            <h3 :class="['tpv-section-title text-xs font-black tracking-widest uppercase', tc.section_title]">
                                                Videos &amp; En Vivo
                                            </h3>
                                        </div>
                                        <span v-if="horizontalVideos.length > 0" class="tpv-badge-count text-[10px] font-bold text-slate-400 px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-800">
                                            {{ horizontalVideos.length }} videos
                                        </span>
                                    </div>

                                    <div v-if="horizontalVideos.length > 0" class="relative w-full">
                                        <div class="flex gap-4 overflow-x-auto py-2 scroll-smooth snap-x snap-mandatory scrollbar-hide">
                                            <div
                                                v-for="item in horizontalVideos"
                                                :key="item.id"
                                                @click="openLightbox(item)"
                                                class="snap-start shrink-0 w-[260px] sm:w-[320px] aspect-video relative rounded-2xl overflow-hidden border border-slate-700/30 hover:border-cyan-500/40 transition-all duration-300 shadow-md group cursor-pointer bg-slate-900/60 backdrop-blur-md"
                                            >
                                                <img
                                                    v-if="getThumbnail(item)"
                                                    :src="getThumbnail(item)"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                    alt="YouTube"
                                                />
                                                <div v-else class="w-full h-full bg-gradient-to-tr from-slate-900 to-red-950/30 flex items-center justify-center">
                                                    <svg class="h-10 w-10 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 00-2.1 2.1C0 8 0 12 0 12s0 4 .5 5.8a3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1C24 16 24 12 24 12s0-4-.5-5.8zm-14 9.4V8.4l6.3 3.6-6.3 3.6z"/></svg>
                                                </div>
                                                <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                                                    <div :class="['w-10 h-10 rounded-full bg-gradient-to-r flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300', tc.play_circle, tc.play_circle_shadow]">
                                                        <svg class="h-5 w-5 fill-slate-950 translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                    </div>
                                                </div>
                                                <div class="absolute top-2 right-2 bg-red-600/90 text-white text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md tracking-wider">YT</div>
                                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2">
                                                    <p class="text-[9px] font-bold text-white truncate">{{ item.title || 'Video' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="tpv-empty-box p-8 text-center border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                        <p class="text-xs text-slate-400">Sin videos de YouTube agregados aún.</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Vincula videos en tu panel de medios para exhibirlos aquí.</p>
                                    </div>
                                </template>

                                <!-- CARD 5: PHOTOS -->
                                <template v-else-if="card.id === 'photos'">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <span :class="['tpv-section-accent w-8 h-0.5 rounded-full', tc.section_accent]"></span>
                                            <h3 :class="['tpv-section-title text-xs font-black tracking-widest uppercase', tc.section_title]">
                                                Galería Fotográfica
                                            </h3>
                                        </div>
                                        <span v-if="photos.length > 0" class="tpv-badge-count text-[10px] font-bold text-slate-400 px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-800">
                                            {{ photos.length }} fotos
                                        </span>
                                    </div>

                                    <div v-if="photos.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                        <div
                                            v-for="photo in photos"
                                            :key="photo.id"
                                            @click="openLightbox(photo)"
                                            class="aspect-square relative rounded-2xl overflow-hidden border border-slate-800/80 hover:border-cyan-500/40 transition-all duration-300 shadow-md group cursor-pointer bg-slate-900/60"
                                        >
                                            <img
                                                :src="getThumbnail(photo)"
                                                :alt="photo.title || 'Foto de promoción'"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                loading="lazy"
                                            />
                                            <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/40 transition-colors flex items-end p-2.5">
                                                <p v-if="photo.title" class="text-[10px] font-bold text-white truncate w-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                    {{ photo.title }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="tpv-empty-box p-8 text-center border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                                        <p class="text-xs text-slate-400">Sin fotografías en la galería aún.</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Sube fotos de tus eventos o sesiones en el panel de medios.</p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>
                </template>
            </div>

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
                    activeLightboxItem && (getPlatform(activeLightboxItem) === 'tiktok' || getPlatform(activeLightboxItem) === 'instagram' || getPlatform(activeLightboxItem) === 'facebook')
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

                <!-- ── FACEBOOK iframe embed (Reels & Videos - Vertical 9:16) ── -->
                <div
                    v-else-if="getPlatform(activeLightboxItem) === 'facebook'"
                    :class="['rounded-2xl border overflow-hidden shadow-2xl bg-slate-950 flex flex-col items-center justify-between relative', tc.lightbox_border]"
                    style="width:325px; height:580px;"
                >
                    <iframe
                        v-if="getFacebookEmbedUrl(activeLightboxItem.url)"
                        :src="getFacebookEmbedUrl(activeLightboxItem.url)"
                        style="width:325px; height:535px; border:none;"
                        frameborder="0"
                        scrolling="no"
                        allowfullscreen="true"
                        referrerpolicy="no-referrer-when-downgrade"
                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                    ></iframe>
                    <!-- Fallback if URL is non-standard -->
                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-4 p-6 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-[#1877F2] flex items-center justify-center shadow-[0_0_30px_rgba(24,119,242,0.5)]">
                            <svg class="h-8 w-8 fill-white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </div>
                        <p class="text-white font-bold text-sm mb-1">{{ activeLightboxItem.title || 'Video de Facebook' }}</p>
                        <a :href="activeLightboxItem.url" target="_blank" rel="noopener noreferrer"
                           class="bg-slate-800 border border-[#1877F2]/40 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-slate-700 transition-all">
                            Ver en Facebook
                        </a>
                    </div>
                    <!-- Direct Action Link bar in case Meta restricts cross-origin iframe reproduction -->
                    <div v-if="getFacebookEmbedUrl(activeLightboxItem.url)" class="w-full h-[45px] bg-slate-900/90 border-t border-slate-800 flex items-center justify-between px-3 shrink-0">
                        <span class="text-[10px] text-slate-400 font-medium truncate">¿No se carga el video?</span>
                        <a :href="activeLightboxItem.url" target="_blank" rel="noopener noreferrer" class="text-[10px] font-bold text-[#1877F2] hover:underline flex items-center gap-1">
                            Ver en Facebook ↗
                        </a>
                    </div>
                </div>

                <!-- ── INSTAGRAM iframe embed (Reels & Posts) ── -->
                <div
                    v-else-if="getPlatform(activeLightboxItem) === 'instagram'"
                    :class="['rounded-2xl border overflow-hidden shadow-2xl bg-slate-950 flex flex-col items-center justify-between relative', tc.lightbox_border]"
                    style="width:325px; height:580px;"
                >
                    <iframe
                        v-if="getInstagramShortcode(activeLightboxItem.url)"
                        :src="getInstagramEmbedUrl(activeLightboxItem.url)"
                        style="width:325px; height:535px; border:none;"
                        frameborder="0"
                        scrolling="no"
                        allowtransparency="true"
                        referrerpolicy="no-referrer-when-downgrade"
                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture"
                    ></iframe>
                    <!-- Fallback if shortcode is not detected -->
                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-4 p-6 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-orange-500 via-pink-600 to-purple-700 flex items-center justify-center shadow-[0_0_30px_rgba(236,72,153,0.4)]">
                            <svg class="h-8 w-8 fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                        </div>
                        <p class="text-white font-bold text-sm mb-1">{{ activeLightboxItem.title || 'Reel de Instagram' }}</p>
                        <a :href="activeLightboxItem.url" target="_blank" rel="noopener noreferrer"
                           class="bg-slate-800 border border-pink-500/40 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-slate-700 transition-all">
                            Ver en Instagram
                        </a>
                    </div>
                    <!-- Direct Action Link bar in case Meta restricts cross-origin iframe reproduction -->
                    <div v-if="getInstagramShortcode(activeLightboxItem.url)" class="w-full h-[45px] bg-slate-900/90 border-t border-slate-800 flex items-center justify-between px-3 shrink-0">
                        <span class="text-[10px] text-slate-400 font-medium truncate">¿No se carga el reel?</span>
                        <a :href="activeLightboxItem.url" target="_blank" rel="noopener noreferrer" class="text-[10px] font-bold text-pink-400 hover:underline flex items-center gap-1">
                            Ver en Instagram ↗
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
