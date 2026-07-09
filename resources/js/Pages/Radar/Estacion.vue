<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    entrenamiento: Object,
    jugadores: Array,
    respondieronHoy: Array
});

// Estado para controlar qué niño está respondiendo en este instante
const jugadorSeleccionado = ref(null);

// Formulario reactivo semáforo
const form = useForm({
    jugador_id: null,
    animo_entrenamiento: 3, 
    calidad_sueno: 3,
    siente_dolor: false,
    diversion: 3,
    cansancio_extremo: 2
});

// Al tocar el nombre del niño, se abre su panel
const seleccionarJugador = (jugador) => {
    jugadorSeleccionado.value = jugador;
    form.jugador_id = jugador.id;
    // Reseteamos el formulario a valores limpios por defecto (Verdes)
    form.animo_entrenamiento = 3;
    form.calidad_sueno = 3;
    form.siente_dolor = false;
    form.diversion = 3;
    form.cansancio_extremo = 1;
};

const enviarEncuesta = () => {
    form.post(route('radar.estacion.guardar', props.entrenamiento.id), {
        preserveScroll: true,
        onSuccess: () => {
            // ¡Magia! Se limpia la pantalla por completo para el niño que viene atrás en la fila
            jugadorSeleccionado.value = null;
            form.reset();
        }
    });
};
</script>

<template>
    <Head title="Estación de Salida" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex justify-between items-center">
                <div>
                    <span class="bg-emerald-300 text-gray-900 border-2 border-gray-900 font-black px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider">
                        Estación de Salida Táctil
                    </span>
                    <h2 class="font-black text-xl text-gray-900 uppercase mt-1 tracking-tight">
                        ⚽ ¿Quién sigue en la fila?
                    </h2>
                </div>
                <Link :href="route('entrenamientos.index')" 
                    class="py-2 px-3 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-300">
                    Salir
                </Link>
            </div>
        </template>

        <div class="py-6 min-h-screen bg-gray-100">
            <div class="max-w-6xl mx-auto px-4">
                
                <div v-if="!jugadorSeleccionado" class="space-y-4">
                    <p class="text-xs font-black text-gray-500 uppercase tracking-wider text-center sm:text-left">👉 Busca tu nombre y tócalo para calificar tu día:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <button type="button" v-for="jugador in jugadores" :key="jugador.id"
                            @click="seleccionarJugador(jugador)"
                            :disabled="respondieronHoy.includes(jugador.id)"
                            class="bg-white border-4 border-gray-900 rounded-2xl p-3 flex items-center gap-3 text-left shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition active:translate-x-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]"
                            :class="respondieronHoy.includes(jugador.id) ? 'opacity-40 bg-gray-100 border-gray-400 shadow-none cursor-not-allowed' : 'hover:bg-gray-50'">
                            
                            <div class="w-12 h-12 rounded-xl border-2 border-gray-900 bg-amber-200 flex-shrink-0 overflow-hidden">
                                <img v-if="jugador.foto" :src="`/storage/${jugador.foto}`" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center font-black text-gray-700 text-lg">🏃‍♂️</div>
                            </div>

                            <div class="overflow-hidden">
                                <h4 class="font-black text-xs text-gray-900 uppercase truncate">{{ jugador.apellidos }}</h4>
                                <p class="font-bold text-[11px] text-gray-600 truncate">{{ jugador.nombres }}</p>
                                <span v-if="respondieronHoy.includes(jugador.id)" class="text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-400 px-1 rounded uppercase">Listo ✓</span>
                            </div>
                        </button>
                    </div>
                </div>

                <div v-else class="max-w-lg mx-auto bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6 animate-in zoom-in-95 duration-100">
                    
                    <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-2xl border-2 border-gray-900">
                        <div class="w-14 h-14 rounded-xl border-2 border-gray-900 bg-amber-200 overflow-hidden flex-shrink-0">
<img v-if="jugador.foto" :src="'/storage/' + jugador.foto" class="w-full h-full object-cover" />                            <div v-else class="w-full h-full flex items-center justify-center font-black text-2xl">👦</div>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase">Estás respondiendo como:</p>
                            <h3 class="font-black text-base text-gray-900 uppercase leading-tight">{{ jugadorSeleccionado.nombres }} {{ jugadorSeleccionado.apellidos }}</h3>
                        </div>
                    </div>

                    <div class="space-y-4">
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-tight">1. ¿Cómo estuvo tu entrenamiento hoy?</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="form.animo_entrenamiento = 1" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.animo_entrenamiento === 1 ? 'bg-red-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🔴 Mal</button>
                                <button type="button" @click="form.animo_entrenamiento = 2" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.animo_entrenamiento === 2 ? 'bg-amber-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟡 Regular</button>
                                <button type="button" @click="form.animo_entrenamiento = 3" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.animo_entrenamiento === 3 ? 'bg-emerald-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟢 Excelente</button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-tight">2. ¿Pudiste descansar y dormir bien anoche?</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="form.calidad_sueno = 1" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.calidad_sueno === 1 ? 'bg-red-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🔴 Dormí mal</button>
                                <button type="button" @click="form.calidad_sueno = 2" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.calidad_sueno === 2 ? 'bg-amber-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟡 Más o menos</button>
                                <button type="button" @click="form.calidad_sueno = 3" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.calidad_sueno === 3 ? 'bg-emerald-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟢 Súper bien</button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-black text-gray-900 uppercase tracking-tight">3. ¿Te divertiste en los ejercicios de hoy?</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="form.diversion = 1" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.diversion === 1 ? 'bg-red-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🔴 Aburrido</button>
                                <button type="button" @click="form.diversion = 2" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.diversion === 2 ? 'bg-amber-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟡 Poco</button>
                                <button type="button" @click="form.diversion = 3" class="py-2.5 border-2 border-gray-900 font-black text-xs rounded-xl flex items-center justify-center gap-1 transition" :class="form.diversion === 3 ? 'bg-emerald-400 text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">🟢 Mucho! 🎉</button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border-2 border-gray-900">
                            <span class="text-xs font-black text-gray-900 uppercase">4. ¿Te duele algo en este momento?</span>
                            <button type="button" @click="form.siente_dolor = !form.siente_dolor" 
                                class="px-4 py-2 border-2 border-gray-900 rounded-xl text-xs font-black uppercase transition shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                :class="form.siente_dolor ? 'bg-red-400 text-gray-900' : 'bg-white text-gray-700'">
                                {{ form.siente_dolor ? 'SÍ, ME DUELE 🚨' : 'NO, TODO BIEN 🟢' }}
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="jugadorSeleccionado = null" 
                            class="w-1/3 py-3 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            Atrás
                        </button>
                        <button type="button" @click="enviarEncuesta" :disabled="form.processing"
                            class="w-2/3 py-3 bg-blue-400 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-blue-300 active:translate-y-0.5">
                            {{ form.processing ? 'Guardando...' : '🚀 LISTO, ¡GUARDAR!' }}
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>