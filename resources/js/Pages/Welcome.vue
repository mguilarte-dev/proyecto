<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Inicio" />
    
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col font-sans selection:bg-blue-500 selection:text-white">
        <!-- Background Decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-400/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Navigation -->
        <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">G</div>
                <span class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Guilalva <span class="text-blue-600">LMS</span></span>
            </div>
            
            <nav v-if="canLogin" class="flex items-center gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                      class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 transition">
                    Ir al Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" 
                          class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 transition px-4 py-2">
                        Iniciar Sesión
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" 
                          class="text-sm font-bold bg-blue-600 text-white px-6 py-2.5 rounded-full shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:shadow-lg transition">
                        Registrarse
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-6 text-center max-w-4xl mx-auto">
            <div class="mb-6 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-widest animate-pulse">
                <span>Capacitación Continua</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white leading-[1.1] mb-8">
                El conocimiento es el <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500 italic">motor del éxito.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-12 max-w-2xl">
                En Distribuidora Guilalva C.A., creemos que el aprendizaje constante fortalece nuestras capacidades y nos permite alcanzar metas extraordinarias. Bienvenido a tu plataforma de crecimiento.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <Link v-if="!$page.props.auth.user" :href="route('register')" 
                      class="w-full sm:w-auto px-10 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-950 font-bold rounded-2xl hover:scale-105 transition-transform duration-300 shadow-xl shadow-slate-900/20">
                    Comenzar ahora
                </Link>
                <Link v-else :href="route('dashboard')" 
                      class="w-full sm:w-auto px-10 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-950 font-bold rounded-2xl hover:scale-105 transition-transform duration-300 shadow-xl shadow-slate-900/20">
                    Ir a mis Cursos
                </Link>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-12 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                © 2024 Distribuidora Guilalva C.A. • Todos los derechos reservados.
            </p>
        </footer>
    </div>
</template>
