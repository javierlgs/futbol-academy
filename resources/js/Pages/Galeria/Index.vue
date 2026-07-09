<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PhotoIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({ entrenamiento: Object, fotos: Array });

const form = useForm({
    fotos: [],
    descripcion: ''
});

const previewUrls = ref([]);

const handleFiles = (e) => {
    form.fotos = Array.from(e.target.files);
    previewUrls.value = form.fotos.map(file => URL.createObjectURL(file));
};

const submit = () => {
    form.post(route('galeria.store', props.entrenamiento.id), {
        onSuccess: () => {
            form.reset();
            previewUrls.value = [];
        }
    });
};
</script>

<template>
    <Head title="Galería" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="text-3xl font-bold mb-6">
                Galería: {{ entrenamiento.categoria.nombre }} - {{ entrenamiento.fecha }}
            </h1>

            <!-- SUBIR FOTOS -->
            <div class="bg-white rounded-3xl shadow-lg p-6 mb-6">
                <form @submit.prevent="submit">
                    <label class="block mb-4">
                        <div class="border-4 border-dashed border-gray-300 rounded-2xl p-12 text-center cursor-pointer hover:border-blue-500">
                            <PhotoIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                            <p class="text-xl font-bold text-gray-700">Toca para subir fotos</p>
                            <p class="text-gray-500">Máx 5MB por foto</p>
                        </div>
                        <input type="file" multiple accept="image/*" @change="handleFiles" class="hidden">
                    </label>

                    <div v-if="previewUrls.length" class="grid grid-cols-3 gap-4 mb-4">
                        <img v-for="(url, i) in previewUrls" :key="i" :src="url" 
                            class="w-full h-32 object-cover rounded-xl">
                    </div>

                    <textarea v-model="form.descripcion" placeholder="Descripción opcional..."
                        class="w-full border-gray-300 rounded-xl p-3 mb-4" rows="2"></textarea>

                    <button type="submit" :disabled="form.processing"
                        class="w-full py-4 bg-blue-600 text-white text-xl font-bold rounded-2xl">
                        Subir {{ form.fotos.length }} Foto(s)
                    </button>
                </form>
            </div>

            <!-- GALERÍA EXISTENTE -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="foto in fotos" :key="foto.id" class="relative group">
                    <img :src="`/storage/${foto.ruta}`" 
                        class="w-full h-64 object-cover rounded-2xl shadow-lg">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 rounded-b-2xl">
                        <p class="text-white text-sm">{{ foto.usuario.name }}</p>
                        <p class="text-white text-xs">{{ foto.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>