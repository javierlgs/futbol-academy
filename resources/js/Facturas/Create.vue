<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { CurrencyDollarIcon, CalendarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ 
    jugador: Object, 
    bancos: Array,
    iva: String,
    facturacionActiva: Boolean
});

const form = useForm({
    concepto: '',
    mes_pension: '',
    subtotal: '',
    metodo_pago: 'efectivo'
});

const calcularTotal = () => {
    const subtotal = parseFloat(form.subtotal) || 0;
    const iva = subtotal * (parseFloat(props.iva) / 100);
    return (subtotal + iva).toFixed(2);
};

const submit = () => {
    form.post(route('facturas.store', props.jugador.id));
};
</script>

<template>
    <Head title="Nueva Factura" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">Nueva Factura</h1>

            <div class="bg-white rounded-3xl shadow-lg p-8">
                <!-- DATOS JUGADOR -->
                <div class="mb-6 p-4 bg-blue-50 rounded-2xl">
                    <p class="text-sm text-gray-600">Cliente</p>
                    <p class="text-xl font-bold">{{ jugador.rep_nombres }} {{ jugador.rep_apellidos }}</p>
                    <p class="text-gray-700">CI: {{ jugador.rep_cedula }} | {{ jugador.nombre_completo }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Concepto *</label>
                        <input v-model="form.concepto" type="text" 
                            class="w-full border-gray-300 rounded-xl p-3"
                            placeholder="Pensión mensual, Inscripción, Uniforme...">
                        <div v-if="form.errors.concepto" class="text-red-600 text-sm">{{ form.errors.concepto }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Mes de Pensión</label>
                        <input v-model="form.mes_pension" type="month" 
                            class="w-full border-gray-300 rounded-xl p-3">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Subtotal *</label>
                        <input v-model="form.subtotal" type="number" step="0.01" 
                            class="w-full border-gray-300 rounded-xl p-3 text-2xl font-bold"
                            placeholder="0.00">
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex justify-between text-lg">
                            <span>Subtotal:</span>
                            <span class="font-bold">${{ parseFloat(form.subtotal || 0).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span>IVA {{ iva }}%:</span>
                            <span class="font-bold">${{ (parseFloat(form.subtotal || 0) * parseFloat(iva) / 100).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-blue-600 border-t-2 pt-2 mt-2">
                            <span>TOTAL:</span>
                            <span>${{ calcularTotal() }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Método de Pago *</label>
                        <select v-model="form.metodo_pago" class="w-full border-gray-300 rounded-xl p-3">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="deposito">Depósito</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>

                    <div v-if="facturacionActiva" class="p-4 bg-green-50 border-2 border-green-500 rounded-xl">
                        <p class="text-green-700 font-bold">✓ Facturación Electrónica SRI activa</p>
                        <p class="text-sm text-green-600">Se enviará automáticamente al SRI</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full py-5 bg-gradient-to-r from-green-600 to-green-800 text-white text-xl font-bold rounded-2xl shadow-xl">
                        Generar Factura
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>