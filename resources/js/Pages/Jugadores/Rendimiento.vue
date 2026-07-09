<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    jugador: {
        type: Object,
        default: () => ({ id: 1, nombres: 'Javier', apellidos: 'Guzmán', foto: null, categoria: 'Sub-12', posicion: 'Delantero' })
    },
    metricas: {
        type: Object,
        default: () => ({
            tecnico_promedio: 4.2,
            asistencia_porcentaje: 95,
            goles_totales: 6,
            asistencias_totales: 3,
            test_velocidad_mejor: '4.15s',
            test_salto_mejor: '38cm'
        })
    },
    alertasEmocionales: {
        type: Array,
        default: () => [
            { variable: 'Sueño', estado: 'verde', mensaje: 'Duerme excelente' },
            { variable: 'Ánimo', estado: 'amarillo', mensaje: 'Energía moderada esta semana' },
            { variable: 'Dolor Físico', estado: 'rojo', mensaje: 'Molestia reportada en rodilla izquierda' }
        ]
    }
});

const colorSemaforo = (estado) => {
    switch (estado) {
        case 'verde': return 'bg-emerald-400 border-emerald-600 text-gray-900';
        case 'amarillo': return 'bg-amber-400 border-amber-600 text-gray-900';
        case 'rojo': return 'bg-rose-400 border-rose-600 text-gray-900';
        default: return 'bg-gray-300 border-gray-400';
    }
};
</script>

<template>
    <Head :title="'Rendimiento - ' + jugador.nombres" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                    <div class="w-20 h-20 rounded-2xl border-4 border-gray-900 bg-amber-200 overflow-hidden flex-shrink-0 flex items-center justify-center text-3xl font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <img v-if="jugador.foto" :src="jugador.foto" class="w-full h-full object-cover" />
                        <span v-else>⚽</span>
                    </div>
                    <div>
                        <span class="bg-gray-900 text-amber-300 font-black text-[10px] px-2.5 py-0.5 rounded-md uppercase">
                            {{ jugador.categoria }}
                        </span>
                        <h1 class="text-2xl font-black text-gray-900 uppercase mt-1 tracking-tight">
                            {{ jugador.nombres }} {{ jugador.apellidos }}
                        </h1>
                        <p class="text-gray-500 text-xs font-bold uppercase mt-0.5">Posición preferente: {{ jugador.posicion }}</p>
                    </div>
                </div>
                <Link :href="route('entrenamientos.index')" class="w-full sm:w-auto py-2.5 px-5 bg-gray-100 hover:bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-center">
                    ← Volver al Panel
                </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <h3 class="font-black text-xs uppercase tracking-widest text-gray-400 border-b-2 pb-1">📊 Desempeño Diario</h3>
                    
                    <div class="p-4 bg-blue-50 border-2 border-gray-900 rounded-2xl text-center">
                        <span class="text-[10px] font-black uppercase text-blue-700 block">Evaluación Técnica (★)</span>
                        <p class="text-4xl font-black text-gray-900 mt-1">{{ metricas.tecnico_promedio }} <span class="text-base text-gray-400">/ 5</span></p>
                    </div>

                    <div class="p-4 bg-emerald-50 border-2 border-gray-900 rounded-2xl text-center">
                        <span class="text-[10px] font-black uppercase text-emerald-700 block">Asistencia a Prácticas</span>
                        <p class="text-4xl font-black text-gray-900 mt-1">{{ metricas.asistencia_porcentaje }}%</p>
                    </div>
                </div>

                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <h3 class="font-black text-xs uppercase tracking-widest text-gray-400 border-b-2 pb-1">🏃‍♂️ Test de Aptitud Física</h3>
                    
                    <div class="p-4 bg-purple-50 border-2 border-gray-900 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-black uppercase text-purple-700 block">Velocidad Lineal (30m)</span>
                            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ metricas.test_velocidad_mejor }}</p>
                        </div>
                        <span class="text-2xl">⏱️</span>
                    </div>

                    <div class="p-4 bg-purple-50 border-2 border-gray-900 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-black uppercase text-purple-700 block">Salto Vertical</span>
                            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ metricas.test_salto_mejor }}</p>
                        </div>
                        <span class="text-2xl">🚀</span>
                    </div>
                </div>

                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <h3 class="font-black text-xs uppercase tracking-widest text-gray-400 border-b-2 pb-1">🧠 Reporte de Salud (Tótem)</h3>
                    
                    <div class="space-y-2">
                        <div v-for="alerta in alertasEmocionales" :key="alerta.variable"
                            class="p-2.5 border-2 border-gray-900 rounded-xl flex items-center gap-3 bg-gray-50">
                            <span class="px-2 py-1 border-2 text-[10px] font-black uppercase rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]" :class="colorSemaforo(alerta.estado)">
                                {{ alerta.variable }}
                            </span>
                            <p class="text-[11px] font-bold text-gray-700 uppercase tracking-tight truncate">{{ alerta.mensaje }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-gray-900 text-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-around text-center">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Goles Marcados</span>
                    <p class="text-3xl font-black text-amber-300 mt-1">⚽ {{ metricas.goles_totales }}</p>
                </div>
                <div class="h-10 w-1 bg-gray-800 rounded"></div>
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Asistencias Clave</span>
                    <p class="text-3xl font-black text-purple-300 mt-1">🎯 {{ metricas.asistencias_totales }}</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>