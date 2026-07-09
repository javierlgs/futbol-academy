<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ArrowLeftIcon, PlusIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    categorias: Array,
    proveedores: Array,
    ivaActual: Object
});

const form = useForm({
    categoria_id: '',
    proveedor_id: '',
    numero_factura: '',
    descripcion: '',
    fecha: new Date().toISOString().split('T')[0],
    subtotal: '',
    tiene_iva: true,
    forma_pago: 'EFECTIVO',
    comprobante: null
});

const mostrarModalProveedor = ref(false);
const proveedorForm = useForm({
    ruc: '',
    razon_social: '',
    nombre_comercial: '',
    telefono: '',
    email: '',
    direccion: ''
});

const iva = computed(() => {
    if (!form.tiene_iva ||!form.subtotal) return 0;
    return parseFloat(form.subtotal) * (props.ivaActual.porcentaje / 100);
});

const total = computed(() => {
    return parseFloat(form.subtotal || 0) + iva.value;
});

const submit = () => {
    form.post(route('gastos.store'));
};

const guardarProveedor = () => {
    proveedorForm.post(route('proveedores.store'), {
        onSuccess: (page) => {
            const nuevoProveedor = page.props.flash.data;
            props.proveedores.push(nuevoProveedor);
            form.proveedor_id = nuevoProveedor.id;
            mostrarModalProveedor.value = false;
            proveedorForm.reset();
        }
    });
};

const handleFile = (e) => {
    form.comprobante = e.target.files[0];
};
</script>

<template>
    <Head title="Nuevo Gasto" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6">
                <Link :href="route('gastos.index')" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-bold">
                    <ArrowLeftIcon class="w-5 h-5" />
                    Volver a Gastos
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                <h1 class="text-3xl font-black mb-6">Registrar Gasto</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-bold mb-2">Categoría *</label>
                            <select v-model="form.categoria_id" class="w-full rounded-xl border-gray-300 p-3" required>
                                <option value="">Seleccionar</option>
                                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                            </select>
                            <div v-if="form.errors.categoria_id" class="text-red-600 text-sm mt-1">{{ form.errors.categoria_id }}</div>
                        </div>

                        <div>
                            <label class="block font-bold mb-2">Proveedor</label>
                            <div class="flex gap-2">
                                <select v-model="form.proveedor_id" class="flex-1 rounded-xl border-gray-300 p-3">
                                    <option value="">Sin proveedor</option>
                                    <option v-for="prov in proveedores" :key="prov.id" :value="prov.id">{{ prov.razon_social }}</option>
                                </select>
                                <button
                                    type="button"
                                    @click="mostrarModalProveedor = true"
                                    class="px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
                                >
                                    <PlusIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold mb-2">N° Factura</label>
                            <input v-model="form.numero_factura" type="text" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>

                        <div>
                            <label class="block font-bold mb-2">Fecha *</label>
                            <input v-model="form.fecha" type="date" class="w-full rounded-xl border-gray-300 p-3" required />
                            <div v-if="form.errors.fecha" class="text-red-600 text-sm mt-1">{{ form.errors.fecha }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Descripción *</label>
                        <textarea v-model="form.descripcion" rows="3" class="w-full rounded-xl border-gray-300 p-3" required></textarea>
                        <div v-if="form.errors.descripcion" class="text-red-600 text-sm mt-1">{{ form.errors.descripcion }}</div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block font-bold mb-2">Subtotal *</label>
                            <input v-model="form.subtotal" type="number" step="0.01" class="w-full rounded-xl border-gray-300 p-3" required />
                            <div v-if="form.errors.subtotal" class="text-red-600 text-sm mt-1">{{ form.errors.subtotal }}</div>
                        </div>

                        <div>
                            <label class="block font-bold mb-2">Aplica IVA {{ ivaActual.porcentaje }}%</label>
                            <select v-model="form.tiene_iva" class="w-full rounded-xl border-gray-300 p-3">
                                <option :value="true">Sí</option>
                                <option :value="false">No</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold mb-2">Forma de Pago *</label>
                            <select v-model="form.forma_pago" class="w-full rounded-xl border-gray-300 p-3" required>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="TARJETA">Tarjeta</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Comprobante</label>
                        <input @input="handleFile" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-xl border-gray-300 p-3" />
                        <p class="text-sm text-gray-500 mt-1">PDF, JPG o PNG. Máx 2MB</p>
                    </div>

                    <!-- RESUMEN -->
                    <div class="bg-gray-50 rounded-2xl p-6 border-2 border-gray-900">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold">Subtotal:</span>
                            <span class="font-bold">${{ parseFloat(form.subtotal || 0).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="font-bold">IVA {{ ivaActual.porcentaje }}%:</span>
                            <span class="font-bold">${{ iva.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-black border-t-2 border-gray-900 pt-2">
                            <span>TOTAL:</span>
                            <span class="text-red-600">${{ total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-4 bg-red-600 text-white font-black text-xl rounded-xl hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ form.processing? 'Guardando...' : 'Registrar Gasto' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL PROVEEDOR -->
        <div v-if="mostrarModalProveedor" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-8 max-w-2xl w-full">
                <h2 class="text-2xl font-black mb-6">Nuevo Proveedor</h2>
                <form @submit.prevent="guardarProveedor" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold mb-2">RUC *</label>
                            <input v-model="proveedorForm.ruc" type="text" maxlength="13" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Razón Social *</label>
                            <input v-model="proveedorForm.razon_social" type="text" class="w-full rounded-xl border-gray-300 p-3" required />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Nombre Comercial</label>
                            <input v-model="proveedorForm.nombre_comercial" type="text" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>
                        <div>
                            <label class="block font-bold mb-2">Teléfono</label>
                            <input v-model="proveedorForm.telefono" type="text" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-bold mb-2">Email</label>
                            <input v-model="proveedorForm.email" type="email" class="w-full rounded-xl border-gray-300 p-3" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-bold mb-2">Dirección</label>
                            <textarea v-model="proveedorForm.direccion" rows="2" class="w-full rounded-xl border-gray-300 p-3"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button
                            type="button"
                            @click="mostrarModalProveedor = false"
                            class="flex-1 py-3 bg-gray-200 text-gray-900 font-bold rounded-xl"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="proveedorForm.processing"
                            class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>