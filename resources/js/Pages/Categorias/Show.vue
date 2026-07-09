<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon, UserGroupIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline';

defineProps({
    categoria: Object
});
</script>

<template>
    <Head :title="categoria.nombre" />
    
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="flex items-center gap-4 mb-6">
                <Link :href="route('categorias.index')" class="p-2 hover:bg-gray-100 rounded-lg">
                    <ArrowLeftIcon class="w-6 h-6" />
                </Link>
                <div>
                    <h1 class="text-5xl font-black text-gray-900">{{ categoria.nombre }}</h1>
                    <p class="text-gray-600 mt-1">{{ categoria.sede?.nombre }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                        <h2 class="text-2xl font-black mb-4 pb-2 border-b-4 border-gray-900">Información</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 font-bold">Sede</p>
                                <p class="text-lg font-black">{{ categoria.sede?.nombre }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-bold">Entrenador</p>
                                <p class="text-lg font-black">{{ categoria.entrenador?.name || 'Sin asignar' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-bold">Precio Inscripción</p>
                                <p class="text-lg font-black text-green-600">${{ categoria.precio_inscripcion }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-bold">Precio Mensual</p>
                                <p class="text-lg font-black text-green-600">${{ categoria.precio_mensual }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg p-6 border-4 border-gray-900">
                        <h2 class="text-2xl font-black mb-4 flex items-center gap-2">
                            <UserGroupIcon class="w-6 h-6" />
                            Jugadores Inscritos ({{ categoria.jugadores?.length || 0 }})
                        </h2>
                        <div class="space-y-2">
                            <div v-for="jugador in categoria.jugadores" :key="jugador.id" 
                                class="p-3 bg-gray-50 rounded-xl border-2 border-gray-900 flex justify-between">
                                <span class="font-bold">{{ jugador.nombres }} {{ jugador.apellidos }}</span>
                                <Link :href="route('jugadores.show', jugador.id)" class="text-blue-600 font-bold">Ver</Link>
                            </div>
                            <div v-if="!categoria.jugadores || categoria.jugadores.length === 0" class="text-center py-6 text-gray-400">
                                <p class="font-bold">Sin jugadores inscritos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <Link :href="route('categorias.edit', categoria.id)" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl font-bold border-4 border-gray-900 block text-center">
                        Editar Categoría
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>