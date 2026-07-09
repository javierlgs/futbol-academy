<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PlusIcon, PencilSquareIcon, TrashIcon, UsersIcon } from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    categorias: Object,
    sedes: Array,
    filtros: Object
});

const sede_id = ref(props.filtros?.sede_id || '');
const sexo = ref(props.filtros?.sexo || '');

watch([sede_id, sexo], debounce(() => {
    router.get(route('categorias.index'), {
        sede_id: sede_id.value,
        sexo: sexo.value
    }, { preserveState: true, replace: true });
}, 300));

const eliminar = (categoria) => {
    if (confirm(`¿Eliminar la categoría ${categoria.nombre}?\n\nTiene ${categoria.jugadores_count} jugadores.\nLos jugadores quedarán sin categoría.`)) {
        router.delete(route('categorias.destroy', categoria.id));
    }
};
</script>

<template>
    <Head title="Categorías" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-5xl font-black text-gray-900">CATEGORÍAS</h1>
                    <p class="text-gray-600 mt-1">Total: {{ categorias.total }} categorías</p>
                </div>
                <Link :href="route('categorias.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold border-4 border-gray-900 flex items-center gap-2 w-fit">
                    <PlusIcon class="w-5 h-5" />
                    Nueva Categoría
                </Link>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900 mb-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <select v-model="sede_id" class="border-2 border-gray-900 rounded-lg px-4 py-2 font-bold">
                        <option value="">Todas las sedes</option>
                        <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                            {{ sede.nombre }}
                        </option>
                    </select>
                    <select v-model="sexo" class="border-2 border-gray-900 rounded-lg px-4 py-2 font-bold">
                        <option value="">Todos los sexos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="Mixto">Mixto</option>
                    </select>
                    <button @click="sede_id = ''; sexo = ''" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-4 py-2 rounded-lg font-bold border-2 border-gray-900">
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-black">CATEGORÍA</th>
                                <th class="px-6 py-4 text-left font-black">AÑO</th>
                                <th class="px-6 py-4 text-left font-black">SEXO</th>
                                <th class="px-6 py-4 text-left font-black">SEDE</th>
                                <th class="px-6 py-4 text-center font-black">JUGADORES</th>
                                <th class="px-6 py-4 text-center font-black">ESTADO</th>
                                <th class="px-6 py-4 text-center font-black">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-200">
                            <tr v-for="categoria in categorias.data" :key="categoria.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-black text-lg text-gray-900">{{ categoria.nombre }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-black text-xl">{{ categoria.año_nacimiento }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg font-bold border-2"
                                        :class="{
                                            'bg-blue-100 text-blue-900 border-blue-900': categoria.sexo === 'M',
                                            'bg-pink-100 text-pink-900 border-pink-900': categoria.sexo === 'F',
                                            'bg-purple-100 text-purple-900 border-purple-900': categoria.sexo === 'Mixto'
                                        }">
                                        {{ categoria.sexo === 'M' ? 'Masculino' : categoria.sexo === 'F' ? 'Femenino' : 'Mixto' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold">{{ categoria.sede?.nombre || 'Sin sede' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <Link :href="route('jugadores.index', { categoria_id: categoria.id })"
                                        class="inline-flex items-center gap-2 font-black text-lg text-blue-600 hover:text-blue-800">
                                        <UsersIcon class="w-5 h-5" />
                                        {{ categoria.jugadores_count }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-lg font-bold border-2"
                                        :class="categoria.activo 
                                            ? 'bg-green-100 text-green-900 border-green-900' 
                                            : 'bg-gray-100 text-gray-600 border-gray-600'">
                                        {{ categoria.activo ? 'ACTIVA' : 'INACTIVA' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-3">
                                        <Link :href="route('categorias.edit', categoria.id)"
                                            class="text-yellow-600 hover:text-yellow-800 font-bold">
                                            <PencilSquareIcon class="w-5 h-5" />
                                        </Link>
                                        <button @click="eliminar(categoria)"
                                            class="text-red-600 hover:text-red-800 font-bold">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categorias.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-xl font-black">No hay categorías registradas</p>
                                    <Link :href="route('categorias.create')" class="text-blue-600 font-bold mt-2 inline-block">
                                        Crear la primera →
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div v-if="categorias.links.length > 3" class="flex flex-wrap gap-2 mt-6 justify-center">
                <template v-for="(link, index) in categorias.links" :key="index">
                    <Link v-if="link.url" :href="link.url" v-html="link.label"
                        class="px-4 py-2 rounded-lg font-black border-2 border-gray-900"
                        :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" />
                    <span v-else v-html="link.label"
                        class="px-4 py-2 rounded-lg font-black border-2 border-gray-900 bg-gray-200 text-gray-400 cursor-not-allowed" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>