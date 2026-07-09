<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { DocumentArrowDownIcon, ArrowPathIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';

const props = defineProps({ facturas: Object, filtros: Object });

const buscar = ref(props.filtros.buscar || '');
const estado_sri = ref(props.filtros.estado_sri || '');
const mes = ref(props.filtros.mes || '');

watch([buscar, estado_sri, mes], () => {
    router.get(route('facturas.index'), { 
        buscar: buscar.value, 
        estado_sri: estado_sri.value,
        mes: mes.value 
    }, { preserveState: true, replace: true });
});

const reenviarSRI = (id) => {
    if (confirm('¿Reenviar factura al SRI?')) {
        router.post(route('facturas.reenviar', id));
    }
};

const estadoColor = {
    'AUTORIZADO': 'bg-green-100 text-green-700',
    'PENDIENTE': 'bg-yellow-100 text-yellow-700',
    'RECHAZADO': 'bg-red-100 text-red-700',
    'DEVUELTA': 'bg-red-100 text-red-700',
    'ERROR': 'bg-red-100 text-red-700'
};
</script>

<template>
    <Head title="Facturas" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Facturas SRI</h1>
                <Link :href="route('jugadores.index')" 
                    class="px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl">
                    Nueva Factura
                </Link>
            </div>

            <!-- FILTROS -->
            <div class="bg-white rounded-2xl shadow p-4 mb-6 grid md:grid-cols-3 gap-4">
                <div class="relative">
                    <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-3.5 text-gray-400" />
                    <input v-model="buscar" type="text" placeholder="Buscar Nro, Cliente, CI..."
                        class="w-full pl-10 pr-4 py-3 border-gray-300 rounded-xl">
                </div>
                
                <select v-model="estado_sri" class="border-gray-300 rounded-xl p-3">
                    <option value="">Todos los estados</option>
                    <option value="AUTORIZADO">Autorizado</option>
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="RECHAZADO">Rechazado</option>
                    <option value="DEVUELTA">Devuelta</option>
                </select>

                <input v-model="mes" type="month" class="border-gray-300 rounded-xl p-3">
            </div>

            <!-- TABLA -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left p-4">Número</th>
                            <th class="text-left p-4">Cliente</th>
                            <th class="text-left p-4">Fecha</th>
                            <th class="text-right p-4">Total</th>
                            <th class="text-center p-4">Estado SRI</th>
                            <th class="text-center p-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="factura in facturas.data" :key="factura.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <p class="font-bold">{{ factura.numero_factura }}</p>
                                <p class="text-xs text-gray-500">{{ factura.clave_acceso }}</p>
                            </td>
                            <td class="p-4">
                                <p class="font-bold">{{ factura.cliente_nombre }}</p>
                                <p class="text-sm text-gray-600">{{ factura.cliente_identificacion }}</p>
                            </td>
                            <td class="p-4">{{ new Date(factura.created_at).toLocaleDateString('es-EC') }}</td>
                            <td class="p-4 text-right font-bold text-lg">${{ factura.total }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold" :class="estadoColor[factura.estado_sri]">
                                    {{ factura.estado_sri }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2 justify-center">
                                    <Link :href="route('facturas.show', factura.id)"
                                        class="p-2 bg-blue-600 text-white rounded-lg">
                                        <DocumentArrowDownIcon class="w-5 h-5" />
                                    </Link>
                                    <button v-if="factura.estado_sri !== 'AUTORIZADO'" 
                                        @click="reenviarSRI(factura.id)"
                                        class="p-2 bg-yellow-600 text-white rounded-lg">
                                        <ArrowPathIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <div class="mt-6 flex justify-center gap-2">
                <Link v-for="link in facturas.links" :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    class="px-4 py-2 rounded-xl"
                    :class="link.active? 'bg-blue-600 text-white' : 'bg-white text-gray-700'" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>