<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    course: Object,
    lessons: Array
});

const isModalOpen = ref(false);
const form = useForm({
    title: '',
    content_type: 'video',
    content_text: '',
    video_file: null,
    pdf_file: null
});

const submit = () => {
    form.post(route('admin.courses.lessons.store', props.course.id), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        },
    });
};

const deleteLesson = (id) => {
    if (confirm('¿Eliminar esta lección?')) {
        form.delete(route('admin.lessons.destroy', id));
    }
};
</script>

<template>
    <Head title="Lecciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-gray-800 dark:text-gray-200">
                <div>
                     <Link :href="route('admin.courses.index')" class="text-blue-600 hover:underline">Cursos</Link>
                     <span class="mx-2">/</span>
                     <h2 class="inline text-xl font-semibold leading-tight">Lecciones</h2>
                </div>
                <button @click="isModalOpen = true" class="px-4 py-2 bg-blue-600 text-white rounded">Nueva Lección</button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Orden</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="lesson in lessons" :key="lesson.id">
                                <td class="px-6 py-4 dark:text-gray-300">{{ lesson.lesson_order }}</td>
                                <td class="px-6 py-4 dark:text-gray-300 font-bold">{{ lesson.title }}</td>
                                <td class="px-6 py-4 dark:text-gray-300 uppercase text-xs">{{ lesson.content_type }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <Link :href="route('admin.lessons.edit', lesson.id)" class="text-blue-600 hover:underline mr-2">Editar</Link>
                                    <button @click="deleteLesson(lesson.id)" class="text-red-600 font-medium">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Lesson Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h3 class="text-lg font-bold dark:text-white">Agregar Lección</h3>
                <form @submit.prevent="submit" class="mt-4">
                    <div>
                        <label class="block dark:text-gray-200">Título</label>
                        <input v-model="form.title" type="text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required />
                    </div>
                    
                    <div class="mt-4">
                        <label class="block dark:text-gray-200">Tipo de Contenido</label>
                        <select v-model="form.content_type" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white">
                            <option value="video">Video</option>
                            <option value="pdf">PDF</option>
                            <option value="text">Texto</option>
                        </select>
                    </div>

                    <div v-if="form.content_type === 'video'" class="mt-4">
                        <label class="block dark:text-gray-200">Archivo de Video</label>
                        <input type="file" @input="form.video_file = $event.target.files[0]" class="w-full text-gray-500" />
                    </div>

                    <div v-if="form.content_type === 'pdf'" class="mt-4">
                        <label class="block dark:text-gray-200">Archivo PDF</label>
                        <input type="file" @input="form.pdf_file = $event.target.files[0]" class="w-full text-gray-500" />
                    </div>

                    <div v-if="form.content_type === 'text'" class="mt-4">
                        <label class="block dark:text-gray-200">Contenido de Texto</label>
                        <textarea v-model="form.content_text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" rows="5"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end">
                         <div v-if="form.processing" class="mr-4 text-blue-500 italic">Subiendo...</div>
                        <button type="button" @click="isModalOpen = false" class="mr-3 text-gray-500">Cancelar</button>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded" :disabled="form.processing">Guardar</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
