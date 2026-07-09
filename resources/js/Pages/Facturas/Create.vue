<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    jugadores: Array,
    jugador: Object,
    bancos: Array,
    iva_tarifa: Object,
    facturacionActiva: Boolean
});

const form = useForm({
    jugador_id: '',
    cliente_nombres: '',
    cliente_apellidos: '',
    cliente_cedula: '',
    cliente_telefono: '',
    cliente_email: '',
    cliente_direccion: '',
    concepto: '',
    mes_pension: '',
    metodo_pago: 'efectivo',
    subtotal: 0,
    banco_id: null
});

const seleccionarJugador = (jugadorId) => {
    const jugador = props.jugadores.find(j => j.id == jugadorId);
    if (jugador) {
        form.jugador_id = jugador.id;
        form.cliente_nombres = jugador.rep_nombres;
        form.cliente_apellidos = jugador.rep_apellidos;
        form.cliente_cedula = jugador.rep_cedula;
        form.cliente_telefono = jugador.rep_telefono;
        form.cliente_email = jugador.rep_correo;
        form.cliente_direccion = jugador.rep_direccion;
    }
};

const ivaValor = computed(() => {
    return parseFloat((form.subtotal * (props.iva_tarifa.porcentaje / 100)).toFixed(2));
});

const total = computed(() => {
    return parseFloat((parseFloat(form.subtotal) + ivaValor.value).toFixed(2));
});

const submit = () => {
    // Apunta al store pasando el id del jugador seleccionado correctamente
    const targetId = form.jugador_id || (props.jugador ? props.jugador.id : null);
    
    if (!targetId) {
        alert('Por favor, selecciona un jugador antes de emitir la factura.');
        return;
    }

    form.post(route('facturas.store', targetId), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Nueva Factura" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-4 sm:p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-black text-gray-900">Nueva Factura</h1>
                <p v-if="jugador" class="text-lg text-gray-600 mt-1">
                    {{ jugador.nombres }} {{ jugador.apellidos }} - {{ jugador.categoria?.nombre }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-200 space-y-6">
                    <div>
                        <label class="block font-bold mb-2">Jugador / Representante *</label>
                        <select 
                            @change="seleccionarJugador($event.target.value)" 
                            class="w-full rounded-xl border-gray-300 p-3" 
                            required
                        >
                            <option value="">Seleccionar jugador</option>
                            <option v-for="jug in jugadores" :key="jug.id" :value="jug.id">
                                {{ jug.nombres }} {{ jug.apellidos }} - Rep: {{ jug.rep_nombres }} {{ jug.rep_apellidos }}
                            </option>
                        </select>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border-2 border-gray-900">
                        <div class="md:col-span-2">
                            <p class="font-black text-lg mb-2">Datos de Facturación - Representante</p>
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Cédula/RUC *</label>
                            <input v-model="form.cliente_cedula" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Nombres *</label>
                            <input v-model="form.cliente_nombres" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Apellidos *</label>
                            <input v-model="form.cliente_apellidos" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Teléfono</label>
                            <input v-model="form.cliente_telefono" type="text" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Email</label>
                            <input v-model="form.cliente_email" type="email" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-bold mb-2">Dirección *</label>
                            <input v-model="form.cliente_direccion" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                    <h2 class="text-2xl font-black mb-6">Detalle de Facturación</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <InputLabel for="concepto" value="Concepto *" />
                            <input id="concepto" v-model="form.concepto" type="text" class="mt-1 block w-full rounded-xl border-gray-300 p-3" placeholder="Ej: Pensión Agosto 2024" required />
                            <InputError :message="form.errors.concepto" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="mes_pension" value="Mes Pensión" />
                            <input id="mes_pension" v-model="form.mes_pension" type="month" class="mt-1 block w-full rounded-xl border-gray-300 p-3" />
                            <InputError :message="form.errors.mes_pension" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="metodo_pago" value="Método de Pago *" />
                            <select id="metodo_pago" v-model="form.metodo_pago" class="mt-1 block w-full rounded-xl border-gray-300 p-3" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="deposito">Depósito</option>
                            </select>
                            <InputError :message="form.errors.metodo_pago" class="mt-2" />
                        </div>

                        <div v-if="form.metodo_pago === 'transferencia' || form.metodo_pago === 'deposito'" class="md:col-span-2">
                            <InputLabel for="banco_id" value="Banco *" />
                            <select 
                                id="banco_id" 
                                v-model="form.banco_id" 
                                class="mt-1 block w-full rounded-xl border-gray-300 p-3"
                                :required="form.metodo_pago === 'transferencia' || form.metodo_pago === 'deposito'"
                            >
                                <option :value="null">Seleccionar banco</option>
                                <option v-for="banco in bancos" :key="banco.id" :value="banco.id">
                                    {{ banco.nombre }} - {{ banco.numero_cuenta }}
                                </option>
                            </select>
                            <InputError :message="form.errors.banco_id" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                    <h2 class="text-2xl font-black mb-6">Valores</h2>
                    <div class="space-y-4">
                        <div>
                            <InputLabel value="Subtotal *" />
                            <input v-model="form.subtotal" type="number" step="0.01" class="w-full rounded-xl border-gray-300 p-3 text-lg font-bold" required />
                        </div>
                        <div class="bg-blue-50 rounded-2xl p-6 border-2 border-blue-600">
                            <div class="flex justify-between font-bold text-gray-700">
                                <span>IVA {{ iva_tarifa ? iva_tarifa.porcentaje : 0 }}%</span>
                                <span>${{ ivaValor.toFixed(2) }}</span>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-2xl p-6 border-4 border-green-600">
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-black">TOTAL</span>
                                <span class="text-4xl font-black">${{ total.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <PrimaryButton type="submit" :disabled="form.processing" class="w-full py-6 text-xl justify-center">
                    {{ form.processing ? 'Creando...' : 'Crear Factura' }}
                </PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>