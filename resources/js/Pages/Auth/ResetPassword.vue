<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer Contraseña - KITA" />

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
                    Restablecer Contraseña 🔐
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-6 text-center">
                    Ingresa tu nueva contraseña para volver a acceder a tu cuenta.
                </p>

                <form @submit.prevent="submit" class="flex flex-col gap-4">
                    <!-- Email input -->
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

                    <!-- Password input -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                            Nueva contraseña
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

                    <!-- Confirm password input -->
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                            Confirmar nueva contraseña
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

                    <button
                        type="submit"
                        :class="[
                            'w-full py-3 rounded-xl font-bold text-xs tracking-wider transition-all duration-300 shadow-lg cursor-pointer text-center mt-2',
                            form.processing
                                ? 'bg-slate-800 text-slate-500 cursor-not-allowed opacity-50'
                                : 'bg-cyan-500 hover:bg-cyan-400 text-slate-950 hover:scale-[1.02] shadow-[0_0_20px_rgba(6,182,212,0.3)]'
                        ]"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando...' : 'Restablecer contraseña' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
