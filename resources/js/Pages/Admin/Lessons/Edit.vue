<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    lesson: Object,
    course: Object
});

const form = useForm({
    title: props.lesson.title,
    content_type: props.lesson.content_type,
    content_text: props.lesson.content_text || '',
    video_file: null,
    pdf_file: null
});

const submit = () => {
    form.put(route('admin.lessons.update', props.lesson.id), {
        onSuccess: () => {
            // Redirección hecha en el controlador
        },
    });
};
</script>

<template>
    <Head title="Editar Lección" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-gray-800 dark:text-gray-200">
                <div>
                     <Link :href="route('admin.courses.index')" class="text-blue-600 hover:underline">Cursos</Link>
                     <span class="mx-2">/</span>
                     <Link :href="route('admin.courses.lessons.index', course.id)" class="text-blue-600 hover:underline">{{ course.title }}</Link>
                     <span class="mx-2">/</span>
                     <h2 class="inline text-xl font-semibold leading-tight">Editar Lección</h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold dark:text-white mb-4">Editar Lección</h3>
                        <form @submit.prevent="submit">
                            <div>
                                <label class="block dark:text-gray-200">Título</label>
                                <input v-model="form.title" type="text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white mt-1" required />
                            </div>
                            
                            <div class="mt-4">
                                <label class="block dark:text-gray-200">Tipo de Contenido</label>
                                <select v-model="form.content_type" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white mt-1">
                                    <option value="video">Video</option>
                                    <option value="pdf">PDF</option>
                                    <option value="text">Texto</option>
                                </select>
                            </div>

                            <div v-if="form.content_type === 'video'" class="mt-4">
                                <label class="block dark:text-gray-200">Archivo de Video</label>
                                <input type="file" @input="form.video_file = $event.target.files[0]" class="w-full text-gray-500 mt-1" />
                                <p v-if="lesson.content_url && lesson.content_type === 'video'" class="text-sm text-gray-500 mt-1">Archivo actual: {{ lesson.content_url }}</p>
                            </div>

                            <div v-if="form.content_type === 'pdf'" class="mt-4">
                                <label class="block dark:text-gray-200">Archivo PDF</label>
                                <input type="file" @input="form.pdf_file = $event.target.files[0]" class="w-full text-gray-500 mt-1" />
                                <p v-if="lesson.content_url && lesson.content_type === 'pdf'" class="text-sm text-gray-500 mt-1">Archivo actual: {{ lesson.content_url }}</p>
                            </div>

                            <div v-if="form.content_type === 'text'" class="mt-4">
                                <label class="block dark:text-gray-200">Contenido de Texto</label>
                                <textarea v-model="form.content_text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white mt-1" rows="5"></textarea>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <div v-if="form.processing" class="mr-4 text-blue-500 italic">Actualizando...</div>
                                <Link :href="route('admin.courses.lessons.index', course.id)" class="mr-3 text-gray-500">Cancelar</Link>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded" :disabled="form.processing">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>