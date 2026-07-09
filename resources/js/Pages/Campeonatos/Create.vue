<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, CheckIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    sedes: Array,
    añosDisponibles: Array // [2020, 2019, 2018, ...]
});

const form = useForm({
    nombre: '',
    descripcion: '',
    año_base: new Date().getFullYear() - 12, // Default Sub-12
    sede_id: '',
    fecha_inicio: '',
    fecha_fin: '',
    años_habilitados: [],
    activo: true,
});

// Años habilitados = año_base y menores (más jóvenes)
const añosHabilitados = computed(() => {
    if (!form.año_base) return [];
    return props.añosDisponibles.filter(año => año >= form.año_base).sort((a, b) => b - a);
});

// Actualizar años_habilitados cuando cambia año_base
const actualizarAños = () => {
    form.años_habilitados = añosHabilitados.value;
};

// Inicializar
actualizarAños();

const submit = () => {
    form.años_habilitados = añosHabilitados.value;
    form.post(route('campeonatos.store'));
};
</script>

<template>
    <Head title="Nuevo Campeonato" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-5xl font-black text-gray-900">NUEVO CAMPEONATO</h1>
                <Link :href="route('campeonatos.index')" class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-2">
                    <ArrowLeftIcon class="w-5 h-5" />
                    Volver
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900 space-y-6">
                
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Nombre del Campeonato *</label>
                    <input 
                        v-model="form.nombre" 
                        type="text" 
                        class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                        placeholder="Ej: Copa Sub-12 2026"
                    >
                    <div v-if="form.errors.nombre" class="text-red-600 text-sm mt-1 font-bold">
                        {{ form.errors.nombre }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Descripción</label>
                    <textarea 
                        v-model="form.descripcion" 
                        rows="3"
                        class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                        placeholder="Detalles del campeonato..."
                    ></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Sede *</label>
                        <select v-model="form.sede_id" class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold">
                            <option value="">Seleccione una sede</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.sede_id" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.sede_id }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Año Base / Categoría Máxima *</label>
                        <input 
                            v-model.number="form.año_base" 
                            @change="actualizarAños"
                            type="number" 
                            min="2000" 
                            :max="new Date().getFullYear()"
                            class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                        >
                        <p class="text-xs text-gray-500 mt-1">Solo juegan este año y menores</p>
                        <div v-if="form.errors.año_base" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.año_base }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Fecha Inicio *</label>
                        <input 
                            v-model="form.fecha_inicio" 
                            type="date" 
                            class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                        >
                        <div v-if="form.errors.fecha_inicio" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.fecha_inicio }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Fecha Fin *</label>
                        <input 
                            v-model="form.fecha_fin" 
                            type="date" 
                            :min="form.fecha_inicio"
                            class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                        >
                        <div v-if="form.errors.fecha_fin" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.fecha_fin }}
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border-4 border-green-900 rounded-2xl p-6">
                    <h3 class="text-lg font-black text-green-900 mb-3">Años Habilitados para Jugar</h3>
                    <p class="text-sm text-green-700 mb-3">Jugadores nacidos en estos años pueden participar:</p>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="año in añosHabilitados" :key="año"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg font-black border-2 border-gray-900 flex items-center gap-2">
                            <CheckIcon class="w-4 h-4" />
                            {{ año }}
                        </span>
                        <span v-if="añosHabilitados.length === 0" class="text-green-700 font-bold">
                            Selecciona un año base
                        </span>
                    </div>
                    <p class="text-xs text-green-600 mt-3">
                        Regla: Si el campeonato es {{ form.año_base }}, NO pueden jugar nacidos en {{ form.año_base - 1 }} o antes
                    </p>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl border-2 border-gray-300">
                    <input v-model="form.activo" type="checkbox" id="activo" class="w-5 h-5 border-2 border-gray-900 rounded">
                    <label for="activo" class="font-bold text-gray-700">Campeonato activo</label>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <Link :href="route('campeonatos.index')" class="px-6 py-3 rounded-xl font-bold text-gray-700 hover:bg-gray-100">
                        Cancelar
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold border-4 border-gray-900 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Creando...' : 'CREAR CAMPEONATO' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>