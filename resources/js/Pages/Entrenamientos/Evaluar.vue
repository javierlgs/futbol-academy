<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    entrenamiento: Object,
    jugadores: Array,
    habilities: Array, 
    checklistsExistentes: Object,
    fotosExistentes: { 
        type: Array,
        default: () => []
    }
});

// 1. Formulario principal de Notas y Conductas
const form = useForm({
    evaluaciones: {},
    comentarios: {},
    conductas: {}
});

// 2. Formulario independiente para la subida de fotos
const formFotos = useForm({
    fotos: []
});

// Prellenamos el formulario de calificaciones
props.jugadores.forEach(jugador => {
    form.evaluaciones[jugador.id] = {};
    props.habilities?.forEach(hab => {
        const evalExistente = jugador.evaluaciones?.find(e => e.habilidad_id === hab.id);
        form.evaluaciones[jugador.id][hab.id] = evalExistente ? evalExistente.puntaje : 0;
    });

    const primerEval = jugador.evaluaciones?.[0];
    form.comentarios[jugador.id] = primerEval ? primerEval.comentario : '';

    const check = props.checklistsExistentes[jugador.id];
    form.conductas[jugador.id] = {
        aislamiento_social: check ? Boolean(check.aislamiento_social) : false,
        frustracion_extrema: check ? Boolean(check.frustracion_extrema) : false,
        agresividad: check ? Boolean(check.agresividad) : false,
    };
});

// Guardar Notas y Asistencias
const guardar = () => {
    form.post(route('entrenamientos.guardarEvaluacion', props.entrenamiento.id), {
        preserveScroll: true
    });
};

// Publicar Fotos en caliente
const subirImagenes = () => {
    if (formFotos.fotos.length === 0) {
        alert('⚠️ Por favor, selecciona al menos una fotografía primero.');
        return;
    }

    formFotos.post(route('entrenamientos.subir_fotos', props.entrenamiento.id), {
        forceFormData: true, 
        preserveScroll: true,
        onSuccess: () => {
            alert('📸 ¡Fotos subidas y publicadas con éxito!');
            formFotos.reset();
        }
    });
};

// Borrar una foto incorrecta de inmediato
const borrarFoto = (fotoId) => {
    if (confirm('¿Seguro que deseas eliminar esta fotografía de la galería pública?')) {
        formFotos.delete(route('entrenamientos.eliminarFoto', fotoId), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Evaluar Práctica" />
    <AuthenticatedLayout>
        
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-blue-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Evaluación de Campo
                    </span>
                    <h2 class="font-black text-2xl text-gray-900 uppercase mt-2 tracking-tight">
                        Asistencia - {{ entrenamiento.categoria?.nombre }}
                    </h2>
                    <p class="text-gray-700 text-xs font-black mt-1">
                        📅 {{ entrenamiento.fecha }}
                    </p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <Link :href="route('entrenamientos.index')" 
                        class="py-2.5 px-4 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-300 text-center flex-1 sm:flex-none">
                        ← Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 min-h-screen bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                
                <form @submit.prevent="guardar" class="space-y-6">
                    <div class="bg-white border-4 border-gray-900 rounded-3xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900 text-white text-[11px] uppercase font-black tracking-wider">
                                        <th class="p-4 border-b-4 border-gray-900 w-1/4">Alumno</th>
                                        <th class="p-4 border-b-4 border-gray-900 w-1/3 text-center">📊 Calificaciones (Habilidades)</th>
                                        <th class="p-4 border-b-4 border-gray-900 w-1/4 text-center">🧠 Alertas Conductuales</th>
                                        <th class="p-4 border-b-4 border-gray-900 w-1/4">📝 Nota Rápida</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-900">
                                    <tr v-for="(jugador, idx) in jugadores" :key="jugador.id" class="hover:bg-gray-50 text-xs font-bold text-gray-900">
                                        
                                        <td class="p-3 border-r-2 border-gray-100 vertical-align-top">
                                            <div class="flex items-center gap-3">
                                                <span class="text-gray-400 font-black text-[10px]">#{{ idx + 1 }}</span>
                                                
                                                <div class="w-10 h-10 rounded-xl border-2 border-gray-900 bg-amber-200 overflow-hidden flex-shrink-0 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                    <img v-if="jugador.foto" 
                                                         :src="jugador.foto.startsWith('/storage/') ? jugador.foto : '/storage/' + jugador.foto" 
                                                         class="w-full h-full object-cover"
                                                         @error="$event.target.src = 'https://placehold.co/100x100?text=⚽'" />
                                                    <div v-else class="w-full h-full flex items-center justify-center font-black text-sm">⚽</div>
                                                </div>

                                                <div>
                                                    <h3 class="font-black text-gray-900 uppercase tracking-tight text-xs">
                                                        {{ jugador.apellidos }} {{ jugador.nombres }}
                                                    </h3>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-0.5">Posición: {{ jugador.posicion || 'Por definir' }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="p-2 border-r-2 border-gray-100 bg-gray-50/50">
                                            <div class="space-y-1.5">
                                                <div v-for="hab in habilities" :key="hab.id" 
                                                     class="flex items-center justify-between gap-4 bg-white px-2 py-1 rounded-lg border border-gray-200">
                                                    <span class="text-[10px] font-black text-gray-700 uppercase truncate max-w-[120px]">{{ hab.nombre }}</span>
                                                    <div class="flex items-center gap-0.5 select-none">
                                                        <button type="button" v-for="estrella in 5" :key="estrella"
                                                            @click="form.evaluaciones[jugador.id][hab.id] = estrella"
                                                            class="w-5 h-5 border rounded text-[9px] font-black transition-all"
                                                            :class="form.evaluaciones[jugador.id][hab.id] >= estrella ? 'bg-amber-400 border-gray-900 text-gray-900 shadow-xs' : 'bg-white border-gray-200 text-gray-300 hover:bg-gray-50'">
                                                            ★
                                                        </button>
                                                        <button type="button" @click="form.evaluaciones[jugador.id][hab.id] = 0"
                                                            class="text-[10px] font-bold text-gray-300 hover:text-red-500 ml-1.5 transition">✕</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="p-3 text-center border-r-2 border-gray-100">
                                            <div class="flex flex-col gap-1 max-w-[110px] mx-auto">
                                                <button type="button" @click="form.conductas[jugador.id].aislamiento_social = !form.conductas[jugador.id].aislamiento_social"
                                                    class="w-full py-1 border-2 border-gray-900 text-[9px] font-black uppercase rounded-lg transition text-center"
                                                    :class="form.conductas[jugador.id].aislamiento_social ? 'bg-amber-300 text-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">
                                                    👤 Aísla
                                                </button>
                                                <button type="button" @click="form.conductas[jugador.id].frustracion_extrema = !form.conductas[jugador.id].frustracion_extrema"
                                                    class="w-full py-1 border-2 border-gray-900 text-[9px] font-black uppercase rounded-lg transition text-center"
                                                    :class="form.conductas[jugador.id].frustracion_extrema ? 'bg-amber-300 text-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">
                                                    😤 Frustra
                                                </button>
                                                <button type="button" @click="form.conductas[jugador.id].agresividad = !form.conductas[jugador.id].agresividad"
                                                    class="w-full py-1 border-2 border-gray-900 text-[9px] font-black uppercase rounded-lg transition text-center"
                                                    :class="form.conductas[jugador.id].agresividad ? 'bg-rose-400 text-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-white text-gray-500'">
                                                    🚨 Agresivo
                                                </button>
                                            </div>
                                        </td>

                                        <td class="p-3">
                                            <textarea v-model="form.comentarios[jugador.id]" rows="2" placeholder="Nota rápida..." 
                                                class="w-full text-[11px] font-bold rounded-lg border-2 border-gray-900 text-gray-900 bg-gray-50 p-1.5 focus:ring-0 focus:border-gray-900 resize-none" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing"
                            class="w-full py-3.5 bg-blue-400 border-4 border-gray-900 text-gray-900 font-black uppercase tracking-wider rounded-2xl text-center text-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-blue-300 transition">
                            {{ form.processing ? 'Guardando Evaluaciones...' : '⚡ Guardar Calificaciones de la Práctica' }}
                        </button>
                    </div>
                </form>

                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6 mt-12">
                    <div>
                        <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight">📸 Fotos de la Práctica (Para los Padres)</h3>
                        <p class="text-xs font-bold text-gray-500 mt-1">Sube capturas del entrenamiento para la galería de padres.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 bg-gray-50 p-4 rounded-2xl border-2 border-gray-900">
                        <input type="file" multiple accept="image/*" @change="formFotos.fotos = Array.from($event.target.files)"
                            class="text-xs font-bold text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-2 file:border-gray-900 file:text-xs file:font-black file:bg-amber-300 file:text-gray-900 hover:file:bg-amber-200 cursor-pointer flex-1" />

                        <button type="button" @click="subirImagenes" :disabled="formFotos.processing"
                            class="py-2.5 px-5 bg-emerald-400 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-emerald-300 transition active:translate-y-0.5">
                            {{ formFotos.processing ? 'Subiendo...' : `Publicar Fotos (${formFotos.fotos.length})` }}
                        </button>
                    </div>

                    <div v-if="fotosExistentes.length > 0" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div v-for="foto in fotosExistentes" :key="foto.id" class="aspect-square rounded-2xl border-2 border-gray-900 overflow-hidden bg-gray-100 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative">
                            <img :src="foto.ruta.startsWith('/storage/') ? foto.ruta : '/storage/' + foto.ruta" 
                                 class="w-full h-full object-cover" 
                                 @error="$event.target.src = 'https://placehold.co/400x400?text=Error+Imagen'" />
                            <button @click="borrarFoto(foto.id)" type="button"
                                class="absolute top-1 right-1 bg-red-500 text-white border-2 border-gray-900 font-black rounded-md text-[9px] px-1.5 py-0.5 opacity-90 hover:opacity-100">
                                ✕
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl text-xs font-bold text-gray-400 uppercase">
                        🚩 No has capturado fotografías para este entrenamiento todavía.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>