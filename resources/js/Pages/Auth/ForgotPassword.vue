<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Contraseña - KITA" />

    <div class="min-h-screen bg-[#030712] flex flex-col text-slate-100 selection:bg-cyan-500 selection:text-slate-950 overflow-x-hidden">
        <!-- Reusable Navbar -->
        <Navbar />

        <div class="flex-1 flex items-center justify-center p-6 sm:p-12 relative z-10">
            <!-- Decorative Backstage glows -->
            <div class="absolute -top-[10%] -left-[10%] w-[35vw] h-[35vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40vw] h-[40vw] rounded-full bg-blue-600/5 blur-[140px] pointer-events-none"></div>

            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/60 p-8 sm:p-10 rounded-2xl w-full max-w-md shadow-2xl relative">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>

                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <img src="/images/navbar.svg" alt="KITA Logo" class="h-10 w-auto" />
                </div>

                <h1 class="text-2xl font-black text-white leading-tight mb-2 text-center">
                    ¿Olvidaste tu contraseña? 🔑
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-6 text-center">
                    No te preocupes. Escribe tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña y que puedas elegir una nueva.
                </p>

                <div
                    v-if="status"
                    class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400 text-center shadow-lg"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-5">
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
                                autofocus
                                autocomplete="username"
                                placeholder="ejemplo@correo.com"
                                class="bg-slate-950/50 border border-slate-800/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/20 focus:outline-none rounded-xl text-slate-100 placeholder-slate-650 w-full pl-11 pr-4 py-3 text-sm transition-all duration-300"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <button
                        type="submit"
                        :class="[
                            'w-full py-3 rounded-xl font-bold text-xs tracking-wider transition-all duration-300 shadow-lg cursor-pointer text-center',
                            form.processing
                                ? 'bg-slate-800 text-slate-500 cursor-not-allowed opacity-50'
                                : 'bg-cyan-500 hover:bg-cyan-400 text-slate-950 hover:scale-[1.02] shadow-[0_0_20px_rgba(6,182,212,0.3)]'
                        ]"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Enviando...' : 'Enviar enlace de restauración' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
