<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    asignaciones: { type: Array, default: () => [] },
    categorias: { type: Array, default: () => [] },
    entrenadores: { type: Array, default: () => [] }
});

const mostrarModal = ref(false);
const mostrarModalEditar = ref(false);
const asignacionSeleccionadaId = ref(null);

// Formulario para Crear (Manteniendo tu estructura original)
const form = useForm({
    categoria_id: '',
    user_id: '',
    dias: '',
    hora: '',
    cancha: ''
});

// Formulario para Editar (Estructura idéntica e independiente)
const editForm = useForm({
    categoria_id: '',
    user_id: '',
    dias: '',
    hora: '',
    cancha: ''
});

const guardarAsignacion = () => {
    form.post(route('asignaciones.store'), {
        onSuccess: () => {
            mostrarModal.value = false;
            form.reset();
        }
    });
};

// ==========================================
// 🛠️ ACCIONES DE EDICIÓN Y ELIMINACIÓN ACOPLADAS
// ==========================================
const abrirModalEditar = (asig) => {
    asignacionSeleccionadaId.value = asig.id;
    editForm.categoria_id = asig.categoria_id || '';
    editForm.user_id = asig.user_id || '';
    editForm.dias = asig.dias || '';
    editForm.hora = asig.hora || '';
    editForm.cancha = asig.cancha || '';
    mostrarModalEditar.value = true;
};

const actualizarAsignacion = () => {
    editForm.put(route('asignaciones.update', asignacionSeleccionadaId.value), {
        onSuccess: () => {
            mostrarModalEditar.value = false;
            editForm.reset();
            asignacionSeleccionadaId.value = null;
        }
    });
};

const eliminarAsignacion = (id) => {
    if (confirm('⚠️ ¿Estás seguro de que deseas eliminar permanentemente esta asignación del cronograma?')) {
        useForm({}).delete(route('asignaciones.destroy', id));
    }
};
</script>

<template>
    <Head title="Cronograma de Entrenamientos" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
            
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-purple-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Planificación Semanal
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 uppercase mt-2 tracking-tight">📅 Agenda de Canchas</h1>
                    <p class="text-gray-500 text-xs font-bold mt-1">Asignación de horarios, profesores y espacios de entrenamiento.</p>
                </div>
                <button @click="mostrarModal = true"
                    class="w-full sm:w-auto px-6 py-3.5 bg-lime-400 hover:bg-lime-300 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl transition shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center active:translate-y-0.5">
                    🗓️ Nueva Asignación
                </button>
            </div>

            <div v-if="asignaciones.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="asig in asignaciones" :key="asig.id"
                    class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                    <div>
                        <div class="flex justify-between items-center border-b-2 border-gray-100 pb-2 mb-3">
                            <span class="bg-gray-900 text-amber-300 font-mono text-[10px] px-2 py-0.5 rounded font-black uppercase">
                                {{ asig.categoria }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-black uppercase">🏃‍♂️ {{ asig.entrenador }}</span>
                        </div>
                        
                        <p class="text-xs font-black text-gray-900 uppercase">🕒 Horario:</p>
                        <p class="text-xs font-bold text-gray-500 mt-0.5 mb-3">{{ asig.horario }}</p>

                        <div class="p-3 bg-gray-50 rounded-xl border-2 border-gray-900">
                            <span class="text-[9px] font-black uppercase text-gray-400 block">Ubicación</span>
                            <p class="text-xs font-black text-gray-900 mt-0.5">🏟️ {{ asig.cancha }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4 pt-2 border-t border-gray-100 justify-end">
                        <button @click="abrirModalEditar(asig)" class="px-2.5 py-1 bg-amber-200 border-2 border-gray-900 font-black text-[10px] uppercase rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] hover:bg-amber-300 transition">
                            ✏️ Editar
                        </button>
                        <button @click="eliminarAsignacion(asig.id)" class="px-2.5 py-1 bg-rose-300 border-2 border-gray-900 font-black text-[10px] uppercase rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] hover:bg-rose-400 transition">
                            🗑️ Borrar
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white border-4 border-gray-900 rounded-3xl p-12 text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <p class="font-black text-gray-400 uppercase text-xs tracking-wider">No hay horarios agendados para esta semana.</p>
            </div>

            <div v-if="mostrarModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center border-b-4 border-gray-100 pb-3 mb-4">
                        <h3 class="font-black text-sm text-gray-900 uppercase">🗓️ Crear Nueva Asignación</h3>
                        <button @click="mostrarModal = false" class="text-gray-400 hover:text-gray-900 font-black text-sm">✕</button>
                    </div>

                    <form @submit.prevent="guardarAsignacion" class="space-y-4 text-xs font-black uppercase text-gray-700">
                        <div>
                            <label class="block mb-1">Profesor / Entrenador:</label>
                            <select v-model="form.user_id" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900">
                                <option value="" disabled>Selecciona un profesor</option>
                                <option v-for="prof in entrenadores" :key="prof.id" :value="prof.id">{{ prof.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1">Categoría asignada:</label>
                            <select v-model="form.categoria_id" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900">
                                <option value="" disabled>Selecciona una categoría</option>
                                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block mb-1">Días:</label>
                                <input v-model="form.dias" type="text" placeholder="Ej: Lun, Mié y Vie" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                            </div>
                            <div>
                                <label class="block mb-1">Rango de Hora:</label>
                                <input v-model="form.hora" type="text" placeholder="Ej: 16:00 - 17:30" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1">Cancha / Cancha alterna:</label>
                            <input v-model="form.cancha" type="text" placeholder="Ej: Campo Sintético Principal" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button type="button" @click="mostrarModal = false" class="py-2.5 bg-gray-100 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px]">Cancelar</button>
                            <button type="submit" class="py-2.5 bg-lime-400 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-lime-300">Guardar Agenda</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="mostrarModalEditar" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 max-w-md w-full shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center border-b-4 border-gray-100 pb-3 mb-4">
                        <h3 class="font-black text-sm text-gray-900 uppercase">✏️ Editar Asignación</h3>
                        <button @click="mostrarModalEditar = false" class="text-gray-400 hover:text-gray-900 font-black text-sm">✕</button>
                    </div>

                    <form @submit.prevent="actualizarAsignacion" class="space-y-4 text-xs font-black uppercase text-gray-700">
                        <div>
                            <label class="block mb-1">Profesor / Entrenador:</label>
                            <select v-model="editForm.user_id" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900">
                                <option value="" disabled>Selecciona un profesor</option>
                                <option v-for="prof in entrenadores" :key="prof.id" :value="prof.id">{{ prof.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1">Categoría asignada:</label>
                            <select v-model="editForm.categoria_id" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900">
                                <option value="" disabled>Selecciona una categoría</option>
                                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block mb-1">Días:</label>
                                <input v-model="editForm.dias" type="text" placeholder="Ej: Lun, Mié y Vie" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                            </div>
                            <div>
                                <label class="block mb-1">Rango de Hora:</label>
                                <input v-model="editForm.hora" type="text" placeholder="Ej: 16:00 - 17:30" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1">Cancha / Cancha alterna:</label>
                            <input v-model="editForm.cancha" type="text" placeholder="Ej: Campo Sintético Principal" required class="w-full border-2 border-gray-900 rounded-xl p-2.5 bg-gray-50 font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button type="button" @click="mostrarModalEditar = false" class="py-2.5 bg-gray-100 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px]">Cancelar</button>
                            <button type="submit" :disabled="editForm.processing" class="py-2.5 bg-amber-400 border-2 border-gray-900 text-gray-900 font-black rounded-xl uppercase text-[10px] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-amber-300 disabled:opacity-50">
                                {{ editForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>