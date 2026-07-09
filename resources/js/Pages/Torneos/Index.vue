<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    torneos: { type: Array, default: () => [] },
    categorias: { type: Array, default: () => [] },
    jugadores: { type: Array, default: () => [] }
});

const mostrarModal = ref(false);
const editando = ref(null);

const form = useForm({
    nombre: '',
    categoria_id: '',
    sexo: '',
    jugadores_ids: [] 
});

// Lógica de filtro por edad: Solo niños aptos para la categoría seleccionada
const jugadoresDisponibles = computed(() => {
    if (!form.categoria_id) return [];
    const cat = props.categorias.find(c => c.id === form.categoria_id);
    if (!cat) return [];
    return props.jugadores.filter(j => {
        const anioNac = new Date(j.fecha_nacimiento).getFullYear();
        // Asumiendo que 'nombre' de categoría tiene el formato "Categoría 2016"
        const anioCat = parseInt(cat.nombre.replace('Categoría ', ''));
        return anioNac >= anioCat;
    });
});

const abrirEdicion = (torneo) => {
    editando.value = torneo;
    form.nombre = torneo.nombre.split(' - ')[0];
    // Buscamos el ID de la categoría por el nombre (ejemplo: '2016' en el string)
    const anio = torneo.categoria.match(/\d{4}/);
    form.categoria_id = props.categorias.find(c => c.nombre.includes(anio))?.id || '';
    form.sexo = torneo.nombre.toLowerCase().includes('masculino') ? 'masculino' : 
                torneo.nombre.toLowerCase().includes('femenino') ? 'femenino' : 'mixto';
    mostrarModal.value = true;
};

const inscribirTorneo = () => {
    if (editando.value) {
        form.put(route('torneos.update', editando.value.id), {
            onSuccess: () => { mostrarModal.value = false; editando.value = null; form.reset(); }
        });
    } else {
        form.post(route('torneos.store'), {
            onSuccess: () => { mostrarModal.value = false; form.reset(); }
        });
    }
};

const eliminarTorneo = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este torneo? Esta acción no se puede deshacer.')) {
        form.delete(route('torneos.destroy', id), {
            onSuccess: () => alert('Torneo eliminado')
        });
    }
};

const clasesEstadoTorneo = (estado) => {
    switch (estado) {
        case 'en_curso': return 'bg-amber-300 text-gray-900 border-2 border-gray-900 animate-pulse';
        case 'finalizado': return 'bg-emerald-300 text-gray-900 border-2 border-gray-900';
        default: return 'bg-blue-300 text-gray-900 border-2 border-gray-900';
    }
};
</script>

<template>
    <Head title="Torneos y Competiciones" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-amber-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Área Competitiva
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 uppercase mt-2 tracking-tight">🏆 Gestión de Torneos</h1>
                    <p class="text-gray-500 text-xs font-bold mt-1">Control de campeonatos oficiales, calendarios de partidos y resultados.</p>
                </div>
                <button @click="mostrarModal = true; editando = null"
                    class="w-full sm:w-auto px-6 py-3.5 bg-purple-400 hover:bg-purple-300 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl transition shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center active:translate-y-0.5">
                    🏆 Inscribir Torneo
                </button>
            </div>

            <div v-if="torneos.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="torneo in torneos" :key="torneo.id"
                    class="bg-white border-4 border-gray-900 rounded-3xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col justify-between hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                    
                    <div>
                        <div class="p-4 bg-gray-50 border-b-4 border-gray-900 flex justify-between items-start">
                            <div>
                                <span class="bg-gray-900 text-amber-300 font-black text-[9px] px-2 py-0.5 rounded uppercase">
                                    {{ torneo.categoria }}
                                </span>
                                <h3 class="font-black text-base text-gray-900 uppercase tracking-tight mt-1">
                                    {{ torneo.nombre }}
                                </h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider" :class="clasesEstadoTorneo(torneo.estado)">
                                {{ torneo.estado }}
                            </span>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3 bg-white">
                            <div class="p-3 rounded-xl border-2 border-gray-900 bg-gray-50">
                                <span class="text-[9px] font-black uppercase text-gray-400 block">Calendario</span>
                                <p class="text-xs font-black text-gray-900 mt-1">
                                    🟢 {{ torneo.partidos_jugados }} Jugados | 🕒 {{ torneo.partidos_pendientes }} Pend.
                                </p>
                            </div>
                            <div class="p-3 rounded-xl border-2 border-gray-900 bg-rose-50">
                                <span class="text-[9px] font-black uppercase text-rose-700 block">Próximo Rival</span>
                                <p class="text-xs font-black text-gray-900 mt-1 truncate">
                                    ⚔️ {{ torneo.proximo_rival }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t-4 border-gray-900 bg-gray-50 flex gap-2">
                        <Link :href="route('torneos.fixture', torneo.id)" class="flex-1 text-center bg-white border-2 border-gray-900 py-2 rounded-xl font-black text-[10px] uppercase hover:bg-gray-100">
                            📅 Ver Calendario
                        </Link>
                        <button @click="abrirEdicion(torneo)" class="bg-blue-400 text-white px-3 py-2 rounded-xl font-black text-[10px] uppercase border-2 border-gray-900">✏️</button>
                        <button @click="eliminarTorneo(torneo.id)" class="bg-rose-500 text-white px-3 py-2 rounded-xl font-black text-[10px] uppercase border-2 border-gray-900">🗑️</button>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white border-4 border-gray-900 rounded-3xl p-12 text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <p class="font-black text-gray-400 uppercase text-xs tracking-wider">No hay torneos registrados actualmente.</p>
            </div>

            <div v-if="mostrarModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center border-b-4 border-gray-100 pb-3 mb-4">
                        <h3 class="font-black text-sm text-gray-900 uppercase">{{ editando ? 'Editar Torneo' : 'Inscribir Torneo' }}</h3>
                        <button @click="mostrarModal = false" class="text-gray-400 hover:text-gray-900 font-black text-sm">✕</button>
                    </div>

                    <form @submit.prevent="inscribirTorneo" class="space-y-4 text-xs font-black uppercase text-gray-700">
                        <div>
                            <label class="block mb-1">Nombre de la Competencia:</label>
                            <input v-model="form.nombre" type="text" placeholder="Ej: Copa Oro 2026" required
                                class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold" />
                        </div>

                        <div>
                            <label class="block mb-1">Categoría Participante:</label>
                            <select v-model="form.categoria_id" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold">
                                <option value="" disabled>Selecciona una categoría</option>
                                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1">Modalidad de Género:</label>
                            <select v-model="form.sexo" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold">
                                <option value="" disabled>Selecciona modalidad</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="mixto">Mixto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 font-black uppercase text-[10px]">Seleccionar Niños para este Torneo:</label>
                            <div class="max-h-40 overflow-y-auto border-2 border-gray-900 rounded-xl p-3 bg-gray-50">
                                <label v-for="jugador in jugadoresDisponibles" :key="jugador.id" class="flex items-center gap-2 py-1">
                                    <input type="checkbox" :value="jugador.id" v-model="form.jugadores_ids" class="rounded border-gray-900">
                                    <span class="text-[10px] uppercase font-bold">{{ jugador.nombres }} {{ jugador.apellidos }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button type="button" @click="mostrarModal = false" class="py-2.5 bg-gray-100 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px]">Cancelar</button>
                            <button type="submit" class="py-2.5 bg-purple-400 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px]">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>