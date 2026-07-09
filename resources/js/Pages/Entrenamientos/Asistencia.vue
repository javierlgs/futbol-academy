<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    entrenamiento: Object,
    jugadores: Array,
    asistencias: Object
});

// 1. Creamos primero un mapa plano con JavaScript nativo para asegurar la reactividad
const asistenciaInicial = {};
props.jugadores.forEach(j => {
    // Si ya existe asistencia previa en la BD la usamos, de lo contrario por defecto 1 (Presente)
    asistenciaInicial[j.id] = props.asistencias[j.id] !== undefined ? props.asistencias[j.id] : 1;
});

// 2. Pasamos el objeto ya estructurado a useForm
const form = useForm({
    asistencias: asistenciaInicial
});

const marcarTodos = (valor) => {
    props.jugadores.forEach(j => {
        form.asistencias[j.id] = valor;
    });
};

const submit = () => {
    form.post(route('entrenamientos.guardarAsistencia', props.entrenamiento.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
    <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="bg-amber-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                Control de Asistencia
            </span>
            
            <h2 class="font-black text-2xl text-gray-900 uppercase mt-2 tracking-tight">
                Asistencia - {{ entrenamiento.categoria?.nombre }}
            </h2>
            
            <p class="text-gray-700 text-xs font-black mt-1">
                📅 {{ entrenamiento.fecha }}
            </p>
        </div>
        
        <Link :href="route('entrenamientos.index')" 
            class="w-full sm:w-auto py-2.5 px-4 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-300 text-center transition">
            ← Volver a Bitácora
        </Link>
    </div>
</template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4">
                <form @submit.prevent="submit">
                    <div class="bg-gray-900 rounded-lg overflow-hidden">
                        <div class="divide-y divide-gray-800">
                            <div v-for="jugador in jugadores" :key="jugador.id" 
                                 class="flex items-center justify-between p-4 hover:bg-gray-800">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold">
                                        {{ jugador.nombres.charAt(0) }}{{ jugador.apellidos.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-bold">{{ jugador.nombres }} {{ jugador.apellidos }}</p>
                                        <p class="text-gray-400 text-sm">{{ jugador.posicion || 'Sin posición' }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <button type="button"
                                            @click="form.asistencias[jugador.id] = 1"
                                            class="px-6 py-3 rounded-lg font-bold transition"
                                            :class="form.asistencias[jugador.id] === 1 
                                                ? 'bg-green-600 text-white' 
                                                : 'bg-gray-700 text-gray-400'">
                                        ✓ Presente
                                    </button>
                                    <button type="button"
                                            @click="form.asistencias[jugador.id] = 0"
                                            class="px-6 py-3 rounded-lg font-bold transition"
                                            :class="form.asistencias[jugador.id] === 0 
                                                ? 'bg-red-600 text-white' 
                                                : 'bg-gray-700 text-gray-400'">
                                        ✗ Ausente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white font-black px-8 py-3 rounded-lg">
                            {{ form.processing ? 'Guardando...' : 'Continuar a Evaluación →' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>