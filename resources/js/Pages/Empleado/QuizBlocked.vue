<script setup>
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    evaluation: Object,
    course: Object,
    totalLessons: Number,
    completedLessons: Number,
    completedLessonIds: Array,
    message: String,
    isPassed: Boolean,
    isAttemptsExhausted: Boolean,
    score: Number,
    totalAttempts: Number,
    maxAttempts: Number
});
</script>

<template>
    <Head title="Evaluación Bloqueada" />

    <div class="min-h-screen bg-primary-50 py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <Link href="/dashboard" class="inline-block mb-6">
                    <ApplicationLogo class="h-12 w-auto mx-auto" />
                </Link>
                <h1 class="text-3xl font-bold text-neutral-900 mb-2">
                    Evaluación No Disponible
                </h1>
                <p class="text-lg text-neutral-600">
                    {{ course.title }}
                </p>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-2xl shadow-lg border border-primary-200 p-8">
                <!-- Success Icon (If Already Passed) -->
                <div v-if="isPassed" class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-success-100 mb-4">
                        <svg class="h-10 w-10 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-neutral-900 mb-2">
                        Evaluación Completada
                    </h2>
                    <p class="text-neutral-600 max-w-md mx-auto mb-4">
                        {{ message }}
                    </p>
                    <div class="bg-success-50 rounded-xl p-4 mb-4">
                        <p class="text-sm text-neutral-600 mb-1">Tu puntaje fue:</p>
                        <p class="text-3xl font-bold text-success-600">{{ score }}%</p>
                    </div>
                </div>

                <!-- Attempts Exhausted Icon -->
                <div v-else-if="isAttemptsExhausted" class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-error-100 mb-4">
                        <svg class="h-10 w-10 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-neutral-900 mb-2">
                        Intentos Agotados
                    </h2>
                    <p class="text-neutral-600 max-w-md mx-auto mb-4">
                        {{ message }}
                    </p>
                    <div class="bg-error-50 rounded-xl p-4">
                        <p class="text-sm text-neutral-600">Intentos utilizados: {{ totalAttempts }} / {{ maxAttempts }}</p>
                    </div>
                </div>

                <!-- Default Warning Icon (Lessons Not Completed) -->
                <div v-else class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-warning-100 mb-4">
                        <svg class="h-10 w-10 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-neutral-900 mb-2">
                        Material de Estudio Requerido
                    </h2>
                    <p class="text-neutral-600 max-w-md mx-auto">
                        {{ message }}
                    </p>
                </div>

                <!-- Progress Info -->
                <div v-if="!isPassed && !isAttemptsExhausted" class="bg-neutral-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4">
                        Progreso de Visualización del Material
                    </h3>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-neutral-600 mb-2">
                            <span>Lecciones completadas</span>
                            <span>{{ completedLessons }} de {{ totalLessons }}</span>
                        </div>
                        <div class="w-full bg-neutral-200 rounded-full h-3">
                            <div
                                class="bg-primary-600 h-3 rounded-full transition-all duration-300"
                                :style="{ width: `${(completedLessons / totalLessons) * 100}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Lessons List -->
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-neutral-700 mb-3">
                            Estado de visualización de las lecciones:
                        </p>
                        <div class="grid gap-2 max-h-40 overflow-y-auto">
                            <div
                                v-for="lesson in course.lessons"
                                :key="lesson.id"
                                class="flex items-center justify-between p-3 bg-white rounded-lg border"
                                :class="completedLessonsIds.includes(lesson.id) ? 'border-success-200 bg-success-50' : 'border-neutral-200'"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center"
                                        :class="completedLessonIds.includes(lesson.id) ? 'bg-success-500 text-white' : 'bg-neutral-300 text-neutral-600'"
                                    >
                                        <svg v-if="completedLessonIds.includes(lesson.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span v-else class="text-xs font-medium">{{ lesson.lesson_order }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900">{{ lesson.title }}</p>
                                        <p class="text-xs text-neutral-500">{{ lesson.content_type }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <template v-if="isPassed || isAttemptsExhausted">
                        <!-- Show only return home for completed or exhausted -->
                        <Link href="/dashboard">
                            <PrimaryButton class="w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Volver al Inicio
                            </PrimaryButton>
                        </Link>
                    </template>
                    <template v-else>
                        <!-- Show material viewing for incomplete lessons -->
                        <Link :href="route('empleado.courses.show', course.id)">
                            <PrimaryButton class="w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Visualizar Material
                            </PrimaryButton>
                        </Link>

                        <Link href="/dashboard">
                            <PrimaryButton variant="outline" class="w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Volver al Inicio
                            </PrimaryButton>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    // Component logic if needed
}
</script>