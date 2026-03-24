<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    courses: Array,
    categories: Array,
    areas: Array
});

const isModalOpen = ref(false);
const form = useForm({
    id: null,
    title: '',
    description: '',
    category_id: '',
    active: true,
    areas: []
});

const openModal = (course = null) => {
    if (course) {
        form.id = course.id;
        form.title = course.title;
        form.description = course.description;
        form.category_id = course.category_id;
        form.active = course.active;
        form.areas = course.areas.map(a => a.id);
    } else {
        form.reset();
        form.areas = [];
    }
    isModalOpen.value = true;
};

const submit = () => {
    if (form.id) {
        form.patch(route('admin.courses.update', form.id), {
            onSuccess: () => (isModalOpen.value = false)
        });
    } else {
        form.post(route('admin.courses.store'), {
            onSuccess: () => (isModalOpen.value = false)
        });
    }
};

const deleteCourse = (id) => {
    if (confirm('¿Eliminar este curso?')) {
        form.delete(route('admin.courses.destroy', id));
    }
};
</script>

<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Cursos de la Empresa</h2>
                <button @click="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded">Nuevo Curso</button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border dark:border-gray-700">
                        <div class="p-6">
                            <span class="text-xs font-bold uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ course.category.name }}</span>
                            <h3 class="mt-2 text-xl font-bold dark:text-white">{{ course.title }}</h3>
                            <div class="mt-1 flex flex-wrap gap-1">
                                <span v-for="area in course.areas" :key="area.id" 
                                      class="text-[9px] px-2 py-0.5 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 rounded-full font-black uppercase">
                                    {{ area.name }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ course.description }}</p>
                            <div class="mt-6 flex justify-between items-center">
                                <Link :href="route('admin.courses.lessons.index', course.id)" class="text-blue-600 font-bold hover:underline">Gestionar Lecciones</Link>
                                <div class="space-x-4">
                                    <Link :href="route('admin.courses.evaluations.index', course.id)" class="text-purple-600 font-bold hover:underline">Evaluaciones</Link>
                                    <button @click="openModal(course)" class="text-indigo-600 font-medium">Editar</button>
                                    <button @click="deleteCourse(course.id)" class="text-red-600 font-medium">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h3 class="text-lg font-bold dark:text-white">{{ form.id ? 'Editar Curso' : 'Nuevo Curso' }}</h3>
                <form @submit.prevent="submit" class="mt-4">
                    <div class="mt-4">
                        <label class="block dark:text-gray-200">Título</label>
                        <input v-model="form.title" type="text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required />
                    </div>
                    <div class="mt-4">
                        <label class="block dark:text-gray-200">Categoría</label>
                        <select v-model="form.category_id" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required>
                            <option value="">Seleccione...</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label class="block dark:text-gray-200 font-bold mb-2">Áreas Destinatarias</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-32 overflow-y-auto p-2 border rounded dark:border-gray-700">
                            <label v-for="area in areas" :key="area.id" class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                                <input type="checkbox" :value="area.id" v-model="form.areas" class="rounded text-blue-600 focus:ring-blue-500" />
                                <span class="text-xs dark:text-gray-300">{{ area.name }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block dark:text-gray-200">Descripción</label>
                        <textarea v-model="form.description" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" rows="3"></textarea>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="isModalOpen = false" class="mr-3 text-gray-500">Cancelar</button>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
