<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Administración" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Administración
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- User CRUD Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Gestión de Usuarios</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Gestionar empleados, gerentes y administradores de la plataforma.</p>
                        <Link :href="route('admin.users.index')" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium cursor-pointer">Ver Usuarios</Link>
                    </div>
 
                    <!-- Course CRUD Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Gestión de Cursos</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Cargar videos, PDFs y material educativo para capacitación.</p>
                        <Link :href="route('admin.courses.index')" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium cursor-pointer">Gestionar Cursos</Link>
                    </div>
 
                    <!-- Categories Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Gestión de Categorías</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Crear y organizar las categorías para clasificar los cursos.</p>
                        <Link :href="route('admin.categories.index')" class="inline-block mt-4 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 font-medium cursor-pointer">Ver Categorías</Link>
                    </div>

                    <!-- Areas Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition border-2 border-dashed border-indigo-200 dark:border-indigo-900/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Gestión de Áreas</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Administrar departamentos y segmentación de cursos para Guilalva.</p>
                        <Link :href="route('admin.areas.index')" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-medium cursor-pointer">Ver Áreas</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
