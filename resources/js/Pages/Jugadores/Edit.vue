<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeftIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    jugador: Object,
    sedes: Array
});

const form = useForm({
    _method: 'PUT', // Necesario para enviar archivos a través de una petición PUT
    nombres: props.jugador.nombres,
    apellidos: props.jugador.apellidos,
    fecha_nacimiento: props.jugador.fecha_nacimiento?.split('T')[0] || '',
    sexo: props.jugador.sexo,
    sede_id: props.jugador.sede_id,
    rep_cedula: props.jugador.rep_cedula || '',
    rep_nombres: props.jugador.rep_nombres || '',
    rep_apellidos: props.jugador.rep_apellidos || '',
    rep_relacion: props.jugador.rep_relacion || 'Padre',
    rep_telefono: props.jugador.rep_telefono || '',
    rep_correo: props.jugador.rep_correo || '',
    rep_direccion: props.jugador.rep_direccion || '',
    estado: props.jugador.estado || 'activo',
    activo: props.jugador.activo,
    foto: null, // Campo para la nueva foto
});

const submit = () => {
    if (!props.jugador || !props.jugador.id) {
        alert("Error: No se encontró el ID del jugador.");
        return;
    }
    
    // Usamos post con forceFormData para procesar el archivo y el _method PUT
    form.post(route('jugadores.update', props.jugador.id), {
        forceFormData: true,
        onSuccess: () => alert("Actualizado con éxito"),
        onError: (err) => console.log("Errores:", err)
    });
};
</script>

<template>
    <Head :title="`Editar ${jugador.nombres}`" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-4xl font-black text-gray-900">EDITAR JUGADOR</h1>
                <Link :href="route('jugadores.index')" class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-2">
                    <ArrowLeftIcon class="w-5 h-5" />
                    Volver
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-3xl shadow-lg p-8 border-4 border-gray-900">
                
                <div class="mb-8 flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border-2 border-gray-900">
                    <div class="w-20 h-20 rounded-xl border-2 border-gray-900 bg-amber-200 overflow-hidden flex-shrink-0">
                        <img v-if="jugador.foto" :src="'/storage/' + jugador.foto" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center font-black text-2xl">👦</div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase">Fotografía actual</p>
                        <p class="text-xs font-bold text-gray-600">Si subes una foto nueva abajo, la actual se reemplazará.</p>
                    </div>
                </div>

                <div class="mb-8 flex flex-col gap-2 bg-amber-50 border-2 border-gray-900 rounded-2xl p-4">
                    <label class="font-black text-xs text-gray-900 uppercase">📸 Cambiar Foto del Alumno</label>
                    <input type="file" @input="form.foto = $event.target.files[0]" accept="image/*"
                        class="text-xs font-bold text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-2 file:border-gray-900 file:text-xs file:font-black file:bg-white file:text-gray-900 hover:file:bg-gray-100 cursor-pointer" />
                    <p class="text-[9px] font-bold text-gray-400">Presiona desde tu celular si quieres capturar directo con la cámara.</p>
                    <InputError :message="form.errors.foto" class="mt-2" />
                </div>

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

                <div class="mb-8 bg-blue-50 border-2 border-blue-900 rounded-xl p-4">
                    <p class="text-sm text-blue-700 font-bold">Categoría actual:</p>
                    <p class="text-2xl font-black text-blue-900">{{ jugador.categoria?.nombre || 'Sin asignar' }}</p>
                    <p class="text-xs text-blue-600 mt-1">Se recalcula automáticamente si cambias fecha/sexo/sede</p>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black mb-4 pb-2 border-b-4 border-gray-900">Datos del Jugador</h2>

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
                            <InputLabel for="estado" value="Estado *" />
                            <select id="estado" v-model="form.estado" class="mt-1 block w-full border-2 border-gray-900 rounded-lg px-3 py-2 font-bold" required>
                                <option value="activo">Activo</option>
                                <option value="lesionado">Lesionado</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="prueba">Prueba</option>
                            </select>
                            <InputError :message="form.errors.estado" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
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
                        {{ form.processing ? 'Actualizando...' : 'Actualizar Jugador' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>