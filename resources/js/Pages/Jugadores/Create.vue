<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

defineProps({ sedes: Array });

const form = useForm({
    nombres: '',
    apellidos: '',
    fecha_nacimiento: '',
    sexo: 'M',
    sede_id: '',
    rep_cedula: '',
    rep_nombres: '',
    rep_apellidos: '',
    rep_telefono: '',
    rep_correo: '',
    rep_direccion: '',
    rep_relacion: 'Padre',
    activo: true,
    foto: null, // Campo para el archivo
});

const guardarJugador = () => {
    form.post(route('jugadores.store'), {
        forceFormData: true, // 🚨 CRUCIAL: Esto procesa la foto como archivo real
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Nuevo Jugador" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-4xl font-black text-gray-900">NUEVO JUGADOR</h1>
                <Link :href="route('jugadores.index')" class="text-gray-600 font-bold hover:text-gray-900">
                    ← Volver
                </Link>
            </div>

            <form @submit.prevent="guardarJugador" class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                <div v-if="form.errors.categoria_id" class="mb-6 bg-red-100 border-4 border-red-900 rounded-xl p-4 flex items-start gap-3">
                    <ExclamationTriangleIcon class="w-6 h-6 text-red-900 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="font-black text-red-900">Error de Categoría</p>
                        <p class="text-red-800">{{ form.errors.categoria_id }}</p>
                        <Link :href="route('categorias.create')" class="text-red-900 font-bold underline mt-1 inline-block">
                            Crear categoría ahora →
                        </Link>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black mb-4 pb-2 border-b-4 border-gray-900">Datos del Jugador</h2>

                    <div class="mb-6 flex flex-col gap-2 bg-white border-4 border-gray-900 rounded-2xl p-4 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <label class="font-black text-xs text-gray-900 uppercase">📸 Foto del Alumno</label>
                        <input type="file" @input="form.foto = $event.target.files[0]" accept="image/*"
                            class="text-xs font-bold text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-2 file:border-gray-900 file:text-xs file:font-black file:bg-amber-300 file:text-gray-900 hover:file:bg-amber-200 cursor-pointer" />
                        <p class="text-[10px] font-bold text-gray-400">Puedes subir un archivo o presionar desde el celular para abrir la cámara en vivo.</p>
                        <InputError :message="form.errors.foto" class="mt-2" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="nombres" value="Nombres *" />
                            <TextInput id="nombres" v-model="form.nombres" type="text" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.nombres" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="apellidos" value="Apellidos *" />
                            <TextInput id="apellidos" v-model="form.apellidos" type="text" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.apellidos" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="fecha_nacimiento" value="Fecha Nacimiento *" />
                            <TextInput id="fecha_nacimiento" v-model="form.fecha_nacimiento" type="date" :max="new Date().toISOString().split('T')[0]" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.fecha_nacimiento" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="sexo" value="Sexo *" />
                            <select id="sexo" v-model="form.sexo" class="mt-1 block w-full border-2 border-gray-900 rounded-lg px-3 py-2 font-bold" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <InputError :message="form.errors.sexo" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="sede_id" value="Sede *" />
                            <select id="sede_id" v-model="form.sede_id" class="mt-1 block w-full border-2 border-gray-900 rounded-lg px-3 py-2 font-bold" required>
                                <option value="">Seleccione una sede</option>
                                <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.nombre }}</option>
                            </select>
                            <InputError :message="form.errors.sede_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_cedula" value="Cédula Jugador" />
                            <TextInput id="rep_cedula" v-model="form.rep_cedula" type="text" maxlength="13" class="mt-1 block w-full border-2 border-gray-900" />
                            <InputError :message="form.errors.rep_cedula" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black mb-4 pb-2 border-b-4 border-gray-900">Datos del Representante</h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="rep_nombres" value="Nombres *" />
                            <TextInput id="rep_nombres" v-model="form.rep_nombres" type="text" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.rep_nombres" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_apellidos" value="Apellidos *" />
                            <TextInput id="rep_apellidos" v-model="form.rep_apellidos" type="text" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.rep_apellidos" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_cedula_rep" value="Cédula Representante *" />
                            <TextInput id="rep_cedula_rep" v-model="form.rep_cedula" type="text" maxlength="13" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.rep_cedula" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_relacion" value="Relación *" />
                            <select id="rep_relacion" v-model="form.rep_relacion" class="mt-1 block w-full border-2 border-gray-900 rounded-lg px-3 py-2 font-bold" required>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Abuelo">Abuelo/a</option>
                                <option value="Tío">Tío/a</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <InputError :message="form.errors.rep_relacion" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_telefono" value="Teléfono *" />
                            <TextInput id="rep_telefono" v-model="form.rep_telefono" type="text" class="mt-1 block w-full border-2 border-gray-900" required />
                            <InputError :message="form.errors.rep_telefono" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="rep_correo" value="Email" />
                            <TextInput id="rep_correo" v-model="form.rep_correo" type="email" class="mt-1 block w-full border-2 border-gray-900" />
                            <InputError :message="form.errors.rep_correo" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="rep_direccion" value="Dirección" />
                            <TextInput id="rep_direccion" v-model="form.rep_direccion" type="text" class="mt-1 block w-full border-2 border-gray-900" />
                            <InputError :message="form.errors.rep_direccion" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <Link :href="route('jugadores.index')" class="text-gray-600 font-bold">Cancelar</Link>
                    <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 border-2 border-gray-900">
                        {{ form.processing? 'Guardando...' : 'Guardar Jugador' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>