<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    categoria: Object,
    sedes: Array
});

const form = useForm({
    año_nacimiento: props.categoria.año_nacimiento,
    sexo: props.categoria.sexo,
    sede_id: props.categoria.sede_id,
    activo: props.categoria.activo,
});

const submit = () => {
    form.put(route('categorias.update', props.categoria.id));
};

const eliminar = () => {
    if (confirm(`¿Eliminar la categoría ${props.categoria.nombre}? Los jugadores quedarán sin categoría.`)) {
        form.delete(route('categorias.destroy', props.categoria.id));
    }
};
</script>

<template>
    <Head :title="`Editar ${categoria.nombre}`" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-5xl font-black text-gray-900">EDITAR CATEGORÍA</h1>
                    <p class="text-gray-600 mt-1 font-bold">{{ categoria.nombre }} - {{ categoria.sede?.nombre }}</p>
                </div>
                <Link :href="route('categorias.index')" class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-2">
                    <ArrowLeftIcon class="w-5 h-5" />
                    Volver
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Sede -->
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

                    <!-- Año de Nacimiento -->
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Año de Nacimiento *</label>
                        <input 
                            v-model="form.año_nacimiento" 
                            type="number" 
                            min="2000" 
                            :max="new Date().getFullYear()"
                            class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold"
                            placeholder="Ej: 2016"
                        >
                        <p class="text-xs text-gray-500 mt-1">Incluye jugadores nacidos del 01/01 al 31/12 de este año</p>
                        <div v-if="form.errors.año_nacimiento" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.año_nacimiento }}
                        </div>
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">Sexo *</label>
                        <select v-model="form.sexo" class="w-full border-2 border-gray-900 rounded-lg px-4 py-3 font-bold">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="Mixto">Mixto</option>
                        </select>
                        <div v-if="form.errors.sexo" class="text-red-600 text-sm mt-1 font-bold">
                            {{ form.errors.sexo }}
                        </div>
                    </div>

                    <!-- Activo -->
                    <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl border-2 border-gray-300">
                        <input v-model="form.activo" type="checkbox" id="activo" class="w-5 h-5 border-2 border-gray-900 rounded">
                        <label for="activo" class="font-bold text-gray-700">Categoría activa</label>
                    </div>

                    <!-- Preview del nombre -->
                    <div class="bg-blue-50 border-2 border-blue-900 rounded-lg p-4">
                        <p class="text-sm text-blue-700 font-bold">Nombre generado:</p>
                        <p class="text-2xl font-black text-blue-900">{{ form.año_nacimiento }} - {{ form.sexo }}</p>
                    </div>

                    <!-- Info jugadores -->
                    <div v-if="categoria.jugadores_count > 0" class="bg-yellow-50 border-2 border-yellow-900 rounded-lg p-4">
                        <p class="text-sm text-yellow-700 font-bold">⚠️ Esta categoría tiene {{ categoria.jugadores_count }} jugadores asignados</p>
                        <p class="text-xs text-yellow-600">Si cambias año/sexo/sede, los jugadores se moverán automáticamente</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between items-center pt-4">
                        <button 
                            type="button"
                            @click="eliminar"
                            class="text-red-600 hover:text-red-800 font-bold flex items-center gap-2"
                            :disabled="form.processing"
                        >
                            <TrashIcon class="w-5 h-5" />
                            Eliminar
                        </button>
                        
                        <div class="flex gap-4">
                            <Link :href="route('categorias.index')" class="px-6 py-3 rounded-xl font-bold text-gray-700 hover:bg-gray-100">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold border-4 border-gray-900 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Actualizando...' : 'ACTUALIZAR' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>