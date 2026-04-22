<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Inicio" />
    

    <div class="min-h-screen flex flex-col font-sans selection:bg-blue-500 selection:text-white relative bg-gradient-to-br from-[#23272f] via-[#1a1d23] to-[#23272f]">
        <!-- Fondo luminoso -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute left-1/2 top-1/3 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[500px] bg-teal-400/10 rounded-full blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-cyan-300/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Navigation -->
        <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[#3a4d46] to-[#1a1d23] rounded-lg flex items-center justify-center text-[#b6e3d4] font-black text-2xl shadow-lg border border-[#3a4d46]/40">G</div>
                <span class="text-xl font-bold tracking-tight text-[#b6e3d4] drop-shadow">Guilalva <span class="text-[#b6e3d4] font-normal">LMS</span></span>
            </div>
            <nav v-if="canLogin" class="flex items-center gap-3">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                      class="text-base font-semibold text-[#b6e3d4] hover:text-[#7de2d1] transition">
                    Ir al Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" 
                          class="text-base font-semibold text-[#b6e3d4] hover:text-[#7de2d1] transition px-4 py-2">
                        Iniciar Sesión
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" 
                          class="text-base font-bold px-6 py-2.5 rounded-full border border-[#7de2d1]/60 bg-gradient-to-br from-[#2e3a36]/80 to-[#3a4d46]/80 text-[#b6e3d4] shadow-md hover:bg-[#2e3a36] hover:text-[#7de2d1] transition">
                        Registrarse
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-6 text-center max-w-3xl mx-auto">
            <div class="mb-6 inline-flex items-center gap-2 px-4 py-1 rounded-full bg-[#2e3a36]/60 text-[#7de2d1] text-xs font-bold uppercase tracking-widest shadow-sm border border-[#7de2d1]/20">
                <span>CAPACITACIÓN CONTINUA</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold text-[#e6e6e6] leading-[1.1] mb-6 drop-shadow-lg">
                El conocimiento es el <br/>
                <span class="text-[#7de2d1] italic">motor del éxito.</span>
            </h1>
            <p class="text-base md:text-lg text-[#b6e3d4] leading-relaxed mb-10 max-w-2xl mx-auto">
                En Distribuidora Guilalva C.A., creemos que el aprendizaje constante fortalece nuestras capacidades y nos permite alcanzar metas extraordinarias. Bienvenido a tu plataforma de crecimiento.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <Link v-if="!$page.props.auth.user" :href="route('register')" 
                      class="w-full sm:w-auto px-10 py-4 rounded-2xl font-bold text-lg bg-gradient-to-br from-[#3a4d46]/80 to-[#7de2d1]/30 text-[#e6e6e6] border border-[#7de2d1]/40 shadow-lg shadow-[#7de2d1]/10 backdrop-blur-md hover:scale-105 hover:bg-[#2e3a36] hover:text-[#7de2d1] transition-transform duration-300">
                    Comenzar ahora
                </Link>
                <Link v-else :href="route('dashboard')" 
                      class="w-full sm:w-auto px-10 py-4 rounded-2xl font-bold text-lg bg-gradient-to-br from-[#3a4d46]/80 to-[#7de2d1]/30 text-[#e6e6e6] border border-[#7de2d1]/40 shadow-lg shadow-[#7de2d1]/10 backdrop-blur-md hover:scale-105 hover:bg-[#2e3a36] hover:text-[#7de2d1] transition-transform duration-300">
                    Ir a mis Cursos
                </Link>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-8 border-t border-[#3a4d46]/40 flex flex-col md:flex-row justify-between items-center gap-6 mt-8">
            <p class="text-sm text-[#7de2d1] font-medium">
                © 2024 Distribuidora Guilalva C.A. • Todos los derechos reservados.
            </p>
        </footer>
    </div>
</template>
