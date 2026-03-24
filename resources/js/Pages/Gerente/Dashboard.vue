<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps({
    departmentMetrics: Array,
    courseMetrics: Array
});

// Prepare data for Department scores (Bar Chart)
const departmentChartData = {
    labels: props.departmentMetrics.map(m => m.department || 'Sin Dept.'),
    datasets: [{
        label: 'Promedio de Calificaciones',
        data: props.departmentMetrics.map(m => parseFloat(m.avg_score).toFixed(2)),
        backgroundColor: '#4f46e5'
    }]
};

// Prepare data for Course completion (Pie Chart)
const courseChartData = {
    labels: props.courseMetrics.map(m => m.title),
    datasets: [{
        label: 'Tasa de Finalización',
        data: props.courseMetrics.map(m => m.completion_rate),
        backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6']
    }]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
};
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Reportes
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Department Scores -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold dark:text-white mb-4 text-center">Rendimiento por Departamento</h3>
                        <div class="h-64">
                            <Bar :data="departmentChartData" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Course Completion Rates -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold dark:text-white mb-4 text-center">Finalización de Cursos (%)</h3>
                        <div class="h-64">
                            <Pie :data="courseChartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <!-- Stats summary table -->
                <div class="mt-8 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg overflow-x-auto">
                    <h3 class="text-lg font-bold dark:text-white mb-4">Detalle de Métricas de Cursos</h3>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Curso</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Inscritos</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Calificación Promedio</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tasa de Finalización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="course in courseMetrics" :key="course.id">
                                <td class="px-6 py-4 dark:text-white font-medium">{{ course.title }}</td>
                                <td class="px-6 py-4 text-center dark:text-gray-300">{{ course.total_enrolled }}</td>
                                <td class="px-6 py-4 text-center dark:text-gray-300">{{ parseFloat(course.avg_score).toFixed(2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded"
                                          :class="course.completion_rate > 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                        {{ course.completion_rate }}%
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
