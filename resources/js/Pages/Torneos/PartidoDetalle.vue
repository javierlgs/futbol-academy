<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import TableroTactico from '@/Components/TableroTactico.vue';

const props = defineProps({ 
    partido: Object, 
    jugadores: Array,
    alineacionesExistentes: Array,
    tactica: Object,
    historial: { type: Array, default: () => [] } 
});

const tableroRef = ref(null);
const tabActiva = ref('acta');

const convocados = computed(() => {
    const ids = props.alineacionesExistentes.filter(a => a.tipo === 'convocado').map(a => a.jugador_id);
    return props.jugadores.filter(j => ids.includes(j.id));
});

const form = useForm({
    analisis_general: props.partido.analisis_general ? JSON.parse(props.partido.analisis_general) : [],
    alineaciones: convocados.value.map(j => {
        const registro = props.alineacionesExistentes.find(a => a.jugador_id === j.id);
        return {
            jugador_id: j.id,
            asistio: registro ? (registro.tipo === 'asistente') : true,
            calificacion: registro ? registro.calificacion : 0,
            animo: registro ? registro.animo : ''
        };
    })
});

const estadosAnimo = [
    { icon: '🎯', label: 'Concentrado', value: 'concentrado' },
    { icon: '🔥', label: 'Animado', value: 'animado' },
    { icon: '😊', label: 'Feliz', value: 'feliz' },
    { icon: '😴', label: 'Distraído', value: 'distraido' }
];

const puntos = ['PASES', 'DEFINICIÓN', 'ENCARE', 'PORTERÍA', 'DEFENSA', 'ATAQUE'];

const guardarActa = () => {
    form.post(route('partidos.guardarActa', props.partido.id), {
        onSuccess: () => alert('¡Acta guardada!')
    });
};

// 3. Función para cargar la táctica
const cargarTactica = (dibujo_data) => {
    const posiciones = JSON.parse(dibujo_data);
    tabActiva.value = 'tablero';
    // Esperamos al siguiente ciclo para asegurar que el tablero esté visible y disponible
    setTimeout(() => {
        if (tableroRef.value) {
            tableroRef.value.cargarPosiciones(posiciones);
        }
    }, 50);
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-4 space-y-6">
        <div class="flex gap-2 border-b pb-4">
            <button @click="tabActiva = 'acta'" :class="tabActiva === 'acta' ? 'bg-gray-900 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded-xl font-black text-xs uppercase transition">📝 Acta</button>
            <button @click="tabActiva = 'tablero'" :class="tabActiva === 'tablero' ? 'bg-gray-900 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded-xl font-black text-xs uppercase transition">⚽ Tablero Táctico</button>
        </div>

        <div v-if="tabActiva === 'acta'" class="space-y-6">
            <div class="bg-amber-300 border-4 border-gray-900 rounded-3xl p-5">
                <h2 class="font-black text-sm uppercase mb-2">💡 ¿Qué debemos entrenar?</h2>
                <div class="flex flex-wrap gap-2">
                    <button type="button" v-for="punto in puntos" :key="punto" 
                        @click="form.analisis_general.includes(punto) ? form.analisis_general.splice(form.analisis_general.indexOf(punto), 1) : form.analisis_general.push(punto)"
                        :class="form.analisis_general.includes(punto) ? 'bg-gray-900 text-white' : 'bg-white'"
                        class="border-2 border-gray-900 px-3 py-1 rounded-full font-black text-[10px] uppercase transition">
                        {{ punto }}
                    </button>
                </div>
            </div>

            <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <div class="grid grid-cols-12 gap-2 text-[9px] font-black uppercase text-gray-400 mb-4 px-2">
                    <div class="col-span-1 text-center">ASIS.</div>
                    <div class="col-span-3">JUGADOR</div>
                    <div class="col-span-4 text-center">CALIFICACIÓN</div>
                    <div class="col-span-4 text-center">ÁNIMO</div>
                </div>
                <div v-for="(ali, index) in form.alineaciones" :key="ali.jugador_id" 
                       class="grid grid-cols-12 gap-2 items-center py-3 border-t border-gray-100">
                    <button type="button" @click="ali.asistio = !ali.asistio" 
                        class="col-span-1 h-8 w-8 border-2 border-gray-900 rounded-lg font-black text-sm flex items-center justify-center transition"
                        :class="ali.asistio ? 'bg-lime-400' : 'bg-gray-200'">
                        {{ ali.asistio ? '✓' : 'X' }}
                    </button>
                    <span class="col-span-3 font-black text-[11px] uppercase truncate">
                        {{ convocados[index]?.nombres || 'Jugador' }}
                    </span>
                    <div class="col-span-4 flex justify-center gap-0.5">
                        <button type="button" v-for="n in 3" :key="n" @click="ali.calificacion = n" class="text-lg">
                            {{ ali.calificacion >= n ? '⭐' : '☆' }}
                        </button>
                    </div>
                    <div class="col-span-4 flex justify-center gap-1">
                        <button type="button" v-for="estado in estadosAnimo" :key="estado.value" 
                            @click="ali.animo = estado.value"
                            :class="ali.animo === estado.value ? 'bg-gray-200 ring-2 ring-gray-900' : ''"
                            class="text-lg p-1 rounded transition-all" :title="estado.label">
                            {{ estado.icon }}
                        </button>
                    </div>
                </div>
            </div>
            
            <button @click="guardarActa" class="w-full bg-gray-900 text-white p-4 font-black uppercase rounded-2xl hover:bg-black transition shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                Guardar Acta Final
            </button>

            <div class="bg-white p-6 rounded-3xl border-4 border-gray-900 mt-6">
                <h3 class="font-black uppercase mb-4 text-sm">📜 Historial de Tácticas Guardadas</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div v-for="t in historial" :key="t.id" class="p-3 border-2 border-gray-900 rounded-xl">
                        <p class="text-xs font-bold">{{ t.nombre_tactica }}</p>
                        <button @click="cargarTactica(t.dibujo_data)" class="text-[10px] bg-blue-400 p-2 rounded-lg mt-2 font-black uppercase">Cargar Análisis</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="tabActiva === 'tablero'" class="bg-white border-4 border-gray-900 rounded-3xl p-5 flex flex-col items-center">
            <TableroTactico ref="tableroRef" :partido-id="partido.id" :convocados="convocados" />
        </div>
    </div>
</template>