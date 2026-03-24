<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    description: ''
});

const submit = () => {
    form.post(route('admin.categories.store'), {
        onSuccess: () => form.reset()
    });
};

const deleteCategory = (id) => {
    if (confirm('¿Eliminar esta categoría?')) {
        form.delete(route('admin.categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Categorías
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
                <!-- Create Category -->
                <div class="w-full md:w-1/3 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg h-fit">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Nueva Categoría</h3>
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Nombre" />
                            <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="description" value="Descripción (Opcional)" />
                            <TextInput id="description" v-model="form.description" class="mt-1 block w-full" />
                        </div>
                        <div class="mt-4">
                            <PrimaryButton :disabled="form.processing">Agregar</PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Category List -->
                <div class="w-full md:w-2/3 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="cat in categories" :key="cat.id">
                                <td class="px-6 py-4 dark:text-gray-200">{{ cat.name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteCategory(cat.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
