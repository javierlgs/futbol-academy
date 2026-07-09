<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CalendarDaysIcon, TrophyIcon } from '@heroicons/vue/24/outline';
const props = defineProps({ proximosEventos: Object });
</script>

<template>
    <Head title="Mi Hijo" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">Progreso de mi Hijo</h1>
            
            <div v-if="Object.keys(proximosEventos).length === 0" class="bg-white rounded-3xl shadow-lg p-12 text-center">
                <p class="text-gray-500 text-lg">No tienes jugadores asignados</p>
            </div>

            <div v-for="(data, jugadorId) in proximosEventos" :key="jugadorId" class="bg-white rounded-3xl shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">{{ data.jugador.nombre_completo }}</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                            <CalendarDaysIcon class="w-6 h-6 text-blue-600" />
                            Próximos Entrenamientos
                        </h3>
                        <div v-if="data.entrenamientos.length === 0" class="text-gray-500 text-sm">
                            Sin entrenamientos programados
                        </div>
                        <div v-for="ent in data.entrenamientos" :key="ent.id" class="p-3 bg-blue-50 rounded-xl mb-2">
                            <p class="font-semibold">{{ ent.fecha }}</p>
                            <p class="text-sm text-gray-600">{{ ent.objetivo || 'Entrenamiento regular' }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                            <TrophyIcon class="w-6 h-6 text-green-600" />
                            Próximos Partidos
                        </h3>
                        <div v-if="data.partidos.length === 0" class="text-gray-500 text-sm">
                            Sin partidos programados
                        </div>
                        <div v-for="part in data.partidos" :key="part.id" class="p-3 bg-green-50 rounded-xl mb-2">
                            <p class="font-semibold">vs {{ part.rival }}</p>
                            <p class="text-sm text-gray-600">{{ part.fecha }} - {{ part.lugar || 'Por definir' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>