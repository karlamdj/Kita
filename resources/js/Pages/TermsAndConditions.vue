<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <Head title="Términos y Condiciones - KITA" />

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
                    • ACUERDO LEGAL •
                </span>
                <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">
                    Términos y Condiciones de Uso
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
                        <span class="text-cyan-400 font-mono">01.</span> Aceptación de los Términos
                    </h3>
                    <p>
                        Al registrarte y hacer uso de la plataforma KITA (el "Servicio"), aceptas quedar vinculado por estos Términos y Condiciones. Si no estás de acuerdo con alguno de los puntos descritos, no deberás acceder ni utilizar el Servicio.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">02.</span> Descripción del Servicio
                    </h3>
                    <p>
                        KITA es una plataforma virtual que proporciona a músicos, bandas y artistas una Tarjeta de Presentación Virtual (TPV) para centralizar su portafolio, redes sociales, videos de shows en vivo e información de contacto directo. KITA actúa como facilitador tecnológico y no interviene ni cobra comisiones en las contrataciones entre bares, festivales o clientes y los artistas.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">03.</span> Registro y Responsabilidad de la Cuenta
                    </h3>
                    <p>
                        Para hacer uso de los servicios de KITA, debes registrarte y mantener una cuenta activa. Eres responsable de:
                    </p>
                    <ul class="list-disc pl-5 mt-2 flex flex-col gap-1.5 text-slate-400">
                        <li>Mantener la confidencialidad de tu contraseña y datos de acceso.</li>
                        <li>Proporcionar información verídica, exacta y actualizada sobre tu identidad o la de tu banda.</li>
                        <li>Toda la actividad que ocurra bajo tu perfil o TPV.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">04.</span> Propiedad Intelectual y Contenido
                    </h3>
                    <p>
                        Conservas todos los derechos de propiedad intelectual sobre el contenido que subas, vincules o alimentes en tu galería (logos, fotografías, videos incrustados, enlaces de redes sociales). Al publicar contenido en KITA, otorgas a la plataforma una licencia global, no exclusiva y gratuita para mostrar dicho contenido públicamente con fines de promoción de tu propio perfil de artista.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">05.</span> Limitación de Responsabilidad
                    </h3>
                    <p>
                        KITA se esfuerza por ofrecer un servicio continuo y de alta calidad; sin embargo, no garantizamos que la plataforma funcione de manera ininterrumpida o libre de errores. No nos hacemos responsables por pérdidas derivadas de fallos técnicos ajenos o por acuerdos insatisfactorios pactados entre bares/clientes y músicos registrados.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-cyan-400 font-mono">06.</span> Modificaciones del Servicio y Términos
                    </h3>
                    <p>
                        Nos reservamos el derecho de modificar o retirar el Servicio, así como de actualizar estos términos en cualquier momento. Te notificaremos sobre cambios sustanciales a través de la plataforma o por correo electrónico. El uso continuado del Servicio tras dichas actualizaciones constituirá tu aceptación de las mismas.
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
