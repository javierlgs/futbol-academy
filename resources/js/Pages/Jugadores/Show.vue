<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    ArrowLeftIcon, 
    PencilSquareIcon, 
    TrashIcon,
    UserIcon,
    CalendarIcon,
    MapPinIcon,
    PhoneIcon,
    EnvelopeIcon,
    IdentificationIcon,
    TrophyIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jugador: Object
});

const eliminar = () => {
    if (confirm(`¿Eliminar a ${props.jugador.nombre_completo}? Esta acción no se puede deshacer.`)) {
        router.delete(route('jugadores.destroy', props.jugador.id));
    }
};
</script>

<template>
    <Head :title="jugador.nombre_completo" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
                <div class="flex items-start gap-4">
                    <Link :href="route('jugadores.index')" class="text-gray-600 hover:text-gray-900 mt-2">
                        <ArrowLeftIcon class="w-6 h-6" />
                    </Link>
                    <div>
                        <h1 class="text-5xl font-black text-gray-900">{{ jugador.nombre_completo }}</h1>
                        <div class="flex flex-wrap gap-3 mt-2">
                            <span class="px-4 py-1 bg-blue-600 text-white rounded-lg font-black border-2 border-gray-900">
                                {{ jugador.categoria?.nombre }}
                            </span>
                            <span class="px-4 py-1 rounded-lg font-bold border-2"
                                :class="{
                                    'bg-green-100 text-green-900 border-green-900': jugador.estado === 'activo',
                                    'bg-yellow-100 text-yellow-900 border-yellow-900': jugador.estado === 'lesionado',
                                    'bg-red-100 text-red-900 border-red-900': jugador.estado === 'inactivo',
                                    'bg-purple-100 text-purple-900 border-purple-900': jugador.estado === 'prueba'
                                }">
                                {{ jugador.estado.toUpperCase() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link :href="route('jugadores.edit', jugador.id)" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl font-bold border-4 border-gray-900 flex items-center gap-2">
                        <PencilSquareIcon class="w-5 h-5" />
                        Editar
                    </Link>
                    <button @click="eliminar"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold border-4 border-gray-900 flex items-center gap-2">
                        <TrashIcon class="w-5 h-5" />
                        Eliminar
                    </button>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Columna Izquierda: Foto y Datos Básicos -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Foto -->
                    <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                        <div class="flex flex-col items-center">
                            <img v-if="jugador.foto" 
                                :src="'/storage/' + jugador.foto" 
                                class="w-40 h-40 rounded-full border-4 border-gray-900 object-cover mb-4">
                            <div v-else class="w-40 h-40 rounded-full bg-blue-600 text-white flex items-center justify-center text-5xl font-black border-4 border-gray-900 mb-4">
                                {{ jugador.nombres[0] }}{{ jugador.apellidos[0] }}
                            </div>
                            <h2 class="text-2xl font-black text-center">{{ jugador.nombres }} {{ jugador.apellidos }}</h2>
                            <p class="text-gray-600 font-bold">{{ jugador.edad }} años - {{ jugador.sexo === 'M' ? 'Masculino' : 'Femenino' }}</p>
                        </div>
                    </div>

                    <!-- Datos del Jugador -->
                    <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                        <h3 class="text-xl font-black mb-4 flex items-center gap-2">
                            <UserIcon class="w-6 h-6" />
                            Datos del Jugador
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <CalendarIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Fecha de Nacimiento</p>
                                    <p class="font-bold">{{ new Date(jugador.fecha_nacimiento).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <MapPinIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Sede</p>
                                    <p class="font-bold">{{ jugador.sede?.nombre }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <TrophyIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Categoría</p>
                                    <p class="font-bold">{{ jugador.categoria?.nombre }}</p>
                                </div>
                            </div>
                            <div v-if="jugador.rep_cedula" class="flex items-start gap-3">
                                <IdentificationIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Cédula Jugador</p>
                                    <p class="font-bold">{{ jugador.rep_cedula }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Representante -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Datos del Representante -->
                    <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                        <h3 class="text-xl font-black mb-4 flex items-center gap-2">
                            <UserIcon class="w-6 h-6" />
                            Datos del Representante
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 font-bold">Nombres Completos</p>
                                <p class="font-black text-lg">{{ jugador.rep_nombres }} {{ jugador.rep_apellidos }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold">Relación</p>
                                <p class="font-bold">{{ jugador.rep_relacion }}</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <IdentificationIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Cédula</p>
                                    <p class="font-bold">{{ jugador.rep_cedula }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <PhoneIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Teléfono</p>
                                    <p class="font-bold">{{ jugador.rep_telefono }}</p>
                                </div>
                            </div>
                            <div v-if="jugador.rep_correo" class="flex items-start gap-3">
                                <EnvelopeIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Email</p>
                                    <p class="font-bold">{{ jugador.rep_correo }}</p>
                                </div>
                            </div>
                            <div v-if="jugador.rep_direccion" class="flex items-start gap-3 md:col-span-2">
                                <MapPinIcon class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-xs text-gray-500 font-bold">Dirección</p>
                                    <p class="font-bold">{{ jugador.rep_direccion }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info adicional -->
                    <div class="bg-yellow-50 border-4 border-yellow-900 rounded-3xl p-6">
                        <h3 class="text-lg font-black text-yellow-900 mb-2">Información del Sistema</h3>
                        <div class="text-sm space-y-1 text-yellow-800">
                            <p><span class="font-bold">ID:</span> {{ jugador.id }}</p>
                            <p><span class="font-bold">Registrado:</span> {{ new Date(jugador.created_at).toLocaleDateString('es-ES') }}</p>
                            <p><span class="font-bold">Última actualización:</span> {{ new Date(jugador.updated_at).toLocaleDateString('es-ES') }}</p>
                            <p><span class="font-bold">Estado:</span> {{ jugador.activo ? 'Activo en sistema' : 'Inactivo en sistema' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>