<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    allLessons: Array
});

const form = useForm({});
const videoWatchedEnough = ref(false);
const showWarning = ref(false);
const showSuccess = ref(false);

const isYouTube = (url) => {
    return url && (url.includes('youtube.com') || url.includes('youtu.be'));
};

const getYouTubeEmbedUrl = (url) => {
    if (url.includes('embed')) return url;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? `https://www.youtube.com/embed/${match[2]}` : url;
};

const handleTimeUpdate = (event) => {
    const video = event.target;
    if (video.duration > 0 && (video.duration - video.currentTime) <= 10) {
        videoWatchedEnough.value = true;
    }
};

const completeAndNext = () => {
    if (props.lesson.content_type === 'video' && !isYouTube(props.lesson.content_url) && !videoWatchedEnough.value) {
        showWarning.value = true;
        return;
    }
    
    form.post(route('empleado.lessons.complete', props.lesson.id), {
        onSuccess: () => {
            showSuccess.value = true;
        }
    });
};

const goToCourse = () => {
    router.visit(route('empleado.courses.show', props.course.id));
};
</script>

<template>
    <Head title="Lección" />

    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center text-gray-800 dark:text-gray-200">
                <Link :href="route('empleado.courses.show', course.id)" class="text-blue-600 hover:underline">← Volver al curso</Link>
                <h2 class="text-xl font-semibold leading-tight">{{ lesson.title }}</h2>
                <span></span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden p-6 min-h-[500px]">
                        <!-- Video Support (YouTube or Local) -->
                        <div v-if="lesson.content_type === 'video'" class="aspect-video w-full bg-black rounded mb-6 overflow-hidden">
                            <iframe v-if="isYouTube(lesson.content_url)" 
                                    :src="getYouTubeEmbedUrl(lesson.content_url)" 
                                    class="w-full h-full" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                            <video v-else 
                                   :src="lesson.content_url" 
                                   controls 
                                   class="w-full h-full" 
                                   @timeupdate="handleTimeUpdate"
                                   @ended="videoWatchedEnough = true"></video>
                        </div>

                        <!-- PDF Support -->
                        <div v-if="lesson.content_type === 'pdf'" class="w-full h-[600px] mb-6 border rounded overflow-hidden">
                            <iframe :src="lesson.content_url" class="w-full h-full"></iframe>
                        </div>

                        <!-- Text Support -->
                        <div v-if="lesson.content_type === 'text'" class="prose dark:prose-invert max-w-none mb-6">
                            {{ lesson.content_text }}
                        </div>

                        <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end">
                            <button @click="completeAndNext" class="px-6 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700 transition shadow-lg">
                                Marcar como Completada
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Lesson List -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 h-fit">
                    <h4 class="font-bold dark:text-white mb-4 border-b pb-2">Contenido</h4>
                    <ul class="space-y-2">
                        <li v-for="item in allLessons" :key="item.id">
                            <Link :href="route('empleado.lessons.show', [course.id, item.id])" 
                                  class="block p-2 rounded text-sm"
                                  :class="item.id === lesson.id ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'">
                                {{ item.lesson_order }}. {{ item.title }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Warning Modal -->
        <div v-if="showWarning" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-8 transform transition-all scale-100">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-6">
                        <svg class="h-10 w-10 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¡Espera un momento!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Para asegurar tu aprendizaje, debes terminar de ver el video antes de marcar la lección como completada.
                    </p>
                    <button @click="showWarning = false" 
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-lg">
                        Entendido, seguiré viendo
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-8 transform transition-all scale-100 animate-in fade-in zoom-in duration-300">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                        <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">¡Lección Completada!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                        Lo has hecho excelente. Sigue así para terminar el curso.
                    </p>
                    <button @click="goToCourse" 
                            class="w-full py-4 px-6 bg-green-600 hover:bg-green-700 text-white font-black text-xl rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-[0_10px_20px_-10px_rgba(22,163,74,0.5)]">
                        Continuar aprendiendo
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
