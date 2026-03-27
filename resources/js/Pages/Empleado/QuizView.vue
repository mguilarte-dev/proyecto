<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    evaluation: Object
});

const page = usePage();
const form = useForm({
    answers: {} // Map question_id to answer_id
});

const showResultModal = ref(false);
const quizResult = computed(() => page.props.flash.quiz_result);

// Watch for flash message to show modal
watch(() => page.props.flash.quiz_result, (newVal) => {
    if (newVal) {
        showResultModal.value = true;
    }
}, { immediate: true });

const submit = () => {
    const result = {
        answers: Object.values(form.answers).filter(answer => answer !== null)
    };
    form.transform((data) => result).post(route('empleado.quizzes.submit', props.evaluation.id), {
        onSuccess: () => {
            // Modal handled by watcher
        }
    });
};

const handleRadioClick = (questionId, answerId) => {
    if (form.answers[questionId] === answerId) {
        form.answers[questionId] = null;
    }
};

const goToCourse = () => {
    router.visit(route('empleado.courses.show', props.evaluation.course_id));
};
</script>

<template>
    <Head title="Evaluación" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 text-center">
                {{ evaluation.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-12">
                        <div v-for="(question, qIdx) in evaluation.questions" :key="question.id" class="space-y-4">
                            <h3 class="text-lg font-bold dark:text-white">
                                <span class="text-blue-600 mr-2">{{ qIdx + 1 }}.</span>
                                {{ question.question_text }}
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-3">
                                <label v-for="answer in question.answers" :key="answer.id" 
                                       class="flex items-center p-4 border rounded-lg dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                       :class="form.answers[question.id] == answer.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''">
                                    <input type="radio" 
                                           v-model="form.answers[question.id]" 
                                           :name="'question_' + question.id" 
                                           :value="answer.id" 
                                           @click="handleRadioClick(question.id, answer.id)"
                                           class="text-blue-600" />
                                    <span class="ml-3 dark:text-gray-200">{{ answer.answer_text }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-8 border-t dark:border-gray-700 flex flex-col items-center">
                            <p class="text-sm text-gray-500 mb-4">Puedes dejar preguntas sin responder si no estás seguro. Haz clic en una opción seleccionada para deseleccionarla.</p>
                            <button class="w-full md:w-auto px-12 py-3 bg-purple-600 text-white font-bold rounded-full hover:bg-purple-700 shadow-lg transition" :disabled="form.processing">
                                Finalizar y Enviar Evaluación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Modal (Success or Fail) -->
        <div v-if="showResultModal && quizResult" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-md">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all scale-100 animate-in fade-in zoom-in duration-300">
                <div class="text-center">
                    <!-- Icon -->
                    <div v-if="quizResult.passed" class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                        <svg class="h-14 w-14 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div v-else class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/30 mb-6">
                        <svg class="h-14 w-14 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h3 class="text-3xl font-black mb-2" :class="quizResult.passed ? 'text-green-600' : 'text-red-600'">
                        {{ quizResult.passed ? '¡Felicidades!' : 'Sigue intentando' }}
                    </h3>
                    
                    <p class="text-gray-600 dark:text-gray-400 mb-2 font-medium">
                        Tu puntaje fue de:
                    </p>
                    <div class="text-5xl font-black mb-6" :class="quizResult.passed ? 'text-green-600' : 'text-red-600'">
                        {{ quizResult.score }}%
                    </div>

                    <p class="text-gray-500 dark:text-gray-400 mb-8 italic">
                        {{ quizResult.passed ? 'Has aprobado satisfactoriamente esta evaluación.' : 'No has alcanzado el puntaje mínimo requerido.' }}
                    </p>

                    <button @click="goToCourse" 
                            class="w-full py-4 px-6 text-white font-black text-xl rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-xl"
                            :class="quizResult.passed ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                        {{ quizResult.passed ? 'Continuar' : 'Entendido' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
