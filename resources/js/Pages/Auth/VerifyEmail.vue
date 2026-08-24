<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        preserveScroll: true,
    });
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verificar Correo - KITA" />

    <div class="min-h-screen bg-[#030712] flex flex-col text-slate-100 selection:bg-cyan-500 selection:text-slate-950 overflow-x-hidden">
        <!-- Reusable Navbar -->
        <Navbar />

        <div class="flex-1 flex items-center justify-center p-6 sm:p-12 relative z-10">
            <!-- Decorative Backstage glows -->
            <div class="absolute -top-[10%] -left-[10%] w-[35vw] h-[35vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40vw] h-[40vw] rounded-full bg-blue-600/5 blur-[140px] pointer-events-none"></div>

            <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/60 p-8 sm:p-10 rounded-2xl w-full max-w-md shadow-2xl relative text-center">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>

                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <img src="/images/navbar.svg" alt="KITA Logo" class="h-10 w-auto" />
                </div>

                <h1 class="text-2xl font-black text-white mb-4">
                    ¡Casi listo! 🎸
                </h1>

                <div class="mb-6 text-sm text-slate-400 leading-relaxed text-left">
                    Gracias por registrarte en KITA. Antes de comenzar a configurar tu perfil de artista, por favor verifica tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar. Si no recibiste el correo, con gusto te enviaremos otro.
                </div>

                <div
                    class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400 text-center shadow-lg"
                    v-if="verificationLinkSent"
                >
                    Se ha enviado un nuevo enlace de verificación a la dirección de correo que proporcionaste al registrarte.
                </div>

                <form @submit.prevent="submit">
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
                        {{ form.processing ? 'Reenviando...' : 'Reenviar Correo de Verificación' }}
                    </button>
                </form>

                <div class="mt-4">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        type="button"
                        class="w-full text-center text-xs text-slate-500 hover:text-red-400 hover:bg-red-500/5 py-2.5 rounded-xl font-semibold transition-colors cursor-pointer"
                    >
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
