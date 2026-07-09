<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ClipboardDocumentCheckIcon, 
    TrophyIcon, 
    CameraIcon,
    CalendarDaysIcon,
    UsersIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    categorias: Array,
    entrenamientosHoy: Array,
    proximosPartidos: Array
});
</script>

<template>
    <Head title="Dashboard Entrenador" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Hola, {{ $page.props.auth.user.name }}</h1>

            <!-- ATAJOS GRANDES - 1 TOQUE -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <Link :href="route('entrenamientos.create')" 
                    class="bg-gradient-to-br from-blue-500 to-blue-700 p-8 rounded-3xl shadow-xl active:scale-95 transition-transform">
                    <ClipboardDocumentCheckIcon class="w-16 h-16 text-white mb-4" />
                    <h3 class="text-2xl font-bold text-white">Registrar Entrenamiento</h3>
                    <p class="text-blue-100 mt-2">Pasar lista + Evaluar con estrellas</p>
                </Link>

                <Link href="#" 
                    class="bg-gradient-to-br from-green-500 to-green-700 p-8 rounded-3xl shadow-xl active:scale-95 transition-transform">
                    <TrophyIcon class="w-16 h-16 text-white mb-4" />
                    <h3 class="text-2xl font-bold text-white">Registrar Partido</h3>
                    <p class="text-green-100 mt-2">Goles, cambios, tarjetas</p>
                </Link>

                <Link href="#" 
                    class="bg-gradient-to-br from-purple-500 to-purple-700 p-8 rounded-3xl shadow-xl active:scale-95 transition-transform">
                    <CameraIcon class="w-16 h-16 text-white mb-4" />
                    <h3 class="text-2xl font-bold text-white">Subir Fotos/Videos</h3>
                    <p class="text-purple-100 mt-2">Galería para padres</p>
                </Link>
            </div>

            <!-- ENTRENAMIENTOS DE HOY -->
            <div class="bg-white rounded-3xl shadow-lg p-6 mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <CalendarDaysIcon class="w-8 h-8 text-blue-600" />
                    <h2 class="text-2xl font-bold">Entrenamientos Hoy</h2>
                </div>
                
                <div v-if="entrenamientosHoy.length === 0" class="text-center py-8 text-gray-500">
                    No tienes entrenamientos programados hoy
                </div>

                <div v-else class="space-y-3">
                    <div v-for="ent in entrenamientosHoy" :key="ent.id" 
                        class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl">
                        <div>
                            <p class="font-bold text-lg">{{ ent.categoria.nombre }}</p>
                            <p class="text-gray-600">{{ ent.hora_inicio || 'Hora por definir' }}</p>
                        </div>
                        <Link :href="route('entrenamientos.asistencia', ent.id)"
                            class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl active:scale-95">
                            Iniciar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- PRÓXIMOS PARTIDOS -->
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <TrophyIcon class="w-8 h-8 text-green-600" />
                    <h2 class="text-2xl font-bold">Próximos Partidos</h2>
                </div>

                <div v-if="proximosPartidos.length === 0" class="text-center py-8 text-gray-500">
                    Sin partidos programados
                </div>

                <div v-else class="space-y-3">
                    <div v-for="partido in proximosPartidos" :key="partido.id" 
                        class="p-4 bg-green-50 rounded-2xl">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-lg">vs {{ partido.rival }}</p>
                                <p class="text-gray-600">{{ partido.categoria.nombre }} - {{ partido.fecha }}</p>
                            </div>
                            <span class="px-4 py-2 bg-green-600 text-white rounded-xl font-bold">
                                {{ partido.condicion }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>