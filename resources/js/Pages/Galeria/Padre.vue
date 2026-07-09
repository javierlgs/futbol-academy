<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { PhotoIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ fotos: Object, jugadores: Array });
</script>

<template>
    <Head title="Galería de mi Hijo" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6 flex items-center gap-3">
                <PhotoIcon class="w-10 h-10 text-blue-600" />
                Fotos de Entrenamientos
            </h1>

            <div v-if="fotos.data.length === 0" class="bg-white rounded-3xl shadow-lg p-12 text-center">
                <p class="text-gray-500 text-lg">Aún no hay fotos publicadas</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="foto in fotos.data" :key="foto.id" 
                    class="bg-white rounded-3xl shadow-lg overflow-hidden">
                    <img :src="`/storage/${foto.ruta}`" 
                        class="w-full h-80 object-cover">
                    <div class="p-4">
                        <p class="font-bold text-lg">{{ foto.entrenamiento.categoria.nombre }}</p>
                        <p class="text-gray-600">{{ foto.entrenamiento.fecha }}</p>
                        <p v-if="foto.descripcion" class="text-gray-700 mt-2">{{ foto.descripcion }}</p>
                        <a :href="`/storage/${foto.ruta}`" download
                            class="block mt-3 text-center py-2 bg-blue-600 text-white rounded-xl font-bold">
                            Descargar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>