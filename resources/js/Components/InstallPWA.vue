<script setup>
import { ref, onMounted } from 'vue';
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const deferredPrompt = ref(null);
const showInstall = ref(false);

onMounted(() => {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt.value = e;
        showInstall.value = true;
    });
});

const installApp = async () => {
    if (!deferredPrompt.value) return;
    deferredPrompt.value.prompt();
    await deferredPrompt.value.userChoice;
    deferredPrompt.value = null;
    showInstall.value = false;
};
</script>

<template>
    <button v-if="showInstall" @click="installApp"
        class="fixed bottom-20 right-4 z-50 px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold rounded-2xl shadow-2xl flex items-center gap-2 animate-bounce">
        <ArrowDownTrayIcon class="w-6 h-6" />
        Instalar App
    </button>
</template>