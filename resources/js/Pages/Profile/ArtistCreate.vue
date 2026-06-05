<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const themes = [
    { id: 'kita-neon', name: 'Kita Neon', class: 'theme-kita-neon', colors: 'from-cyan-500 to-blue-500' },
    { id: 'cyber-purple', name: 'Cyber Purple', class: 'theme-cyber-purple', colors: 'from-purple-500 to-pink-500' },
    { id: 'volt-orange', name: 'Volt Orange', class: 'theme-volt-orange', colors: 'from-orange-500 to-amber-500' },
    { id: 'electric-red', name: 'Electric Red', class: 'theme-electric-red', colors: 'from-red-600 to-orange-500' },
];

const form = useForm({
    name: '',
    theme: 'kita-neon',
});

const submit = () => {
    form.post(route('profile.store'));
};
</script>

<template>
    <Head title="Crear Perfil o Banda - KITA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-white flex items-center gap-2">
                <span>➕</span> Crear Perfil o Banda
            </h2>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 relative">
            <!-- Ambient Glow for Form Area -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] rounded-full bg-cyan-500/5 blur-[100px] pointer-events-none"></div>

            <div class="mx-auto max-w-xl">
                <!-- Form Card with Glassmorphic Styles -->
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                    <!-- Top subtle accent line -->
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-cyan-500/40 to-transparent"></div>

                    <h3 class="text-lg font-black text-white mb-2">Nuevo Proyecto</h3>
                    <p class="text-xs text-slate-400 mb-8 leading-relaxed">
                        Crea un perfil independiente para promocionar tu trabajo solista o de banda. Cada perfil tiene su propio portafolio (TPV), agenda de eventos, archivos de medios y tema visual exclusivo.
                    </p>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Input -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                Nombre del Artista o Banda
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Ej. Los Vagabundos, María Soler"
                                class="bg-[#050b14]/60 border border-slate-800/80 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 focus:outline-none rounded-xl text-slate-200 placeholder-slate-600 w-full px-4 py-3 text-sm transition-all duration-300"
                                required
                                autofocus
                            />
                            <p v-if="form.errors.name" class="text-red-400 text-xs mt-1.5 font-semibold">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Theme Radio Grid selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-3">
                                Identidad Visual & Tema Neon
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    v-for="t in themes"
                                    :key="t.id"
                                    :class="[
                                        'relative flex flex-col justify-between p-4 rounded-xl border cursor-pointer select-none transition-all duration-300 bg-slate-950/40 hover:bg-slate-950/60',
                                        form.theme === t.id
                                            ? 'border-cyan-500/50 shadow-[0_0_15px_rgba(6,182,212,0.08)] bg-slate-900/60'
                                            : 'border-slate-800/60 hover:border-slate-800'
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        name="theme"
                                        :value="t.id"
                                        v-model="form.theme"
                                        class="sr-only"
                                    />
                                    
                                    <div class="flex items-center justify-between w-full mb-3">
                                        <span class="text-xs font-bold text-slate-200">{{ t.name }}</span>
                                        <!-- Checked ring indicator -->
                                        <div
                                            class="w-3.5 h-3.5 rounded-full border flex items-center justify-center transition-all duration-300"
                                            :class="form.theme === t.id ? 'border-cyan-400 bg-cyan-400/10' : 'border-slate-700'"
                                        >
                                            <div v-if="form.theme === t.id" class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                                        </div>
                                    </div>

                                    <!-- Theme visual preview bar -->
                                    <div class="h-2 w-full rounded bg-slate-800/80 overflow-hidden flex">
                                        <div :class="['h-full w-1/2 bg-gradient-to-r', t.colors]"></div>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.theme" class="text-red-400 text-xs mt-1.5 font-semibold">
                                {{ form.errors.theme }}
                            </p>
                        </div>

                        <!-- Buttons Row -->
                        <div class="flex items-center justify-end gap-4 border-t border-slate-800/60 pt-6 mt-8">
                            <Link
                                href="/dashboard"
                                class="text-xs font-bold text-slate-400 hover:text-white px-4 py-2.5 rounded-xl transition-colors"
                            >
                                Cancelar
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 disabled:opacity-50 text-slate-950 font-bold text-xs tracking-wider transition-all duration-300 shadow-[0_0_20px_rgba(6,182,212,0.3)] hover:shadow-[0_0_25px_rgba(6,182,212,0.45)] cursor-pointer"
                            >
                                {{ form.processing ? 'Creando...' : 'Crear Perfil' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
