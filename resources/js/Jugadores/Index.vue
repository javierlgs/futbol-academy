<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { MagnifyingGlassIcon, FunnelIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    jugadores: Object,
    categorias: Array,
    filtros: Object
});

const search = ref(props.filtros.search || '');
const categoria_id = ref(props.filtros.categoria_id || '');
const estado = ref(props.filtros.estado || '');

watch([search, categoria_id, estado], () => {
    router.get(route('jugadores.index'), {
        search: search.value,
        categoria_id: categoria_id.value,
        estado: estado.value
    }, { preserveState: true, replace: true });
});

const estadoColor = (estado) => {
    return {
        'activo': 'bg-green-100 text-green-700',
        'lesionado': 'bg-red-100 text-red-700',
        'inactivo': 'bg-gray-100 text-gray-700',
        'prueba': 'bg-blue-100 text-blue-700'
    }[estado] || 'bg-gray-100';
}
</script>

<template>
    <Head title="Jugadores" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Jugadores</h1>
                <Link :href="route('jugadores.create')"
                    class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl">
                    Nuevo Jugador
                </Link>
            </div>

            <!-- FILTROS -->
            <div class="bg-white rounded-2xl shadow-lg p-4 mb-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="relative">
                        <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-4 text-gray-400" />
                        <input v-model="search" type="text" placeholder="Buscar por nombre..."
                            class="pl-10 w-full border-gray-300 rounded-xl p-3 text-lg">
                    </div>

                    <select v-model="categoria_id" class="border-gray-300 rounded-xl p-3 text-lg">
                        <option value="">Todas las categorías</option>
                        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                            {{ cat.nombre }}
                        </option>
                    </select>

                    <select v-model="estado" class="border-gray-300 rounded-xl p-3 text-lg">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="lesionado">Lesionado</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="prueba">En Prueba</option>
                    </select>
                </div>
            </div>

            <!-- GRID JUGADORES -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="j in jugadores.data" :key="j.id"
                    :href="route('jugadores.show', j.id)"
                    class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <img :src="j.foto? `/storage/${j.foto}` : '/img/avatar.png'"
                            class="w-16 h-16 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg">{{ j.nombre_completo }}</h3>
                            <p class="text-gray-600">{{ j.categoria.nombre }} - {{ j.edad }} años</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span :class="estadoColor(j.estado)" class="px-3 py-1 rounded-full text-sm font-bold">
                            {{ j.estado }}
                        </span>
                        <span class="text-gray-500 font-semibold">{{ j.posicion || 'N/A' }}</span>
                    </div>
                </Link>
            </div>

            <!-- PAGINACIÓN -->
            <div class="mt-6 flex justify-center gap-2">
                <Link v-for="link in jugadores.links" :key="link.label"
                    :href="link.url"
                    :class="link.active? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                    class="px-4 py-2 rounded-xl font-bold shadow"
                    v-html="link.label">
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>