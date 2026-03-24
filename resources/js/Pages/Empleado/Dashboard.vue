<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    courses: Array,
    myEnrollments: Array,
    courseProgress: Object
});

const isEnrolled = (courseId) => props.myEnrollments.includes(courseId);
const getCourseData = (courseId) => props.courseProgress[courseId] || { percentage: 0, is_completed: false };
</script>

<template>
    <Head title="Mis Cursos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Catálogo de Capacitación
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border dark:border-gray-700">
                        <div class="h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="p-4">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ course.category.name }}</span>
                            <h3 class="mt-2 text-lg font-bold dark:text-white">{{ course.title }}</h3>
                            <p class="mt-0.5 text-sm text-gray-500 line-clamp-2">{{ course.description }}</p>
                            
                            <!-- Progress Bar -->
                            <div v-if="isEnrolled(course.id)" class="mt-2 mb-2">
                                <template v-if="getCourseData(course.id).is_completed">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                            <svg class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-black text-green-600 uppercase">Curso Completado</span>
                                    </div>
                                    <div class="w-full bg-green-100 dark:bg-green-900/20 rounded-full h-2 overflow-hidden">
                                        <div class="bg-green-500 h-full rounded-full w-full"></div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] uppercase font-black text-gray-400">Tu Progreso</span>
                                        <span class="text-xs font-black text-blue-600">{{ getCourseData(course.id).percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 ease-out" 
                                             :style="{ width: getCourseData(course.id).percentage + '%' }"></div>
                                    </div>
                                    <p v-if="getCourseData(course.id).percentage >= 100 && getCourseData(course.id).has_evaluations && !getCourseData(course.id).passed_evaluations"
                                       class="text-[9px] text-amber-600 font-bold mt-1 animate-pulse">
                                       ⚠️ Pendiente: Aprobar Evaluación Final
                                    </p>
                                </template>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span v-if="isEnrolled(course.id)" class="text-xs font-bold" :class="getCourseData(course.id).is_completed ? 'text-green-600' : 'text-blue-600'">
                                        {{ getCourseData(course.id).is_completed ? 'Certificado Listo' : 'Inscrito' }}
                                    </span>
                                </div>
                                <Link :href="route('empleado.courses.show', course.id)" 
                                      class="px-5 py-2 text-white rounded-lg text-sm font-black transition-all transform active:scale-95 shadow-md"
                                      :class="getCourseData(course.id).is_completed ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'">
                                    {{ getCourseData(course.id).is_completed ? 'Repasar' : 'Continuar' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
