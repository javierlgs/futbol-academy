<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ tarifas: Array });

const form = useForm({
    porcentaje: '',
    codigo_sri: '',
    descripcion: ''
});

const submit = () => {
    form.post(route('iva-tarifas.store'), {
        onSuccess: () => form.reset()
    });
};

const activar = (tarifa) => {
    router.put(route('iva-tarifas.update', tarifa.id), { 
        por_defecto: true 
    });
};

const eliminar = (tarifa) => {
    if (confirm('¿Eliminar tarifa?')) {
        router.delete(route('iva-tarifas.destroy', tarifa.id));
    }
};
</script>

<template>
    <Head title="Tarifas IVA" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h1 class="text-3xl font-black mb-6">Tarifas de IVA SRI</h1>

            <div class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900 mb-6">
                <h2 class="text-xl font-black mb-4">Nueva Tarifa</h2>
                <form @submit.prevent="submit" class="grid md:grid-cols-4 gap-4">
                    <input v-model="form.porcentaje" type="number" step="0.01" placeholder="Porcentaje" class="rounded-xl border-gray-300 p-3" required />
                    <input v-model="form.codigo_sri" type="text" placeholder="Código SRI" class="rounded-xl border-gray-300 p-3" required />
                    <input v-model="form.descripcion" type="text" placeholder="Descripción" class="rounded-xl border-gray-300 p-3" required />
                    <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white font-bold rounded-xl">
                        Crear
                    </button>
                </form>
                <div v-if="form.errors.codigo_sri" class="text-red-600 text-sm mt-2">{{ form.errors.codigo_sri }}</div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border-4 border-gray-900 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-900 text-white">
                        <tr>
                            <th class="p-4 text-left">Porcentaje</th>
                            <th class="p-4 text-left">Código SRI</th>
                            <th class="p-4 text-left">Descripción</th>
                            <th class="p-4 text-left">Estado</th>
                            <th class="p-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tarifa in tarifas" :key="tarifa.id" class="border-b-2">
                            <td class="p-4 font-black text-xl">{{ tarifa.porcentaje }}%</td>
                            <td class="p-4">{{ tarifa.codigo_sri }}</td>
                            <td class="p-4">{{ tarifa.descripcion }}</td>
                            <td class="p-4">
                                <span v-if="tarifa.por_defecto" class="px-3 py-1 bg-green-600 text-white rounded-lg font-bold">
                                    ACTIVA
                                </span>
                                <span v-else class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg font-bold">
                                    Inactiva
                                </span>
                            </td>
                            <td class="p-4 flex gap-2">
                                <button 
                                    v-if="!tarifa.por_defecto" 
                                    @click="activar(tarifa)"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg font-bold"
                                >
                                    Activar
                                </button>
                                <button 
                                    v-if="!tarifa.por_defecto"
                                    @click="eliminar(tarifa)"
                                    class="px-3 py-1 bg-red-600 text-white rounded-lg font-bold"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>