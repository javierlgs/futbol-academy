<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { DocumentArrowDownIcon, CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({ 
    factura: Object, 
    settings: Object,
    bancos: Array 
});

const showAnularModal = ref(false);
const anularForm = useForm({
    motivo: '02',
    detalle: ''
});

const submitAnular = () => {
    anularForm.post(route('facturas.anular', props.factura.id), {
        onSuccess: () => showAnularModal.value = false
    });
};

const estadoColor = {
    'AUTORIZADO': 'bg-green-100 text-green-700',
    'PENDIENTE': 'bg-yellow-100 text-yellow-700',
    'RECHAZADO': 'bg-red-100 text-red-700',
    'DEVUELTA': 'bg-red-100 text-red-700'
};
</script>

<template>
    <Head title="Factura" />
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-4 sm:p-6">
            <!-- ESTADO SRI -->
            <div class="mb-6 p-6 rounded-3xl" :class="estadoColor[factura.estado_sri]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm">Estado SRI</p>
                        <p class="text-3xl font-bold">{{ factura.estado_sri }}</p>
                    </div>
                    <CheckCircleIcon v-if="factura.estado_sri === 'AUTORIZADO'" class="w-16 h-16" />
                    <XCircleIcon v-else class="w-16 h-16" />
                </div>
                <p v-if="factura.observaciones_sri" class="mt-2 text-sm">{{ factura.observaciones_sri }}</p>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8">
                <!-- HEADER -->
                <div class="flex justify-between items-start mb-8 pb-8 border-b-2">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">{{ settings.razon_social }}</h1>
                        <p class="text-gray-600">{{ settings.nombre_comercial }}</p>
                        <p class="text-gray-600">RUC: {{ settings.ruc }}</p>
                        <p class="text-gray-600">{{ settings.direccion_matriz }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">FACTURA</p>
                        <p class="text-3xl font-bold text-blue-600">{{ factura.numero_factura }}</p>
                        <p class="text-sm text-gray-600 mt-2">Clave Acceso:</p>
                        <p class="text-xs font-mono">{{ factura.clave_acceso }}</p>
                        <p v-if="factura.numero_autorizacion" class="text-sm text-gray-600 mt-2">
                            Autorización: {{ factura.numero_autorizacion }}
                        </p>
                    </div>
                </div>

                <!-- CLIENTE -->
                <div class="mb-8">
                    <h2 class="text-lg font-bold mb-2">CLIENTE</h2>
                    <p class="text-xl font-bold">{{ factura.cliente_nombre }}</p>
                    <p>{{ factura.cliente_tipo_identificacion === '05'? 'CI' : 'RUC' }}: {{ factura.cliente_identificacion }}</p>
                    <p>{{ factura.cliente_direccion }}</p>
                    <p>{{ factura.cliente_telefono }} | {{ factura.cliente_email }}</p>
                </div>

                <!-- DETALLE -->
                <table class="w-full mb-8">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left p-3">Descripción</th>
                            <th class="text-right p-3">Cantidad</th>
                            <th class="text-right p-3">P. Unitario</th>
                            <th class="text-right p-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-3">{{ factura.concepto }}</td>
                            <td class="text-right p-3">1.00</td>
                            <td class="text-right p-3">${{ factura.subtotal_15 }}</td>
                            <td class="text-right p-3 font-bold">${{ factura.subtotal_15 }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- TOTALES -->
                <div class="flex justify-end mb-8">
                    <div class="w-80">
                        <div class="flex justify-between py-2">
                            <span>Subtotal:</span>
                            <span class="font-bold">${{ factura.subtotal_15 }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span>IVA 15%:</span>
                            <span class="font-bold">${{ factura.iva_15 }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-t-2 text-2xl font-bold text-blue-600">
                            <span>TOTAL:</span>
                            <span>${{ factura.total }}</span>
                        </div>
                    </div>
                </div>

                <!-- BANCOS -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                    <h3 class="font-bold mb-3">FORMAS DE PAGO</h3>
                    <div v-for="banco in bancos" :key="banco.id" class="mb-2">
                        <span v-if="banco.principal" class="text-blue-600">★</span>
                        {{ banco.nombre }} - {{ banco.tipo }} - {{ banco.numero_cuenta }} - {{ banco.titular }}
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="flex gap-4">
                    <a :href="route('facturas.pdf', factura.id)" target="_blank"
                        class="flex-1 py-4 bg-blue-600 text-white text-center font-bold rounded-2xl flex items-center justify-center gap-2">
                        <DocumentArrowDownIcon class="w-6 h-6" />
                        Descargar RIDE PDF
                    </a>
                </div>

                <!-- BOTÓN ANULAR -->
                <div v-if="factura.estado_sri === 'AUTORIZADO'" class="mt-6">
                    <button @click="showAnularModal = true"
                        class="w-full py-4 bg-red-600 text-white font-bold rounded-2xl">
                        Anular Factura con Nota de Crédito
                    </button>
                </div>

                <!-- MODAL ANULACIÓN -->
                <div v-if="showAnularModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-8">
                        <h2 class="text-2xl font-bold mb-4">Anular Factura</h2>
                        <form @submit.prevent="submitAnular">
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Motivo *</label>
                                <select v-model="anularForm.motivo" class="w-full border-gray-300 rounded-xl p-3">
                                    <option value="01">Devolución de mercancía</option>
                                    <option value="02">Anulación de operación</option>
                                    <option value="03">Descuento</option>
                                    <option value="04">Otros</option>
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-bold mb-1">Detalle *</label>
                                <textarea v-model="anularForm.detalle" rows="3"
                                    class="w-full border-gray-300 rounded-xl p-3"
                                    placeholder="Explique el motivo de anulación"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="showAnularModal = false"
                                    class="flex-1 py-3 bg-gray-200 rounded-xl font-bold">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="anularForm.processing"
                                    class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold">
                                    Anular
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>