<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    jugadorId: Number,
    entrenamientoId: Number
});

const form = useForm({
    animo_entrenamiento: 3, 
    calidad_sueno: 3,
    siente_dolor: false,
    diversion: 3,
    cansancio_extremo: 1 
});

const enviarEncuesta = () => {
    form.post(route('autoevaluacion.store', { 
        jugador_id: props.jugadorId, 
        entrenamiento_id: props.entrenamientoId 
    }), {
        preserveScroll: true,
        onSuccess: () => {
            alert('¡Gracias! Tus respuestas del día se guardaron correctamente.');
        }
    });
};
</script>

<template>
    <div class="bg-white border-4 border-gray-900 rounded-3xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-4 max-w-sm mx-auto">
        <div class="border-b-2 border-gray-900 pb-2">
            <h3 class="font-black text-sm text-gray-900 uppercase tracking-tight">📊 Panel de Bienestar del Alumno</h3>
            <p class="text-[10px] font-bold text-gray-500">Auto-reporte rápido al finalizar el entrenamiento.</p>
        </div>
        
        <div class="space-y-3">
            <div class="flex flex-col gap-1 border-b border-gray-100 pb-2">
                <span class="text-xs font-black text-gray-900 uppercase">¿Cómo te sentiste entrenando hoy?</span>
                <div class="flex gap-2 mt-1">
                    <button type="button" @click="form.animo_entrenamiento = 1" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.animo_entrenamiento === 1 ? 'bg-red-400' : 'bg-white'">🔴</button>
                    <button type="button" @click="form.animo_entrenamiento = 2" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.animo_entrenamiento === 2 ? 'bg-amber-400' : 'bg-white'">🟡</button>
                    <button type="button" @click="form.animo_entrenamiento = 3" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.animo_entrenamiento === 3 ? 'bg-emerald-400' : 'bg-white'">🟢</button>
                </div>
            </div>

            <div class="flex flex-col gap-1 border-b border-gray-100 pb-2">
                <span class="text-xs font-black text-gray-900 uppercase">¿Pudiste dormir bien anoche?</span>
                <div class="flex gap-2 mt-1">
                    <button type="button" @click="form.calidad_sueno = 1" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.calidad_sueno === 1 ? 'bg-red-400' : 'bg-white'">🔴</button>
                    <button type="button" @click="form.calidad_sueno = 2" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.calidad_sueno === 2 ? 'bg-amber-400' : 'bg-white'">🟡</button>
                    <button type="button" @click="form.calidad_sueno = 3" class="w-10 h-10 rounded-xl border-2 border-gray-900 font-black text-sm transition active:scale-95" :class="form.calidad_sueno === 3 ? 'bg-emerald-400' : 'bg-white'">🟢</button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <span class="text-xs font-black text-gray-900 uppercase">¿Sientes algún dolor físico?</span>
                <button type="button" @click="form.siente_dolor = !form.siente_dolor" 
                    class="px-4 py-2 border-2 border-gray-900 rounded-xl text-xs font-black uppercase transition shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-0.5"
                    :class="form.siente_dolor ? 'bg-red-400 text-gray-900' : 'bg-gray-100 text-gray-700'">
                    {{ form.siente_dolor ? 'Sí, me duele 🚨' : 'No, todo bien 🟢' }}
                </button>
            </div>
        </div>

        <button @click="enviarEncuesta" type="button" :disabled="form.processing"
            class="w-full py-3 bg-blue-400 border-4 border-gray-900 text-gray-900 font-black text-xs uppercase rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-blue-300 transition active:translate-y-0.5">
            {{ form.processing ? 'Guardando...' : '🚀 Finalizar Autoevaluación' }}
        </button>

        <p class="text-[9px] text-gray-400 font-bold leading-tight pt-2 border-t border-gray-100">
            🛡️ Este módulo es preventivo y de seguimiento de bienestar general en cancha. No emite valoraciones clínicas ni diagnósticos psicológicos.
        </p>
    </div>
</template>