<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    sedes: Array
});

const form = useForm({
    nombre: '',
    direccion: '',
    telefono: ''
});

const submit = () => {
    form.post(route('admin.sedes.store'), {
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Gestión de Sedes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <span class="bg-orange-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider">
                    Módulo de Infraestructura
                </span>
                <h2 class="font-black text-3xl text-gray-900 uppercase mt-2 tracking-tight">Sedes de la Academia</h2>
                <p class="text-gray-600 text-sm font-bold">Administra los complejos deportivos y sucursales operativas.</p>
            </div>
        </template>

        <div class="py-8 bg-amber-50/20 min-h-screen text-gray-900">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] h-fit">
                    <h3 class="font-black text-xl uppercase tracking-tight border-b-4 border-gray-900 pb-2 mb-4">Nueva Sede 🏢</h3>
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Nombre de la Sede</label>
                            <input v-model="form.nombre" type="text" placeholder="Ej: Sede Norte / Campus Chillo" required
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                            <div v-if="form.errors.nombre" class="text-red-600 text-xs font-black mt-1 uppercase">× {{ form.errors.nombre }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Dirección / Ubicación</label>
                            <input v-model="form.direccion" type="text" placeholder="Ej: Av. Amazonas y Rumiñahui"
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Teléfono de Contacto</label>
                            <input v-model="form.telefono" type="text" placeholder="Ej: 0998765432"
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <button type="submit" :disabled="form.processing"
                                class="w-full bg-orange-300 border-4 border-gray-900 font-black text-xs uppercase py-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                            {{ form.processing ? 'Guardando...' : 'Habilitar Sede ✓' }}
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <h3 class="font-black text-xs uppercase text-gray-400 tracking-widest">Sedes Registradas</h3>
                    
                    <div v-if="sedes.length === 0" class="bg-white border-4 border-gray-900 border-dashed rounded-3xl p-12 text-center">
                        <p class="font-black text-gray-400 uppercase">No hay sedes operativas registradas aún.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="sede in sedes" :key="sede.id"
                             class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-black text-lg uppercase tracking-tight leading-tight">{{ sede.nombre }}</h4>
                                    <span class="text-[10px] font-black border-2 border-gray-900 px-2 py-0.5 rounded-md uppercase bg-emerald-300">
                                        Operativa
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-gray-500"><span class="text-gray-900">📍 Dir:</span> {{ sede.direccion || 'Sin dirección registrada' }}</p>
                                <p class="text-xs font-bold text-gray-500 mt-1"><span class="text-gray-900">📞 Telf:</span> {{ sede.telefono || 'Sin teléfono' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>