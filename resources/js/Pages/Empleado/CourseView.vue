<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lessons: Array,
    enrollment: Object,
    completedLessons: Array,
    evaluationResults: Object
});
</script>

<template>
    <Head title="Curso" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center text-gray-800 dark:text-gray-200">
                <Link :href="route('empleado.dashboard')" class="text-blue-600 mr-2 hover:underline">Cursos</Link>
                <span>/</span>
                <h2 class="ml-2 text-xl font-semibold leading-tight">{{ course.title }}</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden p-6">
                    <div class="mb-8 border-b pb-6 dark:border-gray-700">
                        <h3 class="text-2xl font-bold dark:text-white">Sobre este curso</h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">{{ course.description }}</p>
                    </div>

                    <h4 class="text-lg font-bold dark:text-white mb-4">Contenido del Curso</h4>
                    <div class="space-y-4">
                        <div v-for="lesson in lessons" :key="lesson.id" 
                             class="flex items-center justify-between p-4 border rounded-lg dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                             :class="completedLessons.includes(lesson.id) ? 'bg-green-50/50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : ''">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold mr-4"
                                     :class="completedLessons.includes(lesson.id) ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'">
                                    <template v-if="completedLessons.includes(lesson.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template v-else>{{ lesson.lesson_order }}</template>
                                </div>
                                <div>
                                    <span class="block font-medium dark:text-white" :class="completedLessons.includes(lesson.id) ? 'text-green-700 dark:text-green-400' : ''">
                                        {{ lesson.title }}
                                    </span>
                                    <span class="text-xs text-gray-400 uppercase">{{ lesson.content_type }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span v-if="completedLessons.includes(lesson.id)" class="text-xs font-bold text-green-600 uppercase">Completado</span>
                                <Link :href="route('empleado.lessons.show', [course.id, lesson.id])" 
                                      class="text-blue-600 font-bold hover:underline">
                                    {{ completedLessons.includes(lesson.id) ? 'Repasar' : 'Estudiar' }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-if="course.evaluations.length > 0" class="mt-8 pt-6 border-t dark:border-gray-700">
                        <h4 class="text-lg font-bold dark:text-white mb-4 italic text-purple-600 font-black">Evaluación Final</h4>
                        <div v-for="evalu in course.evaluations" :key="evalu.id" 
                             class="flex items-center justify-between p-6 bg-white dark:bg-gray-800 border-2 rounded-2xl shadow-sm transition-all transition h-full"
                             :class="evaluationResults[evalu.id] ? (evaluationResults[evalu.id].passed ? 'border-green-500 bg-green-50/10' : 'border-red-500 bg-red-50/10') : 'border-purple-200 dark:border-purple-900 shadow-sm border-dashed'">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-xl"
                                     :class="evaluationResults[evalu.id] ? (evaluationResults[evalu.id].passed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') : 'bg-purple-100 text-purple-600'">
                                    <template v-if="evaluationResults[evalu.id]">
                                        <template v-if="evaluationResults[evalu.id].passed">✓</template>
                                        <template v-else>✕</template>
                                    </template>
                                    <template v-else>?</template>
                                </div>
                                <div>
                                    <span class="block font-black text-lg dark:text-white">{{ evalu.title }}</span>
                                    <div v-if="evaluationResults[evalu.id]" class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase"
                                              :class="evaluationResults[evalu.id].passed ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
                                            {{ evaluationResults[evalu.id].passed ? 'Aprobado' : 'Reprobado' }}
                                        </span>
                                        <span class="text-sm font-bold dark:text-gray-300">Puntaje: {{ evaluationResults[evalu.id].score }}%</span>
                                    </div>
                                    <span v-else class="text-xs text-gray-500 italic">No intentado aún</span>
                                </div>
                            </div>
                            <Link :href="route('empleado.quizzes.show', evalu.id)" 
                                  class="px-8 py-3 bg-purple-600 text-white font-black rounded-xl hover:bg-purple-700 transition shadow-lg hover:shadow-purple-500/20 active:scale-95">
                                {{ evaluationResults[evalu.id] ? 'Reintentar' : 'Iniciar Examen' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
