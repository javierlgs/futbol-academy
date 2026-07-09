<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    partido: { type: Object, required: true },
    jugadores: { type: Array, default: () => [] }
});

const form = useForm({
    alineaciones: props.jugadores.map(j => ({
        jugador_id: j.id,
        tipo: 'titular',
        minuto_entrada: 0,
        minuto_salida: 0,
        posicion_partido: j.posicion || 'ST',
        seleccionado: false
    }))
});

const enviarActa = () => {
    // Enviamos directamente alineaciones (solo los seleccionados)
    form.transform((data) => ({
        alineaciones: data.alineaciones.filter(a => a.seleccionado)
    })).post(route('partidos.alineacion.store', props.partido.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h1 class="text-xl font-black uppercase">Gutigol vs {{ partido.rival }}</h1>
            </div>

            <form @submit.prevent="enviarActa">
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div v-for="(ali, index) in form.alineaciones" :key="ali.jugador_id" 
                        class="border-2 border-gray-900 rounded-xl p-3 mb-2 flex items-center justify-between"
                        :class="ali.seleccionado ? 'bg-lime-50' : 'bg-gray-50'">
                        
                        <label class="flex items-center gap-3">
                            <input type="checkbox" v-model="ali.seleccionado" class="w-5 h-5 border-2 border-gray-900">
                            <span class="font-black text-xs uppercase">Jugador ID: {{ ali.jugador_id }}</span>
                        </label>

                        <div v-if="ali.seleccionado" class="flex gap-2">
                            <input type="number" v-model="ali.minuto_entrada" class="w-16 p-1 border-2 border-gray-900 rounded text-xs text-center" placeholder="Min E">
                            <input type="number" v-model="ali.minuto_salida" class="w-16 p-1 border-2 border-gray-900 rounded text-xs text-center" placeholder="Min S">
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="mt-4 w-full py-4 bg-lime-400 border-4 border-gray-900 font-black uppercase rounded-xl">
                    {{ form.processing ? 'Guardando...' : 'Guardar Acta' }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>