<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    BanknotesIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon,
    LockClosedIcon, LockOpenIcon, PlusIcon, PrinterIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    movimientos: Array,
    totales: Object,
    fecha: String,
    cajaAbierta: Boolean,
    hayApertura: Boolean
});

const mostrarModalApertura = ref(false);
const mostrarModalCierre = ref(false);
const mostrarModalMovimiento = ref(false);

const aperturaForm = useForm({
    monto: '',
    observacion: ''
});

const cierreForm = useForm({
    monto_fisico: '',
    observacion: ''
});

const movimientoForm = useForm({
    tipo: 'INGRESO',
    concepto: '',
    monto: '',
    metodo: 'EFECTIVO',
    observacion: ''
});

const abrirCaja = () => {
    aperturaForm.post(route('caja.abrir'), {
        onSuccess: () => {
            mostrarModalApertura.value = false;
            aperturaForm.reset();
        }
    });
};

const cerrarCaja = () => {
    cierreForm.post(route('caja.cerrar'), {
        onSuccess: () => {
            mostrarModalCierre.value = false;
            cierreForm.reset();
        }
    });
};

const registrarMovimiento = () => {
    movimientoForm.post(route('caja.store'), {
        onSuccess: () => {
            mostrarModalMovimiento.value = false;
            movimientoForm.reset();
        }
    });
};

const cambiarFecha = (e) => {
    router.get(route('caja.index'), { fecha: e.target.value }, { preserveState: true });
};

const imprimirCierre = () => {
    window.print();
};

const diferencia = computed(() => {
    return parseFloat(cierreForm.monto_fisico || 0) - props.totales.saldo_actual;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(value || 0);
};

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('es-EC', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Caja Diaria" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">Caja Diaria</h1>
                    <p class="text-gray-600 mt-1">{{ new Date(fecha).toLocaleDateString('es-EC', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                </div>
                <div class="flex gap-3">
                    <input
                        type="date"
                        :value="fecha"
                        @change="cambiarFecha"
                        class="rounded-xl border-gray-300 p-3 font-bold"
                    />
                    <button
                        v-if="totales.cierre !== null"
                        @click="imprimirCierre"
                        class="px-6 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 flex items-center gap-2"
                    >
                        <PrinterIcon class="w-5 h-5" />
                        Imprimir
                    </button>
                </div>
            </div>

            <!-- ESTADO CAJA -->
            <div v-if="!hayApertura" class="bg-yellow-50 border-4 border-yellow-500 rounded-3xl p-8 mb-6 text-center">
                <LockClosedIcon class="w-16 h-16 mx-auto text-yellow-600 mb-4" />
                <h2 class="text-2xl font-black text-gray-900 mb-2">Caja Cerrada</h2>
                <p class="text-gray-700 mb-6">Debes abrir la caja para registrar movimientos</p>
                <button
                    @click="mostrarModalApertura = true"
                    class="px-8 py-4 bg-green-600 text-white font-black rounded-xl hover:bg-green-700 shadow-lg"
                >
                    Abrir Caja
                </button>
            </div>

            <div v-else-if="!cajaAbierta" class="bg-green-50 border-4 border-green-500 rounded-3xl p-8 mb-6 text-center">
                <LockClosedIcon class="w-16 h-16 mx-auto text-green-600 mb-4" />
                <h2 class="text-2xl font-black text-gray-900 mb-2">Caja Cerrada</h2>
                <p class="text-gray-700 mb-2">Saldo final: {{ formatCurrency(totales.cierre) }}</p>
                <p class="text-sm text-gray-600">Ya se realizó el cierre del día</p>
            </div>

            <!-- CARDS TOTALES -->
            <div v-if="hayApertura" class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-600">APERTURA</p>
                            <p class="text-3xl font-black text-blue-600 mt-2">{{ formatCurrency(totales.apertura) }}</p>
                        </div>
                        <LockOpenIcon class="w-12 h-12 text-blue-600" />
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-600">INGRESOS</p>
                            <p class="text-3xl font-black text-green-600 mt-2">{{ formatCurrency(totales.ingresos) }}</p>
                        </div>
                        <ArrowTrendingUpIcon class="w-12 h-12 text-green-600" />
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-600">EGRESOS</p>
                            <p class="text-3xl font-black text-red-600 mt-2">{{ formatCurrency(totales.egresos) }}</p>
                        </div>
                        <ArrowTrendingDownIcon class="w-12 h-12 text-red-600" />
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-600">SALDO ACTUAL</p>
                            <p class="text-3xl font-black text-gray-900 mt-2">{{ formatCurrency(totales.saldo_actual) }}</p>
                        </div>
                        <BanknotesIcon class="w-12 h-12 text-gray-900" />
                    </div>
                </div>
            </div>

            <!-- ACCIONES -->
            <div v-if="cajaAbierta" class="flex gap-4 mb-6">
                <button
                    @click="mostrarModalMovimiento = true"
                    class="px-6 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 flex items-center gap-2"
                >
                    <PlusIcon class="w-5 h-5" />
                    Nuevo Movimiento
                </button>
                <button
                    @click="mostrarModalCierre = true"
                    class="px-6 py-3 bg-red-600 text-white font-black rounded-xl hover:bg-red-700 flex items-center gap-2"
                >
                    <LockClosedIcon class="w-5 h-5" />
                    Cerrar Caja
                </button>
            </div>

            <!-- TABLA MOVIMIENTOS -->
            <div v-if="hayApertura" class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-900 text-white">
                        <tr>
                            <th class="p-4 text-left">Hora</th>
                            <th class="p-4 text-left">Tipo</th>
                            <th class="p-4 text-left">Concepto</th>
                            <th class="p-4 text-left">Método</th>
                            <th class="p-4 text-right">Monto</th>
                            <th class="p-4 text-right">Saldo</th>
                            <th class="p-4 text-left">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="mov in movimientos" :key="mov.id" class="border-b-2 hover:bg-gray-50">
                            <td class="p-4 font-bold">{{ formatTime(mov.created_at) }}</td>
                            <td class="p-4">
                                <span
                                    class="px-3 py-1 rounded-lg font-bold text-white text-sm"
                                    :class="{
                                        'bg-blue-600': mov.tipo === 'APERTURA',
                                        'bg-green-600': mov.tipo === 'INGRESO',
                                        'bg-red-600': mov.tipo === 'EGRESO',
                                        'bg-gray-900': mov.tipo === 'CIERRE'
                                    }"
                                >
                                    {{ mov.tipo }}
                                </span>
                            </td>
                            <td class="p-4">{{ mov.concepto }}</td>
                            <td class="p-4 text-sm">{{ mov.metodo }}</td>
                            <td class="p-4 text-right font-bold" :class="mov.tipo === 'INGRESO' ? 'text-green-600' : 'text-red-600'">
                                {{ mov.tipo === 'INGRESO' ? '+' : '-' }}{{ formatCurrency(mov.monto) }}
                            </td>
                            <td class="p-4 text-right font-black text-lg">{{ formatCurrency(mov.saldo) }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ mov.user.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL APERTURA -->
        <div v-if="mostrarModalApertura" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full">
                <h2 class="text-2xl font-black mb-6">Abrir Caja</h2>
                <form @submit.prevent="abrirCaja" class="space-y-4">
                    <div>
                        <label class="block font-bold mb-2">Monto Inicial *</label>
                        <input v-model="aperturaForm.monto" type="number" step="0.01" class="w-full rounded-xl border-gray-300 p-3" required />
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Observación</label>
                        <textarea v-model="aperturaForm.observacion" rows="2" class="w-full rounded-xl border-gray-300 p-3"></textarea>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" @click="mostrarModalApertura = false" class="flex-1 py-3 bg-gray-200 text-gray-900 font-bold rounded-xl">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="aperturaForm.processing" class="flex-1 py-3 bg-green-600 text-white font-bold rounded-xl">
                            Abrir Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CIERRE -->
        <div v-if="mostrarModalCierre" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full">
                <h2 class="text-2xl font-black mb-6">Cerrar Caja</h2>
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-sm font-bold text-gray-600">Saldo Sistema</p>
                    <p class="text-3xl font-black">{{ formatCurrency(totales.saldo_actual) }}</p>
                </div>
                <form @submit.prevent="cerrarCaja" class="space-y-4">
                    <div>
                        <label class="block font-bold mb-2">Monto Físico en Caja *</label>
                        <input v-model="cierreForm.monto_fisico" type="number" step="0.01" class="w-full rounded-xl border-gray-300 p-3" required />
                    </div>
                    <div v-if="cierreForm.monto_fisico" class="bg-yellow-50 rounded-xl p-4 border-2 border-yellow-500">
                        <p class="font-bold">Diferencia:</p>
                        <p class="text-2xl font-black" :class="diferencia >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ diferencia >= 0 ? '+' : '' }}{{ formatCurrency(diferencia) }}
                        </p>
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Observación</label>
                        <textarea v-model="cierreForm.observacion" rows="2" class="w-full rounded-xl border-gray-300 p-3"></textarea>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" @click="mostrarModalCierre = false" class="flex-1 py-3 bg-gray-200 text-gray-900 font-bold rounded-xl">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="cierreForm.processing" class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl">
                            Cerrar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL MOVIMIENTO -->
        <div v-if="mostrarModalMovimiento" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full">
                <h2 class="text-2xl font-black mb-6">Nuevo Movimiento</h2>
                <form @submit.prevent="registrarMovimiento" class="space-y-4">
                    <div>
                        <label class="block font-bold mb-2">Tipo *</label>
                        <select v-model="movimientoForm.tipo" class="w-full rounded-xl border-gray-300 p-3" required>
                            <option value="INGRESO">Ingreso</option>
                            <option value="EGRESO">Egreso</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Concepto *</label>
                        <input v-model="movimientoForm.concepto" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Monto *</label>
                        <input v-model="movimientoForm.monto" type="number" step="0.01" class="w-full rounded-xl border-gray-300 p-3" required />
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Método *</label>
                        <select v-model="movimientoForm.metodo" class="w-full rounded-xl border-gray-300 p-3" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="CHEQUE">Cheque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold mb-2">Observación</label>
                        <textarea v-model="movimientoForm.observacion" rows="2" class="w-full rounded-xl border-gray-300 p-3"></textarea>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" @click="mostrarModalMovimiento = false" class="flex-1 py-3 bg-gray-200 text-gray-900 font-bold rounded-xl">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="movimientoForm.processing" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    .no-print { display: none !important; }
    body { print-color-adjust: exact; }
}
</style>