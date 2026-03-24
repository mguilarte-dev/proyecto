<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    areas: Array
});

const isModalOpen = ref(false);
const form = useForm({
    id: null,
    name: '',
    description: ''
});

const openModal = (area = null) => {
    if (area) {
        form.id = area.id;
        form.name = area.name;
        form.description = area.description;
    } else {
        form.reset();
    }
    isModalOpen.value = true;
};

const submit = () => {
    if (form.id) {
        form.patch(route('admin.areas.update', form.id), {
            onSuccess: () => (isModalOpen.value = false)
        });
    } else {
        form.post(route('admin.areas.store'), {
            onSuccess: () => (isModalOpen.value = false)
        });
    }
};

const deleteArea = (id) => {
    if (confirm('¿Eliminar esta área? Los usuarios y cursos perderán esta asociación.')) {
        form.delete(route('admin.areas.destroy', id), {
            onError: (errors) => {
                if(errors.error) alert(errors.error);
            }
        });
    }
};
</script>

<template>
    <Head title="Áreas de Guilalva" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Áreas de la Empresa</h2>
                <button @click="openModal()" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">Nueva Área</button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="p-4 border-b dark:border-gray-600 dark:text-gray-200">Nombre</th>
                                <th class="p-4 border-b dark:border-gray-600 dark:text-gray-200">Descripción</th>
                                <th class="p-4 border-b dark:border-gray-600 dark:text-gray-200">Usuarios</th>
                                <th class="p-4 border-b dark:border-gray-600 dark:text-gray-200">Cursos</th>
                                <th class="p-4 border-b dark:border-gray-600 dark:text-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="area in areas" :key="area.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="p-4 border-b dark:border-gray-600 font-bold dark:text-white">{{ area.name }}</td>
                                <td class="p-4 border-b dark:border-gray-600 text-sm text-gray-500 dark:text-gray-400">{{ area.description || 'Sin descripción' }}</td>
                                <td class="p-4 border-b dark:border-gray-600 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold">{{ area.users_count }}</span></td>
                                <td class="p-4 border-b dark:border-gray-600 text-center"><span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-bold">{{ area.courses_count }}</span></td>
                                <td class="p-4 border-b dark:border-gray-600">
                                    <button @click="openModal(area)" class="text-indigo-600 hover:text-indigo-900 mr-3 font-medium">Editar</button>
                                    <button @click="deleteArea(area.id)" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-bold dark:text-gray-200">{{ form.id ? 'Editar Área' : 'Nueva Área' }}</h2>
                <form @submit.prevent="submit" class="mt-4">
                    <div>
                        <label class="block dark:text-gray-300">Nombre del Área</label>
                        <input v-model="form.name" type="text" class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:text-white" required />
                    </div>
                    <div class="mt-4">
                        <label class="block dark:text-gray-300">Descripción (Opcional)</label>
                        <textarea v-model="form.description" class="w-full mt-1 rounded border-gray-300 dark:bg-gray-900 dark:text-white" rows="3"></textarea>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="isModalOpen = false" class="mr-4 text-gray-500 hover:text-gray-700 transition">Cancelar</button>
                        <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">Guardar</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
