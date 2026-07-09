<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BanknotesIcon, PlusIcon, UserIcon, ArrowTrendingUpIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ pagos: Object, stats: Object });

// Función estética para las etiquetas de estado
const clasesEstado = (estado) => {
    switch (estado) {
        case 'procesado': return 'bg-emerald-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
        case 'pendiente': return 'bg-amber-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
        case 'anulado': return 'bg-red-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
        default: return 'bg-gray-300 text-gray-900 border-2 border-gray-900 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
    }
};
</script>

<template>
    <Head title="Finanzas" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-amber-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Panel Financiero
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 uppercase mt-2 tracking-tight">Gestión de Cobros</h1>
                    <p class="text-gray-500 text-xs font-bold mt-1">Control de pensiones, facturación y estados de cuenta.</p>
                </div>
                <button class="w-full sm:w-auto px-6 py-3.5 bg-gray-900 hover:bg-black border-4 border-gray-900 text-white font-black uppercase text-xs rounded-xl transition shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center active:translate-y-0.5">
                    ➕ Nuevo Pago
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4">
                    <div class="p-3 bg-emerald-100 rounded-2xl border-2 border-gray-900"><BanknotesIcon class="w-8 h-8 text-emerald-700"/></div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase">Ingresos Totales</p>
                        <p class="text-xl font-black">$ {{ stats.total }}</p>
                    </div>
                </div>
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-2xl border-2 border-gray-900"><ArrowTrendingUpIcon class="w-8 h-8 text-blue-700"/></div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase">Pagos Puntuales</p>
                        <p class="text-xl font-black">{{ stats.puntuales }}</p>
                    </div>
                </div>
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4">
                    <div class="p-3 bg-red-100 rounded-2xl border-2 border-gray-900"><ExclamationTriangleIcon class="w-8 h-8 text-red-700"/></div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase">Atrasados</p>
                        <p class="text-xl font-black">{{ stats.atrasados }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border-4 border-gray-900 rounded-3xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b-4 border-gray-900">
                        <tr class="text-[10px] uppercase font-black tracking-widest">
                            <th class="p-4">Jugador</th>
                            <th class="p-4">Concepto</th>
                            <th class="p-4">Monto</th>
                            <th class="p-4">Fecha</th>
                            <th class="p-4">Estado</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="pago in pagos.data" :key="pago.id" class="hover:bg-amber-50 transition">
                            <td class="p-4 font-black text-xs">{{ pago.jugador.nombres }} {{ pago.jugador.apellidos }}</td>
                            <td class="p-4 text-xs font-bold text-gray-600">{{ pago.concepto }}</td>
                            <td class="p-4 font-black text-xs">$ {{ pago.monto }}</td>
                            <td class="p-4 text-xs font-bold text-gray-500">{{ pago.fecha_pago }}</td>
                            <td class="p-4">
                                <span :class="clasesEstado(pago.estado)" class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    {{ pago.estado }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button class="text-[10px] font-black uppercase bg-gray-900 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700">
                                    Detalle
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>