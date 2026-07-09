<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    torneo: { type: Object, required: true },
    partidos: { type: Array, default: () => [] },
    jugadores: { type: Array, default: () => [] }
});

const modalConvocatoria = ref(false);
const partidoSeleccionado = ref(null);

const form = useForm({
    rival: '',
    fecha: '',
    hora: '',
    jugadores_ids: []
});

const abrirConvocatoria = (partido) => {
    partidoSeleccionado.value = partido;
    modalConvocatoria.value = true;
};

const registrarPartido = () => {
    form.post(route('torneos.guardarPartido', props.torneo.id), {
        onSuccess: () => form.reset()
    });
};

const guardarConvocatoria = () => {
    form.post(route('partidos.guardarConvocatoria', partidoSeleccionado.value.id), {
        onSuccess: () => {
            modalConvocatoria.value = false;
            alert('¡Convocatoria exitosa!');
        }
    });
};
</script>

<template>
    <Head :title="'Fixture - ' + torneo.nombre" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <Link :href="route('torneos.index')" class="text-xs font-black uppercase text-purple-600 hover:underline">
                        ⬅️ Volver a Torneos
                    </Link>
                    <h1 class="text-2xl font-black text-gray-900 uppercase mt-1 tracking-tight">📅 Calendario de Partidos</h1>
                    <p class="text-gray-500 text-xs font-bold mt-0.5 uppercase">{{ torneo.nombre }} — {{ torneo.categoria }}</p>
                </div>
            </div>

            <div class="bg-amber-300 border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-6">
                <h2 class="font-black text-xs uppercase mb-4 tracking-wider">Añadir Nuevo Encuentro</h2>
                <form @submit.prevent="registrarPartido" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <input v-model="form.rival" placeholder="Nombre del Rival" class="border-2 border-gray-900 rounded-xl p-2 font-black text-xs uppercase">
                    <input v-model="form.fecha" type="date" class="border-2 border-gray-900 rounded-xl p-2 font-black text-xs uppercase">
                    <input v-model="form.hora" type="time" class="border-2 border-gray-900 rounded-xl p-2 font-black text-xs uppercase">
                    <button type="submit" class="bg-gray-900 text-white font-black py-2 rounded-xl uppercase text-xs hover:bg-gray-800 transition">
                        Guardar Encuentro
                    </button>
                </form>
            </div>

            <div v-if="partidos.length > 0" class="space-y-4">
                <div v-for="(partido, index) in partidos" :key="partido.id"
                    class="bg-white border-4 border-gray-900 rounded-3xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-center gap-4">
                    
                    <div class="text-center sm:text-left shrink-0">
                        <span class="bg-gray-900 text-white font-mono text-[10px] px-2 py-0.5 rounded uppercase font-black">Fecha {{ index + 1 }}</span>
                        <p class="text-xs font-black text-gray-900 mt-1 uppercase">📆 {{ partido.fecha }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">🕒 {{ partido.hora }} HS</p>
                    </div>

                    <div class="flex items-center justify-center gap-4 w-full sm:w-auto my-2 sm:my-0">
                        <div class="text-right w-24 sm:w-32 font-black uppercase text-xs text-gray-900 truncate">Gutigol</div>
                        <div class="flex items-center gap-2 bg-gray-50 border-2 border-gray-900 px-4 py-2 rounded-xl font-mono text-sm font-black">
                            <span :class="partido.jugado ? 'text-gray-900' : 'text-gray-300'">{{ partido.jugado ? partido.resultado_gutigol : '-' }}</span>
                            <span class="text-gray-400 text-xs">vs</span>
                            <span :class="partido.jugado ? 'text-gray-900' : 'text-gray-300'">{{ partido.jugado ? partido.resultado_rival : '-' }}</span>
                        </div>
                        <div class="text-left w-24 sm:w-32 font-black uppercase text-xs text-gray-900 truncate">{{ partido.rival }}</div>
                    </div>

                    <div class="w-full sm:w-auto shrink-0 flex gap-2 items-center">
                        <button v-if="partido.estado === 'CONVOCAR'" @click="abrirConvocatoria(partido)"
                            class="bg-amber-300 p-2 rounded-xl font-black text-[10px] uppercase border-2 border-gray-900 transition hover:scale-105">
                            📋 CONVOCAR
                        </button>
                        
                        <Link :href="route('partidos.detalles', partido.id)"
                            class="px-4 py-2 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase border-2 border-gray-900 transition hover:scale-105">
                            ⚽ GESTIONAR PARTIDO
                        </Link>
                    </div>
                </div> </div> <div v-if="modalConvocatoria" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                <div class="bg-white border-4 border-gray-900 p-6 rounded-3xl w-full max-w-sm shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="font-black text-lg uppercase mb-4">Jugadores Disponibles</h2>
                    <div class="space-y-2 mb-6 max-h-60 overflow-y-auto">
                        <label v-for="j in jugadores" :key="j.id" class="flex items-center gap-3 p-2 border-b">
                            <input type="checkbox" :value="j.id" v-model="form.jugadores_ids">
                            <span class="font-bold text-sm">{{ j.nombres }}</span>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <button @click="modalConvocatoria = false" class="flex-1 p-3 border-2 border-gray-900 rounded-xl font-bold uppercase">Cerrar</button>
                        <button @click="guardarConvocatoria" class="flex-1 p-3 bg-gray-900 text-white rounded-xl font-black uppercase">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 