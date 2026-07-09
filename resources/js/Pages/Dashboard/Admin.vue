<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { UsersIcon, AcademicCapIcon, TrophyIcon, UserGroupIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ stats: Object });
</script>

<template>
    <Head title="Dashboard Administrador" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Panel Administrador</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6 rounded-3xl shadow-xl">
                    <UsersIcon class="w-12 h-12 text-white mb-3" />
                    <p class="text-blue-100 text-sm">Jugadores Activos</p>
                    <p class="text-4xl font-bold text-white">{{ stats.jugadores_activos }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-3xl shadow-xl">
                    <AcademicCapIcon class="w-12 h-12 text-white mb-3" />
                    <p class="text-green-100 text-sm">Categorías</p>
                    <p class="text-4xl font-bold text-white">{{ stats.categorias }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-6 rounded-3xl shadow-xl">
                    <UserGroupIcon class="w-12 h-12 text-white mb-3" />
                    <p class="text-purple-100 text-sm">Entrenadores</p>
                    <p class="text-4xl font-bold text-white">{{ stats.entrenadores }}</p>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-6 rounded-3xl shadow-xl">
                    <TrophyIcon class="w-12 h-12 text-white mb-3" />
                    <p class="text-orange-100 text-sm">Campeonatos Activos</p>
                    <p class="text-4xl font-bold text-white">{{ stats.campeonatos_activos }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>