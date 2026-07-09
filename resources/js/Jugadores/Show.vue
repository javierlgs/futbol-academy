<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    jugador: Object,
    evolucion: Object
});

const chartOptions = (habilidad) => ({
    chart: { type: 'line', height: 250, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 3 },
    colors: ['#0EA5E9'],
    xaxis: { categories: props.evolucion[habilidad].fechas },
    yaxis: { min: 0, max: 5, tickAmount: 5 },
    markers: { size: 6 }
});
</script>

<template>
    <Head :title="jugador.nombre_completo" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <!-- HEADER JUGADOR -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl shadow-2xl p-8 mb-6 text-white">
                <div class="flex items-center gap-6">
                    <img :src="jugador.foto? `/storage/${jugador.foto}` : '/img/avatar.png'"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white">
                    <div>
                        <h1 class="text-4xl font-bold">{{ jugador.nombre_completo }}</h1>
                        <p class="text-xl text-blue-100">{{ jugador.categoria.nombre }} - {{ jugador.edad }} años</p>
                        <span class="inline-block mt-2 px-4 py-1 bg-white text-blue-700 rounded-full font-bold">
                            {{ jugador.posicion || 'Sin posición' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- DATOS REPRESENTANTE -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Datos del Jugador</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><span class="font-semibold">Nacimiento:</span> {{ jugador.fecha_nacimiento }}</p>
                        <p><span class="font-semibold">Estado:</span> {{ jugador.estado }}</p>
                        <p><span class="font-semibold">Sede:</span> {{ jugador.sede.nombre }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Representante</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><span class="font-semibold">Nombre:</span> {{ jugador.rep_nombres }} {{ jugador.rep_apellidos }}</p>
                        <p><span class="font-semibold">Relación:</span> {{ jugador.rep_relacion }}</p>
                        <p><span class="font-semibold">Teléfono:</span> {{ jugador.rep_telefono }}</p>
                        <p><span class="font-semibold">Correo:</span> {{ jugador.rep_correo || 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- GRÁFICAS DE EVOLUCIÓN -->
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Evolución por Habilidad</h2>

                <div v-if="Object.keys(evolucion).length === 0" class="text-center py-12 text-gray-500">
                    Sin evaluaciones registradas aún
                </div>

                <div v-for="(data, habilidad) in evolucion" :key="habilidad" class="mb-8">
                    <h3 class="text-lg font-bold mb-3">{{ habilidad }}</h3>
                    <VueApexCharts
                        type="line"
                        height="250"
                        :options="chartOptions(habilidad)"
                        :series="[{ name: 'Puntaje', data: data.puntajes }]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>