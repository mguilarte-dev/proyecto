<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    course: Object,
    evaluations: Array
});

const isModalOpen = ref(false);
const form = useForm({
    title: '',
    min_score: 70,
    questions: [
        { text: '', answers: [{ text: '', is_correct: true }, { text: '', is_correct: false }] }
    ]
});

const addQuestion = () => {
    form.questions.push({ text: '', answers: [{ text: '', is_correct: true }, { text: '', is_correct: false }] });
};

const removeQuestion = (index) => {
    form.questions.splice(index, 1);
};

const addAnswer = (qIndex) => {
    form.questions[qIndex].answers.push({ text: '', is_correct: false });
};

const removeAnswer = (qIndex, aIndex) => {
    form.questions[qIndex].answers.splice(aIndex, 1);
};

const submit = () => {
    form.post(route('admin.courses.evaluations.store', props.course.id), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};
</script>

<template>
    <Head title="Evaluaciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center text-gray-800 dark:text-gray-200">
                <div>
                     <Link :href="route('admin.courses.index')" class="text-blue-600 hover:underline">Cursos</Link>
                     <span class="mx-2">/</span>
                     <h2 class="inline text-xl font-semibold leading-tight">Evaluaciones</h2>
                </div>
                <button @click="isModalOpen = true" class="px-4 py-2 bg-purple-600 text-white rounded">Nueva Evaluación</button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="evalu in evaluations" :key="evalu.id" class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg border dark:border-gray-700">
                        <h3 class="text-xl font-bold dark:text-white">{{ evalu.title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Puntaje mínimo: {{ evalu.min_score }}%</p>
                        <div class="mt-4">
                            <h4 class="font-medium text-sm dark:text-gray-300">Preguntas: {{ evalu.questions.length }}</h4>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button @click="form.delete(route('admin.evaluations.destroy', evalu.id))" class="text-red-600 font-medium">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Evaluation Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false" maxWidth="4xl">
            <div class="p-6">
                <h3 class="text-lg font-bold dark:text-white mb-4">Nueva Evaluación</h3>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block dark:text-gray-200 text-sm font-bold">Título de la Evaluación</label>
                            <input v-model="form.title" type="text" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required />
                        </div>
                        <div>
                            <label class="block dark:text-gray-200 text-sm font-bold">Puntaje Mínimo (%)</label>
                            <input v-model="form.min_score" type="number" class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" required />
                        </div>
                    </div>

                    <div class="border-t pt-4 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold dark:text-white">Banco de Preguntas</h4>
                            <button type="button" @click="addQuestion" class="text-blue-600 text-sm font-bold">+ Agregar Pregunta</button>
                        </div>
                        
                        <div v-for="(q, qIdx) in form.questions" :key="qIdx" class="bg-gray-50 dark:bg-gray-700 p-4 rounded mb-4">
                            <div class="flex justify-between">
                                <label class="block text-sm font-bold dark:text-white">Pregunta {{ qIdx + 1 }}</label>
                                <button type="button" @click="removeQuestion(qIdx)" class="text-red-500 text-xs">Eliminar</button>
                            </div>
                            <input v-model="q.text" type="text" class="mt-2 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-white" placeholder="Ej: ¿Cuál es el procedimiento de seguridad...?" required />
                            
                            <div class="mt-4 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase">Respuestas</label>
                                <div v-for="(a, aIdx) in q.answers" :key="aIdx" class="flex items-center gap-2">
                                    <input type="checkbox" v-model="a.is_correct" class="rounded text-blue-600" />
                                    <input v-model="a.text" type="text" class="flex-1 rounded border-gray-300 text-sm dark:bg-gray-800 dark:text-white" placeholder="Texto de la respuesta..." required />
                                    <button type="button" @click="removeAnswer(qIdx, aIdx)" class="text-red-500 text-xs hover:text-red-700" v-if="q.answers.length > 1">Eliminar</button>
                                </div>
                                <button type="button" @click="addAnswer(qIdx)" class="text-xs text-blue-500 mt-2"> + Añadir opción</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="text-gray-500">Cancelar</button>
                        <button class="bg-purple-600 text-white px-6 py-2 rounded font-bold" :disabled="form.processing">Guardar Evaluación</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
