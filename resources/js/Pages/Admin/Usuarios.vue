<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    usuarios: Array,
    roles: Array,
    sedes: Array
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'entrenador',
    sede_id: ''
});

const editForm = useForm({
    role: '',
    sede_id: '',
    activo: true
});

const usuarioEnEdicion = ref(null);

const iniciarEdicion = (user) => {
    usuarioEnEdicion.value = user.id;
    editForm.role = user.role_name;
    editForm.sede_id = user.sede_id || '';
    editForm.activo = user.activo === 1 || user.activo === true;
};

const guardarCambios = (userId) => {
    editForm.patch(route('admin.usuarios.update', userId), {
        onSuccess: () => usuarioEnEdicion.value = null
    });
};

const submitNuevoUsuario = () => {
    form.post(route('admin.usuarios.store'), {
        onSuccess: () => form.reset('name', 'email', 'password')
    });
};
</script>

<template>
    <Head title="Personal y Permisos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <span class="bg-indigo-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider">
                    Seguridad y Staff Clínico/Deportivo
                </span>
                <h2 class="font-black text-3xl text-gray-900 uppercase mt-2 tracking-tight">Gestión de Personal</h2>
                <p class="text-gray-600 text-sm font-bold">Crea credenciales y asigna roles/sedes a entrenadores y psicólogos.</p>
            </div>
        </template>

        <div class="py-8 bg-amber-50/20 min-h-screen text-gray-900">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] h-fit">
                    <h3 class="font-black text-xl uppercase tracking-tight border-b-4 border-gray-900 pb-2 mb-4">Registrar Miembro 🔑</h3>
                    
                    <form @submit.prevent="submitNuevoUsuario" class="space-y-4">
                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Nombre Completo</label>
                            <input v-model="form.name" type="text" placeholder="Ej: Prof. Carlos Mantilla" required
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Correo Electrónico</label>
                            <input v-model="form.email" type="email" placeholder="carlos@elitefc.com" required
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                            <div v-if="form.errors.email" class="text-red-600 text-xs font-black mt-1 uppercase">× {{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Contraseña Inicial</label>
                            <input v-model="form.password" type="password" placeholder="Mínimo 6 caracteres" required
                                   class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900" />
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Rol Operativo</label>
                            <select v-model="form.role" class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900">
                                <option v-for="role in roles" :key="role" :value="role">{{ role.toUpperCase() }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Sede de Trabajo Adscrita</label>
                            <select v-model="form.sede_id" class="w-full bg-stone-50 border-2 border-gray-900 rounded-xl px-4 py-2 text-sm font-bold focus:ring-0 focus:border-gray-900">
                                <option value="">Global / Todas las sedes</option>
                                <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.nombre }}</option>
                            </select>
                        </div>

                        <button type="submit" :disabled="form.processing"
                                class="w-full bg-indigo-300 border-4 border-gray-900 font-black text-xs uppercase py-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 transition-all">
                            {{ form.processing ? 'Registrando...' : 'Dar de Alta Personal ⚡' }}
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <h3 class="font-black text-xs uppercase text-gray-400 tracking-widest">Lista de Cuentas del Sistema</h3>
                    
                    <div class="bg-white border-4 border-gray-900 rounded-3xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900 text-white font-black text-xs uppercase tracking-wider border-b-4 border-gray-900">
                                        <th class="p-4">Miembro / Email</th>
                                        <th class="p-4">Rol / Permisos</th>
                                        <th class="p-4">Sede Adscrita</th>
                                        <th class="p-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-900 font-bold text-sm">
                                    <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-stone-50">
                                        <td class="p-4">
                                            <div class="font-black text-gray-900 uppercase">{{ usuario.name }}</div>
                                            <div class="text-xs text-gray-500">{{ usuario.email }}</div>
                                        </td>
                                        
                                        <td class="p-4">
                                            <template v-if="usuarioEnEdicion === usuario.id">
                                                <select v-model="editForm.role" class="bg-white border-2 border-gray-900 text-xs font-black p-1 rounded-md uppercase">
                                                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                                                </select>
                                            </template>
                                            <template v-else>
                                                <span class="bg-amber-300 text-gray-900 border-2 border-gray-900 px-2 py-0.5 rounded-md text-xs uppercase font-black">
                                                    {{ usuario.role_name }}
                                                </span>
                                            </template>
                                        </td>

                                        <td class="p-4">
                                            <template v-if="usuarioEnEdicion === usuario.id">
                                                <select v-model="editForm.sede_id" class="bg-white border-2 border-gray-900 text-xs font-black p-1 rounded-md uppercase">
                                                    <option value="">Global</option>
                                                    <option v-for="s in sedes" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                                                </select>
                                            </template>
                                            <template v-else>
                                                <span class="text-xs font-black uppercase text-gray-700">
                                                    {{ sedes.find(s => s.id === usuario.sede_id)?.nombre || 'Global 🌍' }}
                                                </span>
                                            </template>
                                        </td>

                                        <td class="p-4 text-right">
                                            <div v-if="usuario.id !== 1">
                                                <button v-if="usuarioEnEdicion !== usuario.id" 
                                                        @click="iniciarEdicion(usuario)"
                                                        class="bg-white border-2 border-gray-900 px-3 py-1 rounded-xl text-xs font-black uppercase hover:bg-gray-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                    Editar ⚙️
                                                </button>
                                                <div v-else class="flex gap-1 justify-end">
                                                    <button @click="guardarCambios(usuario.id)"
                                                            class="bg-green-400 border-2 border-gray-900 px-2 py-1 rounded-xl text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                        OK ✓
                                                    </button>
                                                    <button @click="usuarioEnEdicion = null"
                                                            class="bg-gray-200 border-2 border-gray-900 px-2 py-1 rounded-xl text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                        ×
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>