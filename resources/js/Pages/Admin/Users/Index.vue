<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    users: Object
});

const form = useForm({});

const deleteUser = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        form.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Usuarios
                </h2>
                <Link :href="route('admin.users.create')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Nuevo Usuario
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Departamento</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800': user.role === 'admin',
                                            'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800': user.role === 'gerente',
                                            'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800': user.role === 'empleado'
                                        }">{{ user.role }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ user.department || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-4">Editar</Link>
                                        <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900 dark:text-red-400">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Simple Pagination -->
                        <div class="mt-4 flex justify-between">
                            <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">Anterior</Link>
                            <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">Siguiente</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
