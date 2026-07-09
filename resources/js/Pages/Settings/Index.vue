<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { PhotoIcon, SwatchIcon, BuildingOfficeIcon, BanknotesIcon, KeyIcon, PlusIcon, TrashIcon, StarIcon } from '@heroicons/vue/24/outline';
import { ref, computed } from 'vue';

const props = defineProps({ 
    settings: Object, 
    bancos: Array,
    iva_tarifas: Array 
});

const page = usePage();
const isSuperAdmin = computed(() => page.props.auth.user.roles?.some(r => r.name === 'superadmin'));

const tarifaActiva = computed(() => {
    return props.iva_tarifas.find(t => t.id == form.iva_tarifa_default_id);
});

// Formulario de configuración general
const form = useForm({
    razon_social: props.settings.razon_social || '',
    nombre_comercial: props.settings.nombre_comercial || '',
    ruc: props.settings.ruc || '',
    direccion_matriz: props.settings.direccion_matriz || '',
    telefono: props.settings.telefono || '',
    email_facturacion: props.settings.email_facturacion || '',
    obligado_contabilidad: props.settings.obligado_contabilidad || 'NO',
    contribuyente_especial: props.settings.contribuyente_especial || '',
    regimen: props.settings.regimen || 'RIMPE_EMPRENDEDOR',
    primary_color: props.settings.primary_color || '#0EA5E9',
    logo: null,
    facturacion_electronica_activa: props.settings.facturacion_electronica_activa == '1',
    ambiente_sri: props.settings.ambiente_sri || '1',
    codigo_establecimiento: props.settings.codigo_establecimiento || '001',
    codigo_punto_emision: props.settings.codigo_punto_emision || '001',
    secuencial_factura: props.settings.secuencial_factura || '1',
    iva_tarifa_default_id: props.settings.iva_tarifa_default_id || props.iva_tarifas.find(t => t.por_defecto)?.id || null,
    firma_electronica: null,
    firma_electronica_clave: '',
});

// Formulario de bancos
const bancosForm = useForm({
    bancos: props.bancos && props.bancos.length ? props.bancos : [
        { nombre: '', tipo: 'Ahorros', numero_cuenta: '', titular: '', activo: true, principal: true }
    ]
});

const logoPreview = ref(props.settings.academy_logo ? `/storage/${props.settings.academy_logo}` : null);

const handleLogo = (e) => {
    form.logo = e.target.files[0];
    logoPreview.value = URL.createObjectURL(e.target.files[0]);
};

const addBanco = () => {
    bancosForm.bancos.push({
        nombre: '',
        tipo: 'Ahorros',
        numero_cuenta: '',
        titular: '',
        activo: true,
        principal: false
    });
};

const removeBanco = (index) => {
    if (bancosForm.bancos.length === 1) return;
    bancosForm.bancos.splice(index, 1);
    if (!bancosForm.bancos.some(b => b.principal)) {
        bancosForm.bancos[0].principal = true;
    }
};

const setPrincipal = (index) => {
    bancosForm.bancos.forEach((b, i) => b.principal = i === index);
};

const submit = () => {
    form.post(route('settings.update'), {
        forceFormData: true,
        onSuccess: () => {
            form.logo = null;
            form.firma_electronica = null;
        }
    });
};

const submitBancos = () => {
    bancosForm.post(route('settings.bancos'));
};
</script>

<template>
    <Head title="Configuración" />
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">Configuración del Sistema</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- DATOS EMPRESA SRI -->
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <BuildingOfficeIcon class="w-8 h-8 text-blue-600" />
                        Datos de la Empresa - SRI
                    </h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Razón Social *</label>
                            <input v-model="form.razon_social" type="text" class="w-full border-gray-300 rounded-xl p-3">
                            <div v-if="form.errors.razon_social" class="text-red-600 text-sm">{{ form.errors.razon_social }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Comercial *</label>
                            <input v-model="form.nombre_comercial" type="text" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RUC *</label>
                            <input v-model="form.ruc" type="text" maxlength="13" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Dirección Matriz *</label>
                            <input v-model="form.direccion_matriz" type="text" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono *</label>
                            <input v-model="form.telefono" type="text" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Facturación *</label>
                            <input v-model="form.email_facturacion" type="email" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Obligado a llevar contabilidad</label>
                            <select v-model="form.obligado_contabilidad" class="w-full border-gray-300 rounded-xl p-3">
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Régimen</label>
                            <select v-model="form.regimen" class="w-full border-gray-300 rounded-xl p-3">
                                <option value="RIMPE_EMPRENDEDOR">RIMPE Emprendedor</option>
                                <option value="RIMPE_NEGOCIO_POPULAR">RIMPE Negocio Popular</option>
                                <option value="GENERAL">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Contribuyente Especial Nro</label>
                            <input v-model="form.contribuyente_especial" type="text" class="w-full border-gray-300 rounded-xl p-3" placeholder="Opcional">
                        </div>
                    </div>
                </div>

                <!-- BRANDING -->
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <SwatchIcon class="w-8 h-8 text-blue-600" />
                        Branding y PWA
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Color Principal</label>
                            <div class="flex gap-4 items-center">
                                <input v-model="form.primary_color" type="color" class="h-14 w-24 rounded-xl border-2">
                                <input v-model="form.primary_color" type="text" class="flex-1 border-gray-300 rounded-xl p-3 font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Logo de la Academia</label>
                            <div v-if="logoPreview" class="mb-3">
                                <img :src="logoPreview" class="h-24 w-24 object-contain rounded-2xl border-2 p-2">
                            </div>
                            <label class="block">
                                <div class="border-4 border-dashed border-gray-300 rounded-2xl p-6 text-center cursor-pointer hover:border-blue-500">
                                    <PhotoIcon class="w-10 h-10 mx-auto text-gray-400 mb-2" />
                                    <p class="font-bold">Subir logo - Genera iconos PWA automático</p>
                                </div>
                                <input type="file" accept="image/*" @change="handleLogo" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- FACTURACIÓN ELECTRÓNICA -->
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <KeyIcon class="w-8 h-8 text-blue-600" />
                        Facturación Electrónica SRI
                    </h2>
                    <div class="mb-6 p-4 bg-yellow-50 border-2 border-yellow-400 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.facturacion_electronica_activa" type="checkbox" class="w-6 h-6 text-blue-600">
                            <span class="text-lg font-bold">Activar Facturación Electrónica</span>
                        </label>
                    </div>
                    <div v-if="form.facturacion_electronica_activa" class="space-y-4">
                        
                        <!-- TARIFAS DE IVA -->
                        <div class="mt-8 pt-8 border-t-4 border-gray-900">
                            <h3 class="text-2xl font-black text-gray-900 mb-6">Tarifa de IVA Activa</h3>
                            <p class="text-sm text-gray-600 mb-4">Selecciona qué % de IVA usar para facturas nuevas. Gestiona las tarifas en el módulo IVA.</p>
                            
                            <div>
                                <InputLabel for="iva_tarifa_default_id" value="Tarifa IVA Por Defecto *" />
                                <select 
                                    id="iva_tarifa_default_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm" 
                                    v-model="form.iva_tarifa_default_id"
                                    required
                                >
                                    <option v-for="tarifa in props.iva_tarifas" :key="tarifa.id" :value="tarifa.id">
                                        {{ tarifa.descripcion }} - Código SRI: {{ tarifa.codigo_sri }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.iva_tarifa_default_id" />
                            </div>

                            <div class="mt-4 bg-blue-50 rounded-xl p-4">
                                <p class="text-sm font-bold text-blue-900">
                                    IVA Actual: {{ tarifaActiva?.porcentaje }}% - Código: {{ tarifaActiva?.codigo_sri }}
                                </p>
                                <Link
                                    :href="route('iva-tarifas.index')"
                                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700"
                                >
                                    Gestionar todas las tarifas →
                                </Link>
                            </div>
                        </div>
                        
                        <!-- SECCIÓN AMBIENTE SRI DINÁMICA -->
                        <div v-if="isSuperAdmin" class="bg-red-50 border-4 border-red-500 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center gap-2">
                                <KeyIcon class="w-6 h-6" />
                                CONFIGURACIÓN CRÍTICA - SOLO SUPERADMIN
                            </h3>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ambiente SRI *</label>
                                <select v-model="form.ambiente_sri" class="w-full border-gray-300 rounded-xl p-3">
                                    <option value="1">🧪 Pruebas - Sin validez tributaria</option>
                                    <option value="2">✅ Producción - Facturas legales</option>
                                </select>
                            </div>
                        </div>
                        <div v-else class="bg-gray-100 border-2 border-gray-300 rounded-2xl p-6">
                            <p class="text-gray-600 font-bold">🔒 Ambiente SRI</p>
                            <p class="text-2xl font-bold">{{ settings.ambiente_sri === '1'? '🧪 PRUEBAS' : '✅ PRODUCCIÓN' }}</p>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Código Establecimiento *</label>
                                <input v-model="form.codigo_establecimiento" type="text" maxlength="3" class="w-full border-gray-300 rounded-xl p-3" placeholder="001">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Punto de Emisión *</label>
                                <input v-model="form.codigo_punto_emision" type="text" maxlength="3" class="w-full border-gray-300 rounded-xl p-3" placeholder="001">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Secuencial Inicial *</label>
                                <input v-model="form.secuencial_factura" type="number" class="w-full border-gray-300 rounded-xl p-3">
                            </div>
                        </div>
                        <div class="border-t pt-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Firma Electrónica.p12 *</label>
                            <input type="file" @input="form.firma_electronica = $event.target.files[0]" accept=".p12,.pfx" class="w-full border-gray-300 rounded-xl p-3 mb-2">
                            <input v-model="form.firma_electronica_clave" type="password" placeholder="Clave de la firma" class="w-full border-gray-300 rounded-xl p-3">
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-800 text-white text-xl font-bold rounded-2xl shadow-xl">
                    Guardar Configuración
                </button>
            </form>

            <!-- DATOS BANCARIOS MULTIBANCOS -->
            <div class="mt-8 bg-white rounded-3xl shadow-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <BanknotesIcon class="w-8 h-8 text-blue-600" />
                        Cuentas Bancarias
                    </h2>
                    <button type="button" @click="addBanco"
                        class="px-4 py-2 bg-green-600 text-white font-bold rounded-xl flex items-center gap-2">
                        <PlusIcon class="w-5 h-5" />
                        Agregar Banco
                    </button>
                </div>
                <div class="space-y-4">
                    <div v-for="(banco, index) in bancosForm.bancos" :key="index"
                        class="border-2 rounded-2xl p-6"
                        :class="banco.principal? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="setPrincipal(index)"
                                    class="p-2 rounded-lg"
                                    :class="banco.principal? 'bg-blue-600 text-white' : 'bg-gray-200'">
                                    <StarIcon class="w-5 h-5" />
                                </button>
                                <span v-if="banco.principal" class="text-sm font-bold text-blue-600">Principal</span>
                            </div>
                            <button type="button" @click="removeBanco(index)"
                                :disabled="bancosForm.bancos.length === 1"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg disabled:opacity-30">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Banco *</label>
                                <input v-model="banco.nombre" type="text" class="w-full border-gray-300 rounded-xl p-3" placeholder="Banco Pichincha">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tipo *</label>
                                <select v-model="banco.tipo" class="w-full border-gray-300 rounded-xl p-3">
                                    <option value="Ahorros">Ahorros</option>
                                    <option value="Corriente">Corriente</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Número de Cuenta *</label>
                                <input v-model="banco.numero_cuenta" type="text" class="w-full border-gray-300 rounded-xl p-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Titular *</label>
                                <input v-model="banco.titular" type="text" class="w-full border-gray-300 rounded-xl p-3">
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="banco.activo" type="checkbox" class="w-5 h-5 text-blue-600">
                                    <span class="font-bold">Cuenta activa</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="bancosForm.errors.bancos" class="text-red-600 text-sm mt-2">{{ bancosForm.errors.bancos }}</div>
                <button type="button" @click="submitBancos" :disabled="bancosForm.processing"
                    class="mt-6 w-full py-4 bg-blue-600 text-white text-xl font-bold rounded-2xl">
                    Guardar Bancos
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>