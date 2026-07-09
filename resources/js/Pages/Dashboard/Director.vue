<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({ promediosCategorias: Array });
</script>

<template>
    <Head title="Dashboard Director" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">Rendimiento por Categoría</h1>
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div v-for="cat in promediosCategorias" :key="cat.categoria" class="mb-4 p-4 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xl font-bold">{{ cat.categoria }}</p>
                            <p class="text-gray-600">{{ cat.jugadores }} jugadores</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-blue-600">{{ cat.promedio }}/5</p>
                            <p class="text-sm text-gray-500">Promedio</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>