<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon, DocumentArrowDownIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';
import { debounce } from 'lodash-es';

const props = defineProps({
    facturas: Object,
    filtros: Object
});

const buscar = ref(props.filtros.buscar || '');
const estado_sri = ref(props.filtros.estado_sri || '');
const mes = ref(props.filtros.mes || '');

watch([buscar, estado_sri, mes], debounce(() => {
    router.get(route('facturas.index'), {
        buscar: buscar.value,
        estado_sri: estado_sri.value,
        mes: mes.value
    }, { preserveState: true, replace: true });
}, 300));

const getEstadoColor = (estado) => {
    const colores = {
        'AUTORIZADO': 'bg-green-100 text-green-800 border-green-600',
        'PENDIENTE': 'bg-yellow-100 text-yellow-800 border-yellow-600',
        'RECHAZADO': 'bg-red-100 text-red-800 border-red-600',
        'ANULADO': 'bg-gray-100 text-gray-800 border-gray-600',
        'ERROR': 'bg-red-100 text-red-800 border-red-600',
        'DEVUELTA': 'bg-orange-100 text-orange-800 border-orange-600'
    };
    return colores[estado] || 'bg-gray-100 text-gray-800 border-gray-600';
};

/**
 * ⚡ FUNCIÓN DE NOTIFICACIÓN DIRECTA POR WHATSAPP
 */
const enviarNotificacionWhatsApp = (factura) => {
    // Intentamos obtener el teléfono de la factura, o en su defecto del jugador asociado
    const telefonoRaw = factura.cliente_telefono || (factura.jugador ? factura.jugador.rep_telefono : '');
    
    if (!telefonoRaw) {
        alert('Este cliente/representante no cuenta con un número de teléfono registrado.');
        return;
    }

    // Sanitización del formato telefónico
    let telefono = telefonoRaw.replace(/\D/g, '');
    
    // Ajuste automático para código internacional de Ecuador (593) si empieza con '0'
    if (telefono.startsWith('0') && telefono.length === 10) {
        telefono = '593' + telefono.substring(1);
    } else if (telefono.length === 9 && !telefono.startsWith('593')) {
        telefono = '593' + telefono;
    }

    const cliente = factura.cliente_nombre || 'Estimado Representante';
    const concepto = factura.concepto || 'Pensión';
    const totalFactura = parseFloat(factura.total).toFixed(2);
    const numero = factura.numero_factura;
    
    let mensaje = `Hola *${cliente}*, le saluda la Administración de la Academia de Fútbol. ⚽ Le informamos que se ha generado con éxito la Factura *#${numero}* por el concepto de *${concepto}* por un valor total de *$${totalFactura}*. `;
    
    if (factura.estado_sri === 'AUTORIZADO') {
        mensaje += `Su pago se encuentra debidamente registrado y AUTORIZADO por el SRI. ¡Muchas gracias por su confianza!`;
    } else {
        mensaje += `Su comprobante se encuentra actualmente en estado (${factura.estado_sri}). Cualquier duda técnica puede responder a este número.`;
    }

    const url = `https://api.whatsapp.com/send?phone=${telefono}&text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Facturas" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-gray-900">Facturas SRI</h1>
                <Link
                    :href="route('facturas.reporte')"
                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl flex items-center gap-2"
                >
                    <DocumentArrowDownIcon class="w-5 h-5" />
                    Reporte Mensual
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-6 mb-6 border-4 border-gray-900">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Buscar</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
                            <input
                                v-model="buscar"
                                type="text"
                                placeholder="N° Factura, Cliente, Cédula..."
                                class="pl-10 w-full rounded-xl border-gray-300 p-3"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Estado SRI</label>
                        <select v-model="estado_sri" class="w-full rounded-xl border-gray-300 p-3">
                            <option value="">Todos</option>
                            <option value="AUTORIZADO">Autorizado</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="RECHAZADO">Rechazado</option>
                            <option value="ANULADO">Anulado</option>
                            <option value="ERROR">Error</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mes</label>
                        <input
                            v-model="mes"
                            type="month"
                            class="w-full rounded-xl border-gray-300 p-3"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900 text-white">
                            <tr>
                                <th class="p-4 text-left font-black">N° Factura</th>
                                <th class="p-4 text-left font-black">Cliente</th>
                                <th class="p-4 text-left font-black">Fecha</th>
                                <th class="p-4 text-left font-black">IVA</th>
                                <th class="p-4 text-left font-black">Total</th>
                                <th class="p-4 text-left font-black">Estado SRI</th>
                                <th class="p-4 text-left font-black">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="factura in facturas.data" :key="factura.id" class="border-b-2 hover:bg-gray-50">
                                <td class="p-4 font-bold">{{ factura.numero_factura }}</td>
                                <td class="p-4">
                                    <div class="font-bold">{{ factura.cliente_nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ factura.cliente_identificacion }}</div>
                                </td>
                                <td class="p-4">{{ new Date(factura.created_at).toLocaleDateString('es-EC') }}</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg font-bold">
                                        {{ factura.iva_porcentaje_usado }}%
                                    </span>
                                </td>
                                <td class="p-4 font-black text-lg">${{ parseFloat(factura.total).toFixed(2) }}</td>
                                <td class="p-4">
                                    <span
                                        class="px-3 py-1 rounded-lg font-bold border-2"
                                        :class="getEstadoColor(factura.estado_sri)"
                                    >
                                        {{ factura.estado_sri }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2 flex-wrap">
                                        <Link
                                            :href="route('facturas.show', factura.id)"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg font-bold text-sm"
                                        >
                                            Ver
                                        </Link>
                                        <a
                                            :href="route('facturas.pdf', factura.id)"
                                            target="_blank"
                                            class="px-3 py-1 bg-gray-600 text-white rounded-lg font-bold text-sm"
                                        >
                                            PDF
                                        </a>

                                        <button
                                            @click="enviarNotificacionWhatsApp(factura)"
                                            class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm flex items-center gap-1 transition-colors"
                                            title="Enviar comprobante por WhatsApp"
                                        >
                                            💬 WhatsApp
                                        </button>

                                        <button
                                            v-if="factura.estado_sri === 'ERROR'"
                                            @click="router.post(route('facturas.reenviar', factura.id))"
                                            class="px-3 py-1 bg-yellow-600 text-white rounded-lg font-bold text-sm flex items-center gap-1"
                                        >
                                            <ArrowPathIcon class="w-4 h-4" />
                                            Reenviar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t-2 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Mostrando {{ facturas.from }} a {{ facturas.to }} de {{ facturas.total }} facturas
                    </div>
                    <div class="flex gap-2">
                        <template v-for="link in facturas.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-4 py-2 rounded-lg font-bold"
                                :class="link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-4 py-2 rounded-lg font-bold bg-gray-200 text-gray-400 cursor-not-allowed"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>