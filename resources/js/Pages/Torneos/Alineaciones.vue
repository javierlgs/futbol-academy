<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { UserGroupIcon, ClockIcon, UsersIcon, CheckBadgeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    partido: Object,
    jugadores: Array,
});

const form = useForm({
    alineacion: props.partido.alineaciones || []
});

const guardarAlineacion = () => {
    form.post(route('partidos.alineacion.store', props.partido.id));
};
</script>

<template>
    <Head title="Gestión de Alineación" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <!-- HEADER DEL PARTIDO -->
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h1 class="text-3xl font-black uppercase tracking-tight">
                    Alineación: vs {{ partido.rival }}
                </h1>
                <p class="text-xs font-black text-gray-500 uppercase mt-2">
                    {{ partido.fecha }} | {{ partido.hora }} | {{ partido.lugar }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- PANEL TITULARES -->
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                        <CheckBadgeIcon class="w-6 h-6 text-emerald-500" /> Titulares
                    </h2>
                    <div class="space-y-4">
                        <div v-for="jugador in jugadores.filter(j => j.es_titular)" :key="jugador.id" 
                             class="flex items-center justify-between p-4 border-2 border-gray-900 rounded-2xl bg-emerald-50">
                            <span class="font-black uppercase text-sm">{{ jugador.nombres }} {{ jugador.apellidos }}</span>
                            <div class="flex items-center gap-2">
                                <ClockIcon class="w-4 h-4" />
                                <input type="number" class="w-16 border-2 border-gray-900 rounded-lg p-1 text-center font-bold text-xs" placeholder="Min">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL SUPLENTES -->
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                        <UsersIcon class="w-6 h-6 text-amber-500" /> Suplentes
                    </h2>
                    <div class="space-y-4">
                        <div v-for="jugador in jugadores.filter(j => !j.es_titular)" :key="jugador.id" 
                             class="flex items-center justify-between p-4 border-2 border-gray-900 rounded-2xl bg-amber-50">
                            <span class="font-black uppercase text-sm">{{ jugador.nombres }} {{ jugador.apellidos }}</span>
                            <button class="bg-gray-900 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">Ingresar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCIÓN FINAL -->
            <div class="flex justify-end">
                <button @click="guardarAlineacion" 
                        class="px-8 py-4 bg-lime-400 border-4 border-gray-900 font-black uppercase text-sm rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-lime-300 transition-all">
                    Guardar Alineación Oficial
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>