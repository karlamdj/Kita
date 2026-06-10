<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <Head title="Aviso de Privacidad - KITA" />

    <div class="bg-[#050b14] text-slate-100 min-h-screen font-sans antialiased relative overflow-hidden selection:bg-cyan-500 selection:text-slate-950">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 inset-x-0 h-[600px] bg-gradient-to-b from-cyan-950/20 via-[#050b14] to-transparent pointer-events-none z-0"></div>
        <div class="absolute top-[20%] right-[-10%] w-[40vw] h-[40vw] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none z-0"></div>
        <div class="absolute bottom-[20%] left-[-10%] w-[45vw] h-[45vw] rounded-full bg-blue-600/5 blur-[140px] pointer-events-none z-0"></div>

        <!-- Navbar -->
        <Navbar class="relative z-10" />

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 relative z-10">
            
            <!-- Back Button -->
            <div class="mb-8">
                <Link 
                    href="/register" 
                    class="inline-flex items-center gap-2 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-colors uppercase tracking-wider group"
                >
                    <span class="transition-transform group-hover:-translate-x-1 font-bold text-sm">←</span>
                    Volver al Registro
                </Link>
            </div>

            <!-- Page Header -->
            <div class="mb-12 border-b border-slate-900/60 pb-8">
                <span class="text-xs font-black tracking-[0.4em] text-cyan-400 mb-3 block uppercase">
                    • CONFIDENCIALIDAD •
                </span>
                <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">
                    Aviso de Privacidad
                </h1>
                <p class="text-slate-400 text-sm font-medium">
                    Última actualización: 10 de junio de 2026
                </p>
            </div>

            <!-- Content Card -->
            <div class="bg-slate-900/20 backdrop-blur-md border border-slate-900 rounded-2xl p-8 sm:p-10 shadow-2xl relative overflow-hidden flex flex-col gap-8 text-slate-350 leading-relaxed text-sm">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-cyan-500/20 to-transparent"></div>
                
                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">01.</span> Responsable de tus Datos
                    </h3>
                    <p>
                        KITA ("Nosotros") es responsable del tratamiento, uso y resguardo de los datos personales recopilados a través de nuestra plataforma web. Nos comprometemos a garantizar tu privacidad y la seguridad de la información proporcionada.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">02.</span> Datos Personales que Recopilamos
                    </h3>
                    <p>
                        Para el correcto funcionamiento de tu Tarjeta de Presentación Virtual (TPV), recopilamos la siguiente información durante tu registro y edición de perfil:
                    </p>
                    <ul class="list-disc pl-5 mt-2 flex flex-col gap-1.5 text-slate-400">
                        <li>**Información de Registro:** Nombre completo, dirección de correo electrónico y contraseña encriptada.</li>
                        <li>**Información del Perfil de Artista:** Nombre artístico o de la banda, biografía, instrumentos musicales, área de cobertura geográfica, imágenes y enlaces externos (redes sociales, videos de shows en vivo, etc.).</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">03.</span> Finalidad del Tratamiento de los Datos
                    </h3>
                    <p>
                        Utilizamos tus datos personales únicamente para los siguientes propósitos:
                    </p>
                    <ul class="list-disc pl-5 mt-2 flex flex-col gap-1.5 text-slate-400">
                        <li>Crear y mantener tu cuenta de usuario en KITA.</li>
                        <li>Generar y publicar de forma pública tu Tarjeta de Presentación Virtual (TPV) para que posibles clientes y bares puedan contactarte.</li>
                        <li>Enviarte notificaciones relevantes de tu cuenta, actualizaciones técnicas y correos automatizados de bienvenida.</li>
                        <li>Mejorar y optimizar la experiencia de usuario dentro del ecosistema KITA.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">04.</span> Compartición de la Información
                    </h3>
                    <p>
                        Tu TPV es por definición una página pública diseñada para promocionar tu música, por lo que los datos del perfil (redes, videos, biografía y formas de contacto que elijas añadir) serán visibles para cualquier visitante web. **No vendemos, rentamos ni comercializamos tus datos personales privados (como tu correo electrónico de registro o tu contraseña) con terceras partes.**
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">05.</span> Seguridad de tus Datos
                    </h3>
                    <p>
                        Implementamos medidas de seguridad técnicas y administrativas para proteger tus datos personales contra accesos no autorizados, alteraciones, pérdidas o divulgación. Las contraseñas se almacenan mediante algoritmos criptográficos robustos de un solo sentido (bcrypt).
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">06.</span> Tus Derechos (Acceso, Rectificación y Cancelación)
                    </h3>
                    <p>
                        Tienes derecho en todo momento a acceder a los datos personales que poseemos sobre ti, rectificarlos en caso de que sean inexactos o solicitar la eliminación total de tu cuenta y TPV del sistema. Puedes realizar estas acciones directamente desde el panel de control de tu cuenta o contactándonos a través del correo de soporte técnico.
                    </p>
                </div>
            </div>

            <!-- Footer copyright inside right screen bottom -->
            <footer class="mt-12 text-center text-xs text-slate-650">
                © 2024 KITA - Tu Mánager Virtual. Todos los derechos reservados.
            </footer>

        </main>
    </div>
</template>
