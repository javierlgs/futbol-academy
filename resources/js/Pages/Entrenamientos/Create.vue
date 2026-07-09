<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ 
    categorias: {
        type: Array,
        required: true
    },
    sedes: {
        type: Array,
        required: true
    }
});

const form = useForm({
    categoria_id: '',
    fecha: new Date().toISOString().substr(0, 10),
    hora_inicio: '16:00',
    hora_fin: '18:00',
    sede_id: '',
    objetivo: ''
});

const submit = () => {
    form.post(route('entrenamientos.store'));
};
</script>

<template>
    <Head title="Nuevo Entrenamiento" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Planificación de Entrenamiento
            </h2>
        </template>

        <div class="max-w-2xl mx-auto p-4 sm:p-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h1 class="text-2xl font-bold mb-6">Nuevo Entrenamiento</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <InputLabel for="categoria_id" value="Categoría" />
                        <select 
                            v-model="form.categoria_id" 
                            id="categoria_id"
                            class="mt-1 block w-full border-2 border-gray-900 rounded-xl shadow-sm text-lg p-4 focus:ring-0 focus:border-gray-900" 
                            required
                        >
                            <option value="">Selecciona una categoría</option>
                            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                                {{ cat.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.categoria_id" class="text-red-500 text-sm mt-1">
                            {{ form.errors.categoria_id }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase mb-1">Fecha</label>
                        <input v-model="form.fecha" type="date" required
                            class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase mb-1">Sede de la Práctica (Opcional si la Categoría ya tiene una)</label>
                        <select v-model="form.sede_id" class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900">
                            <option value="">-- Usar Sede de la Categoría --</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.nombre }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Hora Inicio</label>
                            <input v-model="form.hora_inicio" type="time" required
                                class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Hora Fin</label>
                            <input v-model="form.hora_fin" type="time"
                                class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase mb-1">Objetivo del día</label>
                        <textarea v-model="form.objetivo" rows="3"
                            class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900"></textarea>
                    </div>

                    <PrimaryButton 
                        :disabled="form.processing"
                        class="w-full justify-center py-4 text-xl rounded-xl"
                    >
                        Continuar a Pasar Lista
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>