<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({ pendientes: Array });
</script>

<template>
    <Head title="Dashboard Psicólogo" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">Evaluaciones Pendientes</h1>
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div v-for="j in pendientes" :key="j.id" class="p-4 border-b">
                    <p class="font-bold text-lg">{{ j.nombres }} {{ j.apellidos }}</p>
                    <p class="text-gray-600">{{ j.edad }} años</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>