<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import Navbar from '@/Components/Navbar.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear Cuenta - KITA" />

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

            <!-- 2. Right Section: Registration Form -->
            <section class="flex-1 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10">
                <!-- Mobile Branding Header (visible only on mobile) -->
                <div class="flex md:hidden items-center justify-center mb-8">
                    <img src="/images/logo.svg" alt="KITA Logo" class="h-10 w-auto" />
                </div>

                <!-- Form Wrapper Card -->
                <div class="flex-1 flex items-center justify-center my-auto py-4">
                    <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/60 p-8 sm:p-10 rounded-2xl w-full max-w-md shadow-2xl relative">
                        <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>

                        <!-- Title Header -->
                        <div class="text-left mb-6">
                            <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">
                                Crea tu cuenta KITA
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                                Regístrate para dar a conocer tu talento musical.
                            </p>
                        </div>

                        <!-- Registration Form -->
                        <form @submit.prevent="submit" class="flex flex-col gap-4">
                            
                            <!-- Name Input -->
                            <div>
                                <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                                    Nombre Completo
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                    <input
                                        id="name"
                                        type="text"
                                        v-model="form.name"
                                        required
                                        autofocus
                                        autocomplete="name"
                                        placeholder="Tu nombre o el de tu banda"
                                        class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-4 py-3 text-sm transition-all duration-300"
                                    />
                                </div>
                                <InputError class="mt-1" :message="form.errors.name" />
                            </div>

                            <!-- Email Input -->
                            <div>
                                <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
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
                                        autocomplete="username"
                                        placeholder="ejemplo@correo.com"
                                        class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-4 py-3 text-sm transition-all duration-300"
                                    />
                                </div>
                                <InputError class="mt-1" :message="form.errors.email" />
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
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
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-11 py-3 text-sm transition-all duration-300"
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
                                <InputError class="mt-1" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password Input -->
                            <div>
                                <label for="password_confirmation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                                    Confirmar Contraseña
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password_confirmation"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-11 py-3 text-sm transition-all duration-300"
                                    />
                                    <!-- Eye Toggle button -->
                                    <button
                                        type="button"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute right-3.5 text-slate-500 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer"
                                    >
                                        <svg v-if="!showConfirmPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <InputError class="mt-1" :message="form.errors.password_confirmation" />
                            </div>

                            <!-- Primary Register Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                :class="[
                                    'w-full bg-gradient-to-r from-cyan-400 to-cyan-500 text-slate-950 font-black text-xs tracking-wider uppercase py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-300 shadow-[0_0_20px_rgba(6,182,212,0.15)] hover:shadow-[0_0_30px_rgba(6,182,212,0.25)] cursor-pointer group mt-2',
                                    { 'opacity-50 pointer-events-none': form.processing }
                                ]"
                            >
                                <span>Crear Cuenta</span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold text-sm">→</span>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="flex items-center gap-3 my-5">
                            <div class="h-[1px] bg-slate-800/80 flex-1"></div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">o regístrate con</span>
                            <div class="h-[1px] bg-slate-800/80 flex-1"></div>
                        </div>

                        <!-- Social Login Button (Facebook) -->
                        <button
                            type="button"
                            class="w-full bg-slate-950/60 border border-slate-800/80 rounded-xl py-3 px-4 flex items-center justify-center gap-3 hover:bg-slate-900 transition-all duration-350 text-white text-xs font-bold uppercase tracking-wider cursor-pointer shadow-sm hover:border-slate-700"
                        >
                            <svg class="h-5 w-5 fill-current text-[#1877F2]" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Facebook</span>
                        </button>

                        <!-- Login Redirection Link -->
                        <div class="text-center mt-6 text-xs">
                            <span class="text-slate-500 font-medium">¿Ya tienes cuenta? </span>
                            <Link :href="route('login')" class="text-cyan-400 hover:text-cyan-300 font-bold hover:underline transition-colors ml-1">
                                Iniciar Sesión
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Footer copyright inside right screen bottom -->
                <footer class="mt-4 pt-4 border-t border-slate-900/40">
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
