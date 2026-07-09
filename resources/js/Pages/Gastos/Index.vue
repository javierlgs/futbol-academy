<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    PlusIcon, TrashIcon, DocumentArrowDownIcon,
    FunnelIcon, ArrowTrendingDownIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    gastos: Object,
    categorias: Array,
    totales: Object,
    filters: Object
});

const filtros = ref({
    categoria_id: props.filters.categoria_id || '',
    desde: props.filters.desde || '',
    hasta: props.filters.hasta || ''
});

const aplicarFiltros = () => {
    router.get(route('gastos.index'), filtros.value, {
        preserveState: true,
        preserveScroll: true
    });
};

const eliminar = (gasto) => {
    if (confirm('¿Eliminar este gasto?')) {
        router.delete(route('gastos.destroy', gasto.id));
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-EC', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Gastos" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">Gastos</h1>
                    <p class="text-gray-600 mt-1">Control de egresos de la academia</p>
                </div>
                <Link
                    :href="route('gastos.create')"
                    class="px-6 py-3 bg-red-600 text-white font-black rounded-xl hover:bg-red-700 shadow-lg flex items-center gap-2"
                >
                    <PlusIcon class="w-5 h-5" />
                    Nuevo Gasto
                </Link>
            </div>

            <!-- CARDS TOTALES -->
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <p class="text-sm font-bold text-gray-600">TOTAL GASTOS</p>
                    <p class="text-3xl font-black text-red-600 mt-2">{{ formatCurrency(totales.total) }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <p class="text-sm font-bold text-gray-600">MES ACTUAL</p>
                    <p class="text-3xl font-black text-orange-600 mt-2">{{ formatCurrency(totales.mes_actual) }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <p class="text-sm font-bold text-gray-600">IVA PAGADO</p>
                    <p class="text-3xl font-black text-blue-600 mt-2">{{ formatCurrency(totales.iva_total) }}</p>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900 mb-6">
                <div class="grid md:grid-cols-4 gap-4">
                    <select v-model="filtros.categoria_id" class="rounded-xl border-gray-300 p-3">
                        <option value="">Todas las categorías</option>
                        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                    </select>
                    <input v-model="filtros.desde" type="date" class="rounded-xl border-gray-300 p-3" placeholder="Desde" />
                    <input v-model="filtros.hasta" type="date" class="rounded-xl border-gray-300 p-3" placeholder="Hasta" />
                    <button @click="aplicarFiltros" class="bg-gray-900 text-white font-bold rounded-xl flex items-center justify-center gap-2">
                        <FunnelIcon class="w-5 h-5" />
                        Filtrar
                    </button>
                </div>
            </div>

            <!-- TABLA -->
            <div class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-900 text-white">
                        <tr>
                            <th class="p-4 text-left">Fecha</th>
                            <th class="p-4 text-left">Categoría</th>
                            <th class="p-4 text-left">Descripción</th>
                            <th class="p-4 text-left">Proveedor</th>
                            <th class="p-4 text-right">Total</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="gasto in gastos.data" :key="gasto.id" class="border-b-2 hover:bg-gray-50">
                            <td class="p-4 font-bold">{{ formatDate(gasto.fecha) }}</td>
                            <td class="p-4">
                                <span
                                    class="px-3 py-1 rounded-lg font-bold text-white text-sm"
                                    :style="{ backgroundColor: gasto.categoria.color }"
                                >
                                    {{ gasto.categoria.nombre }}
                                </span>
                            </td>
                            <td class="p-4">{{ gasto.descripcion }}</td>
                            <td class="p-4 text-sm text-gray-600">
                                {{ gasto.proveedor?.razon_social || 'Sin proveedor' }}
                            </td>
                            <td class="p-4 text-right font-black text-lg text-red-600">
                                {{ formatCurrency(gasto.total) }}
                            </td>
                            <td class="p-4 text-center">
                                <button @click="eliminar(gasto)" class="text-red-600 hover:text-red-800">
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="gastos.data.length === 0">
                            <td colspan="6" class="p-12 text-center text-gray-500">
                                <ArrowTrendingDownIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
                                <p class="text-xl font-bold">No hay gastos registrados</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <div v-if="gastos.links.length > 3" class="mt-6 flex justify-center gap-2">
                <template v-for="link in gastos.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-lg font-bold"
                        :class="link.active? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="px-4 py-2 rounded-lg text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>