<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    lesson: Object,
    course: Object
});
</script>

<template>
    <Head title="Ver Lección" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-gray-800 dark:text-gray-200">
                <div>
                     <Link :href="route('admin.courses.index')" class="text-blue-600 hover:underline">Cursos</Link>
                     <span class="mx-2">/</span>
                     <Link :href="route('admin.courses.lessons.index', course.id)" class="text-blue-600 hover:underline">{{ course.title }}</Link>
                     <span class="mx-2">/</span>
                     <h2 class="inline text-xl font-semibold leading-tight">Ver Lección</h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-4 dark:text-white">{{ lesson.title }}</h3>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-300">Tipo: {{ lesson.content_type }}</p>

                    <div v-if="lesson.content_type === 'video'" class="mb-6">
                        <video controls class="w-full h-auto max-h-96">
                            <source :src="lesson.content_url" type="video/mp4" />
                            Tu navegador no soporta reproducción de video.
                        </video>
                        <p class="mt-2"><a :href="lesson.content_url" target="_blank" class="text-blue-500 underline">Abrir en nueva pestaña</a></p>
                    </div>

                    <div v-else-if="lesson.content_type === 'pdf'" class="mb-6">
                        <iframe :src="lesson.content_url" class="w-full h-96 border" frameborder="0"></iframe>
                        <p class="mt-2"><a :href="lesson.content_url" target="_blank" class="text-blue-500 underline">Abrir en nueva pestaña</a></p>
                    </div>

                    <div v-else class="mb-6">
                        <p class="whitespace-pre-line text-gray-700 dark:text-gray-200">{{ lesson.content_text || 'No hay contenido de texto disponible.' }}</p>
                    </div>

                    <div class="flex justify-end">
                        <Link :href="route('admin.courses.lessons.index', course.id)" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Volver</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
