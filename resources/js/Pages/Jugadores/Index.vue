<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { MagnifyingGlassIcon, PlusIcon, EyeIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    jugadores: Object,
    sedes: Array,
    años: Array,
    filtros: Object
});

const search = ref(props.filtros?.search || '');
const año_nacimiento = ref(props.filtros?.año_nacimiento || '');
const sexo = ref(props.filtros?.sexo || '');
const sede_id = ref(props.filtros?.sede_id || '');
const estado = ref(props.filtros?.estado || '');

watch([search, año_nacimiento, sexo, sede_id, estado], debounce(() => {
    router.get(route('jugadores.index'), {
        search: search.value,
        año_nacimiento: año_nacimiento.value,
        sexo: sexo.value,
        sede_id: sede_id.value,
        estado: estado.value
    }, { preserveState: true, replace: true });
}, 300));
</script>

<template>
    <Head title="Jugadores" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-5xl font-black text-gray-900">JUGADORES</h1>
                    <p class="text-gray-600 mt-1">Total: {{ jugadores.total }} activos</p>
                </div>
                <Link :href="route('jugadores.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold border-4 border-gray-900 flex items-center gap-2 w-fit">
                    <PlusIcon class="w-5 h-5" />
                    Nuevo Jugador
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900 mb-6">
                <div class="grid md:grid-cols-5 gap-4">
                    <div class="relative md:col-span-2">
                        <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-3.5 text-gray-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar por nombre o cédula..."
                            class="pl-10 w-full border-2 border-gray-900 rounded-lg px-4 py-2 font-bold"
                        >
                    </div>

                    <select v-model="año_nacimiento" class="border-2 border-gray-900 rounded-lg px-4 py-2 font-bold">
                        <option value="">Todos los años</option>
                        <option v-for="año in años" :key="año" :value="año">
                            {{ año }}
                        </option>
                    </select>

                    <select v-model="sexo" class="border-2 border-gray-900 rounded-lg px-4 py-2 font-bold">
                        <option value="">Todos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>

                    <select v-model="sede_id" class="border-2 border-gray-900 rounded-lg px-4 py-2 font-bold">
                        <option value="">Todas las sedes</option>
                        <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                            {{ sede.nombre }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-black">JUGADOR</th>
                                <th class="px-6 py-4 text-left font-black">EDAD</th>
                                <th class="px-6 py-4 text-left font-black">CATEGORÍA</th>
                                <th class="px-6 py-4 text-left font-black">SEDE</th>
                                <th class="px-6 py-4 text-left font-black">REPRESENTANTE</th>
                                <th class="px-6 py-4 text-left font-black">ESTADO</th>
                                <th class="px-6 py-4 text-center font-black">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-200">
                            <tr v-for="jugador in jugadores.data" :key="jugador.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img v-if="jugador.foto" :src="'/storage/' + jugador.foto" class="w-10 h-10 rounded-full border-2 border-gray-900 object-cover">
                                        <div v-else class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black border-2 border-gray-900">
                                            {{ jugador.nombres[0] }}{{ jugador.apellidos[0] }}
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900">{{ jugador.nombres }} {{ jugador.apellidos }}</div>
                                            <div class="text-sm text-gray-600">{{ jugador.rep_cedula || 'Sin cédula' }}</div>
                                        </div>
                                    </div>
                                </td>
                                    <td class="px-6 py-4">
                                        <span class="font-black text-lg">
                                            {{ jugador.fecha_nacimiento ? Math.floor((new Date() - new Date(jugador.fecha_nacimiento)) / 31557600000) : '0' }} años
                                        </span>
                                        <div class="text-xs text-gray-600">{{ jugador.sexo }}</div>
                                    </td>
<td class="px-6 py-4">
    <span class="px-3 py-1 bg-blue-100 text-blue-900 rounded-lg font-bold border-2 border-blue-900">
        {{ jugador.año_nacimiento_calculado }}
    </span>
</td>
                                <td class="px-6 py-4 font-bold">{{ jugador.sede?.nombre }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold">{{ jugador.rep_nombres }} {{ jugador.rep_apellidos }}</div>
                                    <div class="text-sm text-gray-600">{{ jugador.rep_telefono }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg font-bold border-2"
                                        :class="{
                                            'bg-green-100 text-green-900 border-green-900': jugador.estado === 'activo',
                                            'bg-yellow-100 text-yellow-900 border-yellow-900': jugador.estado === 'lesionado',
                                            'bg-red-100 text-red-900 border-red-900': jugador.estado === 'inactivo',
                                            'bg-purple-100 text-purple-900 border-purple-900': jugador.estado === 'prueba'
                                        }">
                                        {{ jugador.estado.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <Link :href="route('jugadores.show', jugador.id)"
                                            class="text-blue-600 hover:text-blue-800 font-bold">
                                            <EyeIcon class="w-5 h-5 inline" /> Ver
                                        </Link>
                                        <Link :href="route('jugadores.edit', jugador.id)"
                                            class="text-amber-600 hover:text-amber-800 font-bold">
                                            <PencilSquareIcon class="w-5 h-5 inline" /> Editar
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="jugadores.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-xl font-black">No hay jugadores registrados</p>
                                    <Link :href="route('jugadores.create')" class="text-blue-600 font-bold mt-2 inline-block">
                                        Registrar el primero →
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="jugadores.links.length > 3" class="flex flex-wrap gap-2 mt-6 justify-center">
                <template v-for="(link, index) in jugadores.links" :key="index">
                    <Link v-if="link.url" :href="link.url" v-html="link.label"
                        class="px-4 py-2 rounded-lg font-black border-2 border-gray-900"
                        :class="link.active? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" />
                    <span v-else v-html="link.label"
                        class="px-4 py-2 rounded-lg font-black border-2 border-gray-900 bg-gray-200 text-gray-400 cursor-not-allowed" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>