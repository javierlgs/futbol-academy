<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ entrenamientos: Object });

// Función auxiliar para calcular porcentajes de asistencia de forma segura (Mantenida intacta)
const obtenerPorcentajeAsistencia = (presentes, ausentes) => {
    const total = Number(presentes || 0) + Number(ausentes || 0);
    if (total === 0) return 0;
    return Math.round((Number(presentes || 0) / total) * 100);
};

// Función estética para las etiquetas de estado adaptada al estilo Neo-brutalista
const clasesEstado = (estado) => {
    switch (estado) {
        case 'realizado': return 'bg-emerald-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
        case 'planificado': return 'bg-blue-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
        default: return 'bg-gray-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
    }
};
</script>

<template>
    <Head title="Entrenamientos" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-purple-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Panel de Control
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 uppercase mt-2 tracking-tight">Bitácora de Entrenamientos</h1>
                    <p class="text-gray-500 text-xs font-bold mt-1">Monitoreo grupal, asistencia y rendimiento técnico en cancha.</p>
                </div>
                <Link :href="route('entrenamientos.create')"
                    class="w-full sm:w-auto px-6 py-3.5 bg-lime-400 hover:bg-lime-300 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl transition shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center active:translate-y-0.5">
                    ➕ Nuevo Entrenamiento
                </Link>
            </div>

            <div v-if="entrenamientos.data.length === 0" class="bg-white border-4 border-dashed border-gray-300 rounded-3xl p-12 text-center text-gray-400 font-black uppercase text-xs">
                🚩 No hay entrenamientos registrados en el sistema todavía.
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div v-for="ent in entrenamientos.data" :key="ent.id"
                    class="bg-white border-4 border-gray-900 rounded-3xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col justify-between hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                    
                    <div>
                        <div class="p-4 bg-gray-50 border-b-4 border-gray-900 flex justify-between items-start gap-4">
                            <div class="flex items-center gap-3">
                                <div class="px-3 py-2 bg-gray-900 text-white font-black rounded-xl text-center min-w-[45px] text-xs uppercase">
                                    ID
                                    <span class="block text-base text-amber-300 font-black">{{ ent.id }}</span>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-gray-900 uppercase tracking-tight">
                                        {{ ent.categoria?.nombre || 'Categoría General' }}
                                    </h3>
                                    <p class="text-[11px] font-black text-gray-500 uppercase flex items-center gap-1 mt-0.5">📅 {{ ent.fecha }}</p>
                                </div>
                            </div>
                            
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider" :class="clasesEstado(ent.estado)">
                                ● {{ ent.estado }}
                            </span>
                        </div>

                        <div class="p-4 grid grid-cols-3 gap-2 bg-white">
                            <div class="p-2.5 rounded-xl border-2 border-gray-900 bg-blue-50 text-center">
                                <p class="text-[9px] font-black text-blue-700 uppercase tracking-tight">Asistencia</p>
                                <p class="text-lg font-black text-gray-900 mt-0.5">
                                    {{ obtenerPorcentajeAsistencia(ent.total_presentes, ent.total_ausentes) }}%
                                </p>
                            </div>

                            <div class="p-2.5 rounded-xl border-2 border-gray-900 bg-emerald-50 text-center">
                                <p class="text-[9px] font-black text-emerald-700 uppercase tracking-tight">Evaluado</p>
                                <p class="text-lg font-black text-gray-900 mt-0.5">{{ ent.total_calificaciones || 0 }} <span class="text-[10px] text-gray-400">Jug</span></p>
                            </div>

                            <div class="p-2.5 rounded-xl border-2 border-gray-900 bg-amber-50 text-center">
                                <p class="text-[9px] font-black text-amber-700 uppercase tracking-tight">S. Mental</p>
                                <p class="text-xs font-black mt-1.5 uppercase truncate" :class="ent.total_ausentes > 4 ? 'text-amber-600' : 'text-gray-900'">
                                    🧠 Estable
                                </p>
                            </div>
                        </div>

                        <div v-if="ent.objetivo" class="px-4 pb-4 bg-white text-[11px] font-bold text-gray-700 line-clamp-2">
                            <span class="font-black text-gray-900 uppercase text-[9px] block text-gray-400">Objetivo Técnico:</span> 
                            {{ ent.objetivo }}
                        </div>
                    </div>

                    <div class="p-3 bg-gray-900 border-t-2 border-gray-900 grid grid-cols-2 gap-2">
                        <Link :href="route('entrenamientos.asistencia', ent.id)"
                            class="px-2 py-2 bg-white hover:bg-gray-100 text-gray-900 rounded-xl font-black text-[10px] uppercase transition text-center border-2 border-white shadow-sm">
                            📋 Asistencia
                        </Link>
                        
                        <Link :href="route('entrenamientos.evaluar', ent.id)"
                            class="px-2 py-2 bg-blue-400 hover:bg-blue-300 text-gray-900 rounded-xl font-black text-[10px] uppercase transition text-center border-2 border-gray-900 shadow-sm">
                            ★ Evaluar
                        </Link>
                        
                        <Link :href="route('radar.estacion', ent.id)"
                            class="px-2 py-2 bg-amber-400 hover:bg-amber-300 text-gray-900 rounded-xl font-black text-[10px] uppercase transition text-center border-2 border-gray-900 shadow-sm">
                            🤖 Tótem Alumnos
                        </Link>
                        
                        <Link :href="route('radar.tests', ent.id)"
                            class="px-2 py-2 bg-purple-400 hover:bg-purple-300 text-gray-900 rounded-xl font-black text-[10px] uppercase transition text-center border-2 border-gray-900 shadow-sm">
                            🏃‍♂️ Capturar Tests
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>