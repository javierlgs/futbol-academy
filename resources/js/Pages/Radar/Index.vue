<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    alertas: Array
});
</script>

<template>
    <Head title="Radar Preventivo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="bg-rose-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider inline-block">
                        Monitoreo de Bienestar
                    </span>
                    <h2 class="font-black text-2xl text-gray-900 uppercase mt-2 tracking-tight">
                        🧭 Radar de Alertas Preventivas
                    </h2>
                    <p class="text-gray-500 text-xs font-bold mt-1">Detección automática de variaciones repetitivas en cancha o auto-reportes.</p>
                </div>
                <Link :href="route('dashboard')" 
                    class="py-2.5 px-4 bg-gray-200 border-4 border-gray-900 text-gray-900 font-black uppercase text-xs rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-300 text-center">
                    ← Volver al Inicio
                </Link>
            </div>
        </template>

        <div class="py-8 min-h-screen bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 space-y-6">
                
                <div v-if="alertas.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="alerta in alertas" :key="alerta.jugador_id"
                        class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between space-y-4 border-l-[12px] border-l-amber-400">
                        
                        <div>
                            <span class="bg-gray-900 text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider">
                                Revisión Sugerida
                            </span>
                            <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight mt-1">
                                {{ alerta.nombre_completo }}
                            </h3>
                            <p class="text-xs font-bold text-gray-700 mt-2 bg-gray-50 p-3 rounded-xl border-2 border-dashed border-gray-200">
                                🔍 <strong>Indicador detectado:</strong> {{ alerta.motivo_alerta }}
                            </p>
                        </div>

                        <div class="bg-blue-50 border-2 border-gray-900 rounded-xl p-3 text-xs font-black text-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            💡 {{ alerta.sugerencia_coordinador }}
                        </div>

                    </div>
                </div>

                <div v-else class="bg-white border-4 border-gray-900 rounded-3xl p-12 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] max-w-2xl mx-auto">
                    <span class="text-5xl inline-block mb-3 animate-bounce">🟢</span>
                    <h3 class="font-black text-xl text-gray-900 uppercase">Radar Completamente Limpio</h3>
                    <p class="text-xs font-bold text-gray-500 mt-1 max-w-md mx-auto">
                        Ningún alumno cumple con los parámetros de alertas consecutivas de conducta o bajo bienestar esta semana. ¡Buen trabajo de seguimiento!
                    </p>
                </div>

                <div class="bg-gray-200 border-4 border-gray-900 rounded-2xl p-4 text-[10px] font-bold text-gray-600 tracking-wide leading-relaxed max-w-3xl mx-auto shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    🛡️ **AVISO DE ALCANCE PREVENTIVO:** Este ecosistema opera estrictamente como una bitácora automatizada de variables de rendimiento y observaciones de campo. No realiza evaluaciones psicológicas, interpretaciones clínicas ni diagnósticos de salud mental. Su fin es exclusivamente optimizar los canales de comunicación interna con los representantes legales del menor.
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>