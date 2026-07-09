<template>
  <div class="p-6 bg-amber-50 min-h-screen text-gray-900 font-sans">
    
    <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 mb-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <span class="bg-purple-300 text-gray-900 border-2 border-gray-900 font-black px-3 py-1 rounded-full text-xs uppercase tracking-wider">
            Área de Bienestar Emocional
          </span>
          <h1 class="text-3xl md:text-4xl font-black uppercase mt-2 tracking-tight">
            {{ jugador.nombres }} {{ jugador.apellidos }}
          </h1>
          <p class="text-sm font-bold text-gray-600 mt-1">
            Categoría: <span class="underline">{{ jugador.categoria?.año_nacimiento }} - {{ jugador.categoria?.sexo }}</span> | Sede: {{ jugador.sede?.nombre }}
          </p>
        </div>
        <div class="flex items-center gap-3">
          <span class="font-black text-sm uppercase">Estado Mental Actual:</span>
          <div :class="[
            'border-4 border-gray-900 px-4 py-2 rounded-2xl font-black uppercase text-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]',
            ultimoSemaforo === 'verde' ? 'bg-green-400' : ultimoSemaforo === 'amarillo' ? 'bg-yellow-400' : 'bg-red-400'
          ]">
            Semaforo {{ ultimoSemaforo || 'Sin Datos' }}
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      
      <div v-if="puedeCrear" class="lg:col-span-5 bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
        <h2 class="text-2xl font-black uppercase mb-4 tracking-tight flex items-center gap-2">
          📝 Nueva Evaluación Clínica
        </h2>
        
        <form @submit.prevent="submit" class="space-y-4">
          <div class="bg-purple-50 border-4 border-gray-900 rounded-2xl p-4 space-y-3">
            <h3 class="font-black uppercase text-xs tracking-wider text-purple-800">Métricas Clínicas (Escala 1-10)</h3>
            
            <div>
              <div class="flex justify-between font-bold text-sm">
                <label>Nivel de Ansiedad</label>
                <span class="font-black text-purple-600">{{ form.puntaje_ansiedad }}/10</span>
              </div>
              <input type="range" min="1" max="10" v-model.number="form.puntaje_ansiedad" class="w-full accent-gray-900 cursor-pointer h-3 bg-gray-200 rounded-lg appearance-none border-2 border-gray-900" />
            </div>

            <div>
              <div class="flex justify-between font-bold text-sm">
                <label>Indicadores de Depresión</label>
                <span class="font-black text-purple-600">{{ form.puntaje_depresion }}/10</span>
              </div>
              <input type="range" min="1" max="10" v-model.number="form.puntaje_depresion" class="w-full accent-gray-900 cursor-pointer h-3 bg-gray-200 rounded-lg appearance-none border-2 border-gray-900" />
            </div>

            <div>
              <div class="flex justify-between font-bold text-sm">
                <label>Autoestima / Seguridad</label>
                <span class="font-black text-purple-600">{{ form.puntaje_autoestima }}/10</span>
              </div>
              <input type="range" min="1" max="10" v-model.number="form.puntaje_autoestima" class="w-full accent-gray-900 cursor-pointer h-3 bg-gray-200 rounded-lg appearance-none border-2 border-gray-900" />
            </div>
          </div>

          <div>
            <label class="block font-black text-xs uppercase mb-1">Observaciones Clínicas (Confidencial)</label>
            <textarea v-model="form.observaciones_clinicas" rows="3" class="w-full text-base font-bold border-4 border-gray-900 rounded-2xl p-2 focus:ring-0 focus:border-purple-500" placeholder="Análisis clínico profundo..."></textarea>
          </div>

          <div>
            <label class="block font-black text-xs uppercase mb-1">Recomendaciones para el Entrenador</label>
            <textarea v-model="form.recomendaciones_entrenador" rows="2" class="w-full text-base font-bold border-4 border-gray-900 rounded-2xl p-2 focus:ring-0 focus:border-green-500" placeholder="Acciones en campo..."></textarea>
          </div>

          <div>
            <label class="block font-black text-xs uppercase mb-1">Indicaciones para Padres / Representante</label>
            <textarea v-model="form.recomendaciones_padres" rows="2" class="w-full text-base font-bold border-4 border-gray-900 rounded-2xl p-2 focus:ring-0 focus:border-blue-500" placeholder="Apoyo en el hogar..."></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-black text-xs uppercase mb-1">Semáforo de Riesgo</label>
              <select v-model="form.semaforo_riesgo" class="w-full font-black border-4 border-gray-900 rounded-2xl p-2 bg-white">
                <option value="verde">🟢 Verde (Bajo)</option>
                <option value="amarillo">🟡 Amarillo (Medio)</option>
                <option value="rojo">🔴 Rojo (Alto)</option>
              </select>
            </div>
            <div class="flex flex-col justify-end">
              <label class="inline-flex items-center gap-2 cursor-pointer p-2 border-4 border-gray-900 bg-red-50 rounded-2xl h-[46px]">
                <input type="checkbox" v-model="form.alerta_coordinacion" class="rounded border-4 border-gray-900 text-gray-900 focus:ring-0 w-5 h-5" />
                <span class="text-xs font-black uppercase text-red-700">Alerta Coordinación</span>
              </label>
            </div>
          </div>

          <button type="submit" class="w-full min-h-[44px] bg-purple-400 border-4 border-gray-900 text-gray-900 font-black uppercase rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
            Guardar Diagnóstico
          </button>
        </form>
      </div>

      <div :class="puedeCrear ? 'lg:col-span-7' : 'lg:col-span-12'" class="space-y-6">
        
        <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
          <h2 class="text-2xl font-black uppercase mb-4 tracking-tight">📈 Curva de Evolución Emocional</h2>
          
          <div v-if="chartData.labels.length > 0" class="w-full overflow-x-auto">
            <svg class="w-full min-w-[500px]" height="200" viewBox="0 0 500 200">
              <line x1="40" y1="20" x2="480" y2="20" stroke="#E5E7EB" stroke-width="2" />
              <line x1="40" y1="70" x2="480" y2="70" stroke="#E5E7EB" stroke-width="2" />
              <line x1="40" y1="120" x2="480" y2="120" stroke="#E5E7EB" stroke-width="2" />
              <line x1="40" y1="170" x2="480" y2="170" stroke="#111827" stroke-width="3" />

              <text x="15" y="25" class="text-[10px] font-black" fill="#6B7280">10</text>
              <text x="15" y="75" class="text-[10px] font-black" fill="#6B7280">5</text>
              <text x="15" y="125" class="text-[10px] font-black" fill="#6B7280">2</text>
              
              <polyline fill="none" stroke="#EF4444" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                :points="generarPuntosSVG(chartData.ansiedad)" />
              
              <polyline fill="none" stroke="#3B82F6" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                :points="generarPuntosSVG(chartData.depresion)" />
              
              <polyline fill="none" stroke="#10B981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                :points="generarPuntosSVG(chartData.autoestima)" />
            </svg>

            <div class="flex justify-center gap-6 mt-2 font-black text-xs uppercase">
              <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded-full inline-block border border-black"></span> Ansiedad</span>
              <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded-full inline-block border border-black"></span> Depresión</span>
              <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded-full inline-block border border-black"></span> Autoestima</span>
            </div>
          </div>
          <div v-else class="text-center py-8 font-black text-gray-400 uppercase">
            No hay registros suficientes para armar la gráfica.
          </div>
        </div>

        <div class="bg-white border-4 border-gray-900 rounded-3xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
          <h2 class="text-2xl font-black uppercase mb-4 tracking-tight">🗂️ Registro de Intervenciones</h2>
          
          <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
            <div v-for="evaluacion in evaluaciones" :key="evaluacion.id" class="border-4 border-gray-900 rounded-2xl p-4 bg-stone-50">
              <div class="flex justify-between items-start border-b-2 border-gray-900 pb-2 mb-2">
                <div>
                  <span class="font-black text-sm block">Evaluación #{{ evaluacion.id }}</span>
                  <span class="text-xs font-bold text-gray-500">Por: {{ eval.psicologo?.name }} | {{ formatearFecha(eval.created_at) }}</span>
                </div>
                <span :class="[
                  'px-3 py-1 border-2 border-gray-900 rounded-full text-xs font-black uppercase',
                  eval.semaforo_riesgo === 'verde' ? 'bg-green-300' : eval.semaforo_riesgo === 'amarillo' ? 'bg-yellow-300' : 'bg-red-300'
                ]">
                  {{ eval.semaforo_riesgo }}
                </span>
              </div>

              <div class="space-y-2 text-sm">
                <p v-if="eval.observaciones_clinicas" class="font-medium">
                  <strong class="text-purple-900 uppercase text-xs block font-black">Observaciones Clínicas:</strong>
                  {{ eval.observaciones_clinicas }}
                </p>
                <p class="font-medium">
                  <strong class="text-green-900 uppercase text-xs block font-black">Indicaciones Entrenador:</strong>
                  {{ eval.recomendaciones_entrenador }}
                </p>
                <p class="font-medium">
                  <strong class="text-blue-900 uppercase text-xs block font-black">Indicaciones Padres:</strong>
                  {{ eval.recomendaciones_padres }}
                </p>
              </div>
            </div>

            <div v-if="evaluaciones.length === 0" class="text-center py-4 font-bold text-gray-500">
              Ninguna evaluación registrada previamente.
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  jugador: Object,
  evaluaciones: Array,
  chartData: Object,
  puedeCrear: Boolean
});

// Inicialización de Formulario Reactivo de Inertia
const form = useForm({
  observaciones_clinicas: '',
  puntaje_ansiedad: 1,
  puntaje_depresion: 1,
  puntaje_autoestima: 10,
  recomendaciones_entrenador: '',
  recomendaciones_padres: '',
  semaforo_riesgo: 'verde',
  alerta_coordinacion: false
});

// Obtener el estado del último semáforo calculado
const ultimoSemaforo = computed(() => {
  if (props.evaluaciones.length === 0) return null;
  return props.evaluaciones[props.evaluaciones.length - 1].semaforo_riesgo;
});

// Enviar datos al controlador Laravel
const submit = () => {
  form.post(route('jugadores.psicologico.store', props.jugador.id), {
    onSuccess: () => {
      form.reset();
    }
  });
};

// Helper para graficar las líneas SVG de forma proporcional
const generarPuntosSVG = (valores) => {
  if (!valores || valores.length === 0) return '';
  const anchoTotal = 440; // Rango utilizable en X (480 - 40)
  const xSeparacion = valores.length > 1 ? anchoTotal / (valores.length - 1) : anchoTotal;
  
  return valores.map((val, index) => {
    const x = 40 + (index * xSeparacion);
    // Escala inversa para Y en SVG: 10 arriba (y=20), 1 abajo (y=170)
    const y = 170 - ((val - 1) * (150 / 9)); 
    return `${x},${y}`;
  }).join(' ');
};

// Formateador de fechas legible
const formatearFecha = (fechaStr) => {
  return new Date(fechaStr).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>