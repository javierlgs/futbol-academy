<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    partidoId: [Number, String],
    convocados: { type: Array, default: () => [] },
    tactica: { type: Object, default: null } 
});

const canvas = ref(null);
const colorSeleccionado = ref('#ffffff');
const form = useForm({ dibujo_data: '' });

let ctx = null;
let fichaArrastrada = null;
let dibujando = false;
// MANTENEMOS TU ESTRUCTURA: Array para guardar los trazos (necesario para persistencia)
const trazos = reactive([]); 

// Declaración de fichas (Tu lógica original intacta)
const fichas = reactive([
    ...props.convocados.map((j, i) => {
        const obj = j.jugador || j; 
        const nombre = obj.nombres || obj.nombre || 'J';
        const apellido = obj.apellidos || obj.apellido || '';
        
        return {
            id: j.id, 
            x: 50 + (i * 50), 
            y: 50,
            label: (nombre[0] + (apellido ? apellido[0] : '')).toUpperCase(),
            color: '#3b82f6', 
            tipo: 'jugador'
        };
    }),
    ...Array.from({ length: 6 }).map((_, i) => ({
        id: 'r-' + i, x: 50 + (i * 50), y: 500,
        label: 'R' + (i + 1), color: '#ef4444', tipo: 'rival'
    }))
]);

const dibujarCancha = () => {
    ctx.fillStyle = '#16a34a'; ctx.fillRect(0, 0, 400, 600);
    ctx.strokeStyle = '#ffffff'; ctx.lineWidth = 3;
    ctx.strokeRect(30, 30, 340, 560);
    ctx.beginPath(); ctx.moveTo(30, 310); ctx.lineTo(370, 310); ctx.stroke();
    ctx.strokeRect(100, 30, 200, 80); ctx.strokeRect(100, 510, 200, 80);
};

// Función maestra para redibujar todo sin perder nada
const redibujar = () => {
    ctx.clearRect(0, 0, 400, 600);
    dibujarCancha();
    trazos.forEach(t => {
        ctx.strokeStyle = t.color;
        ctx.beginPath();
        t.puntos.forEach((p, i) => i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y));
        ctx.stroke();
    });
    dibujarFichas();
};

const dibujarFichas = () => {
    fichas.forEach(f => {
        ctx.fillStyle = f.color;
        ctx.beginPath(); ctx.arc(f.x, f.y, 15, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = 'white'; ctx.font = 'bold 12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(f.label, f.x, f.y + 5);
    });
};

const iniciarDibujo = (e) => {
    const rect = canvas.value.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    const x = clientX - rect.left;
    const y = clientY - rect.top;
    
    fichaArrastrada = fichas.find(f => Math.hypot(f.x - x, f.y - y) < 25);
    if (!fichaArrastrada) {
        dibujando = true;
        trazos.push({ color: colorSeleccionado.value, puntos: [{x, y}] });
        ctx.beginPath(); ctx.moveTo(x, y);
    }
};

const dibujarTrazo = (e) => {
    if (!canvas.value) return;
    const rect = canvas.value.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    const x = clientX - rect.left;
    const y = clientY - rect.top;

    if (fichaArrastrada) {
        fichaArrastrada.x = x; fichaArrastrada.y = y;
        redibujar();
    } else if (dibujando) {
        trazos[trazos.length - 1].puntos.push({x, y});
        ctx.strokeStyle = colorSeleccionado.value;
        ctx.lineTo(x, y); ctx.stroke();
    }
};

const guardar = () => {
    // GUARDAR TODO: fichas y trazos
    form.dibujo_data = JSON.stringify({ fichas, trazos });
    form.post(route('partidos.guardarTacticas', props.partidoId), {
        onSuccess: () => alert('Táctica guardada en historial')
    });
};

defineExpose({
    cargarPosiciones: (datos) => {
        const d = typeof datos === 'string' ? JSON.parse(datos) : datos;
        fichas.splice(0, fichas.length, ...d.fichas);
        trazos.splice(0, trazos.length, ...d.trazos);
        redibujar();
    }
});

onMounted(() => {
    ctx = canvas.value.getContext('2d');
    if (props.tactica && props.tactica.dibujo_data) {
        const d = JSON.parse(props.tactica.dibujo_data);
        fichas.splice(0, fichas.length, ...d.fichas);
        trazos.splice(0, trazos.length, ...d.trazos);
    }
    redibujar();
});
</script>

<template>
    <div class="flex flex-col items-center gap-4 bg-gray-200 p-4 rounded-3xl">
        <div class="flex gap-2">
            <button v-for="c in ['#ffffff', '#facc15', '#ef4444', '#3b82f6']" :key="c" 
                @click="colorSeleccionado = c" :style="{ background: c }" class="w-8 h-8 rounded-full border-2 border-black"></button>
        </div>
        <canvas ref="canvas" width="400" height="600" 
            class="rounded-xl shadow-2xl touch-none border-4 border-white cursor-crosshair"
            @mousedown="iniciarDibujo" @mousemove="dibujarTrazo" @mouseup="dibujando = false; fichaArrastrada = null"
            @touchstart="iniciarDibujo" @touchmove="dibujarTrazo" @touchend="dibujando = false; fichaArrastrada = null">
        </canvas>
        <button @click="guardar" class="bg-gray-900 text-white w-full py-3 rounded-xl font-black uppercase text-[10px] hover:bg-black transition">💾 Guardar Historial Táctico</button>
    </div>
</template>