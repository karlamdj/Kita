<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import Navbar from '@/Components/Navbar.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión - KITA" />

    <div class="min-h-screen bg-[#030712] flex flex-col text-slate-100 selection:bg-cyan-500 selection:text-slate-950 overflow-x-hidden">
        <!-- Reusable Navbar -->
        <Navbar />

        <!-- Split Screen Content -->
        <div class="flex-1 flex flex-col md:flex-row">
        
        <!-- 1. Left Section: Branding & Identity -->
        <section class="relative hidden md:flex md:w-[45%] lg:w-[50%] bg-[#050b14] flex-col justify-between p-12 lg:p-16 overflow-hidden border-r border-slate-900/60 shrink-0 select-none">
            <!-- Decorative Backstage glows -->
            <div class="absolute -top-[10%] -left-[10%] w-[35vw] h-[35vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40vw] h-[40vw] rounded-full bg-blue-600/5 blur-[140px] pointer-events-none"></div>

            <!-- Header Branding -->
            <div class="z-10 flex justify-center">
                <img src="/images/logo.svg" alt="KITA Logo" class="w-full max-w-[480px] h-auto" />
            </div>

            <!-- Centered Pitch Text -->
            <div class="my-auto max-w-lg z-10 text-center mx-auto">
                <h2 class="text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight mb-6">
                    Descubre, conecta y haz crecer <br />
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                        tu proyecto musical.
                    </span>
                </h2>
                <p class="text-slate-400 text-base leading-relaxed max-w-md mx-auto">
                    La plataforma que conecta músicos y bandas con oportunidades reales, libre de intermediarios y diseñada para la escena actual.
                </p>
            </div>

            <!-- Wave background graphic (Absolute position bottom-left) -->
            <div class="absolute bottom-0 left-0 w-full opacity-[0.04] pointer-events-none">
                <svg viewBox="0 0 500 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                    <path d="M0 100 C 150 150, 300 50, 500 100 L 500 200 L 0 200 Z" fill="#00f3ff"/>
                    <path d="M0 120 C 120 180, 280 20, 500 120" stroke="#00f3ff" stroke-width="2" />
                </svg>
            </div>
        </section>

        <!-- 2. Right Section: Login Form -->
        <section class="flex-1 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10">
            <!-- Mobile Branding Header (visible only on mobile) -->
            <div class="flex md:hidden items-center justify-center mb-8">
                <img src="/images/logo.svg" alt="KITA Logo" class="h-10 w-auto" />
            </div>

            <!-- Form Wrapper Card -->
            <div class="flex-1 flex items-center justify-center my-auto">
                <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/60 p-8 sm:p-10 rounded-2xl w-full max-w-md shadow-2xl relative">
                    <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>

                    <!-- Title Header -->
                    <div class="text-left mb-8">
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">
                            ¡Bienvenido de vuelta!
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                            Inicia sesión para continuar conectando talento.
                        </p>
                    </div>

                    <!-- Laravel Status Message -->
                    <div v-if="status" class="mb-6 text-xs sm:text-sm font-semibold text-cyan-400 bg-cyan-950/20 border border-cyan-800/30 p-3.5 rounded-xl">
                        {{ status }}
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="flex flex-col gap-5">
                        
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                Correo electrónico
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="ejemplo@correo.com"
                                    class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-4 py-3.5 text-sm transition-all duration-300"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.email" />
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                Contraseña
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-11 py-3.5 text-sm transition-all duration-300"
                                />
                                <!-- Eye Toggle button -->
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3.5 text-slate-500 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer"
                                >
                                    <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me and Forgot Password row -->
                        <div class="flex items-center justify-between mt-1 text-xs">
                            <label class="flex items-center cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    v-model="form.remember"
                                    class="rounded border-slate-800 bg-slate-950 text-cyan-500 focus:ring-cyan-500/20 focus:ring-offset-slate-900 w-4 h-4 cursor-pointer"
                                />
                                <span class="ms-2 text-slate-400 hover:text-slate-300 transition-colors font-medium">Recordarme</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-cyan-400/80 hover:text-cyan-300 transition-colors font-semibold"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>

                        <!-- Primary Login Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :class="[
                                'w-full bg-gradient-to-r from-cyan-400 to-cyan-500 text-slate-950 font-black text-xs tracking-wider uppercase py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-300 shadow-[0_0_20px_rgba(6,182,212,0.15)] hover:shadow-[0_0_30px_rgba(6,182,212,0.25)] cursor-pointer group',
                                { 'opacity-50 pointer-events-none': form.processing }
                            ]"
                        >
                            <span>Iniciar Sesión</span>
                            <span class="transition-transform group-hover:translate-x-1 font-bold text-sm">→</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div v-if="!$page.props.is_production" class="flex items-center gap-3 my-6">
                        <div class="h-[1px] bg-slate-800/80 flex-1"></div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">o continúa con</span>
                        <div class="h-[1px] bg-slate-800/80 flex-1"></div>
                    </div>

                    <!-- Social Login Button (Google) -->
                    <a
                        v-if="!$page.props.is_production"
                        href="/auth/google"
                        class="w-full relative flex items-center justify-center gap-3 bg-slate-950/40 backdrop-blur-md border border-cyan-500/30 text-cyan-400 hover:text-white rounded-xl py-3.5 px-4 transition-all duration-300 text-xs font-bold uppercase tracking-wider cursor-pointer shadow-[0_0_10px_rgba(6,182,212,0.1)] hover:shadow-[0_0_20px_rgba(6,182,212,0.3)] hover:border-cyan-400"
                    >
                        <!-- SVG de Google -->
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continuar con Google</span>
                    </a>

                    <!-- Registration Redirection Link -->
                    <div class="text-center mt-8 text-xs">
                        <span class="text-slate-500 font-medium">¿No tienes cuenta? </span>
                        <Link :href="route('register')" class="text-cyan-400 hover:text-cyan-300 font-bold hover:underline transition-colors ml-1">
                            Registrarse
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Footer copyright inside right screen bottom -->
            <footer class="mt-8 pt-4 border-t border-slate-900/40">
                <p class="text-[10px] text-slate-650 text-center font-semibold tracking-wide select-none">
                    © 2024 KITA - Tu Manager Virtual. Todos los derechos reservados.
                </p>
            </footer>
        </section>

        </div>
    </div>
</template>

<style scoped>
/* Focus transition highlights */
input:focus {
    box-shadow: 0 0 15px rgba(6, 182, 212, 0.05);
}
</style>
