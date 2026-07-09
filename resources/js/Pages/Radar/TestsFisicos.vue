<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';

const props = defineProps({
    entrenamiento: Object,
    jugadores: Array,
    testsExistentes: Object
});

const form = useForm({
    marcas: []
});

const inicializarPlanilla = () => {
    if (!props.jugadores || props.jugadores.length === 0) {
        form.marcas = [];
        return;
    }
    
    form.marcas = props.jugadores.map(jugador => {
        const existente = (props.testsExistentes && props.testsExistentes[jugador.id]) ? props.testsExistentes[jugador.id] : {};
        return {
            jugador_id: jugador.id,
            nombre_completo: `${jugador.apellidos} ${jugador.nombres}`,
            foto: jugador.foto,
            // Inicializamos en 0 si no hay registro previo
            velocidad_segundos: existente.velocidad_segundos || 0,
            coordinacion_puntos: existente.coordinacion_puntos || 0,
            reaccion_segundos: existente.reaccion_segundos || 0,
            equilibrio_segundos: existente.equilibrio_segundos || 0,
            resistencia_vueltas: existente.resistencia_vueltas || 0,
        };
    });
};

onMounted(() => { inicializarPlanilla(); });
watch(() => props.jugadores, () => { inicializarPlanilla(); }, { deep: true, immediate: true });

const guardarPlanilla = () => {
    form.post(route('radar.tests.guardar', props.entrenamiento.id), {
        preserveScroll: true,
        onSuccess: () => alert('📋 ¡Puntuaciones de rendimiento guardadas!')
    });
};
</script>

<template>
    <Head title="Tests Físicos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-purple-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Evaluación Ágil (1-5 Estrellas)
                    </span>
                    <h2 class="font-black text-2xl text-gray-900 uppercase mt-2 tracking-tight">
                        🏃‍♂️ Tests Físicos y Motores
                    </h2>
                    <p class="text-gray-500 text-xs font-bold mt-1">Toca las estrellas para calificar el rendimiento de la sesión del {{ entrenamiento.fecha }}.</p>
                </div>
                <Link :href="route('entrenamientos.index')" 
                    class="py-2.5 px-4 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-300">
                    ← Volver
                </Link>
            </div>
        </template>

        <div class="py-6 min-h-screen bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                
                <form @submit.prevent="guardarPlanilla" class="space-y-6">
                    
                    <div v-if="form.marcas.length === 0" class="bg-amber-100 border-4 border-gray-900 text-gray-900 font-bold p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center">
                        ⚠️ No hay alumnos presentes marcados en la asistencia para este entrenamiento.
                    </div>

                    <div v-else class="bg-white border-4 border-gray-900 rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900 text-white text-[11px] uppercase font-black tracking-wider">
                                        <th class="p-4 border-b-4 border-gray-900 w-64">Alumno</th>
                                        <th class="p-4 border-b-4 border-gray-900 text-center">⏱️ Velocidad</th>
                                        <th class="p-4 border-b-4 border-gray-900 text-center">🧗 Coordinación</th>
                                        <th class="p-4 border-b-4 border-gray-900 text-center">⚡ Reacción</th>
                                        <th class="p-4 border-b-4 border-gray-900 text-center">🤸 Equilibrio</th>
                                        <th class="p-4 border-b-4 border-gray-900 text-center">🫁 Resistencia</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-900">
                                    <tr v-for="(marca, index) in form.marcas" :key="marca.jugador_id" class="hover:bg-gray-50 text-xs font-bold text-gray-900">
                                        
                                        <td class="p-3 flex items-center gap-3 border-r-2 border-gray-100">
                                            <div class="w-10 h-10 rounded-xl border-2 border-gray-900 bg-amber-200 overflow-hidden flex-shrink-0">
                                                <img v-if="marca.foto" :src="marca.foto.startsWith('/storage/') ? marca.foto : '/storage/' + marca.foto" class="w-full h-full object-cover" />
                                                <div v-else class="w-full h-full flex items-center justify-center font-black">🏃‍♂️</div>
                                            </div>
                                            <span class="uppercase tracking-tight text-gray-900 font-black">{{ marca.nombre_completo }}</span>
                                        </td>

                                        <td v-for="metrica in ['velocidad_segundos', 'coordinacion_puntos', 'reaccion_segundos', 'equilibrio_segundos', 'resistencia_vueltas']" :key="metrica" class="p-3 text-center border-r border-gray-100">
                                            <div class="flex items-center justify-center gap-1 select-none">
                                                <button v-for="estrella in 5" :key="estrella" type="button"
                                                    @click="form.marcas[index][metrica] = estrella"
                                                    class="text-xl transition-all duration-700 transform active:scale-150 focus:outline-none">
                                                    <span :class="estrella <= form.marcas[index][metrica] ? 'text-amber-400 drop-shadow-[1px_1px_0_rgba(0,0,0,1)]' : 'text-gray-200'">★</span>
                                                </button>
                                            </div>
                                            <span class="text-[9px] font-black text-gray-400 block mt-0.5">{{ form.marcas[index][metrica] || 0 }} / 5</span>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="form.marcas.length > 0" class="flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto py-3 px-6 bg-purple-400 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-purple-300 transition">
                            {{ form.processing ? 'Guardando Evaluaciones...' : '💾 Guardar Notas por Estrellas' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>