<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Verificación estricta de privilegios administrativos
const isAdmin = computed(() => {
    return user.value.roles?.some(r => r.name === 'superadmin' || r.name === 'administrador') || user.value.es_superadmin === 1;
});

// El catálogo de aplicaciones del sistema estilo Bento Grid táctil 
const apps = [
    { name: 'Fichas de Jugadores', icon: '🏃‍♂️', route: 'jugadores.index', desc: 'Altas, historiales y estatus', color: 'bg-amber-400', public: true },
    { name: 'Categorías del Club', icon: '📅', route: 'categorias.index', desc: 'Agrupación por año de nacimiento', color: 'bg-emerald-400', public: true },
    { name: 'Control de Prácticas', icon: '⚽', route: 'entrenamientos.index', desc: 'Asistencias y checklists clínicos', color: 'bg-sky-400', public: true },
    { name: 'Bienestar Emocional', icon: '🧠', route: 'psicologia.index', desc: 'Evaluaciones clínicas psicológicas', color: 'bg-purple-400', public: true },
    { name: 'Caja Diaria', icon: '💰', route: 'caja.index', desc: 'Flujos e ingresos en tiempo real', color: 'bg-lime-400', adminOnly: true },
    { name: 'Egresos y Gastos', icon: '📉', route: 'gastos.index', desc: 'Insumos y cuentas del club', color: 'bg-rose-400', adminOnly: true },
    { name: 'Facturación SRI', icon: '🧾', route: 'facturas.index', desc: 'Comprobantes y recibos legales', color: 'bg-stone-400', adminOnly: true },
    { name: 'Reportes Globales', icon: '📊', route: 'reportes.index', desc: 'Estadísticas de rendimiento y finanzas', color: 'bg-cyan-400', adminOnly: true },
    { name: 'Sedes Operativas', icon: '🏢', route: 'admin.sedes.index', desc: 'Administrar complejos del club', color: 'bg-orange-400', adminOnly: true },
    { name: 'Personal y Roles', icon: '🔑', route: 'admin.usuarios.index', desc: 'Asignar entrenadores y roles', color: 'bg-indigo-400', adminOnly: true },
];

// Filtrar las aplicaciones según rol
const allowedApps = computed(() => {
    return apps.filter(app => app.public || (app.adminOnly && isAdmin.value));
});
</script>

<template>
    <Head title="Consola Operativa" />

    <AuthenticatedLayout>
        <div class="py-8 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 space-y-8">
                
                <div>
                    <h3 class="font-black text-sm uppercase text-slate-300 tracking-widest mb-4">
                        Consola de Módulos (Acceso Rápido)
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        <Link v-for="app in allowedApps" :key="app.name"
                              :href="route().has(app.route) ? route(app.route) : '#'"
                              class="bg-white border-2 border-slate-300 rounded-2xl p-5 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between min-h-[160px] group">
                            
                            <div class="flex justify-between items-start">
                                <div :class="['w-12 h-12 border-2 border-slate-900 rounded-xl flex items-center justify-center text-2xl shadow-sm', app.color]">
                                    {{ app.icon }}
                                </div>
                                <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-1 rounded-md uppercase tracking-wider group-hover:bg-blue-600 transition">
                                    Ingresar
                                </span>
                            </div>

                            <div class="mt-4">
                                <h4 class="font-black text-base uppercase tracking-tight text-slate-900 leading-tight">
                                    {{ app.name }}
                                </h4>
                                <p class="text-xs font-bold text-slate-500 mt-1 leading-snug">
                                    {{ app.desc }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

                <div v-if="isAdmin">
                    <h3 class="font-black text-sm uppercase text-slate-300 tracking-widest mb-4">
                        Resumen del Mes
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-md">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Ingresos Mensuales</p>
                            <p class="text-3xl font-black text-emerald-600 mt-1">$0,00</p>
                        </div>
                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-md">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Gastos Mensuales</p>
                            <p class="text-3xl font-black text-rose-600 mt-1">$0,00</p>
                        </div>
                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-md">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Utilidad Balance</p>
                            <p class="text-3xl font-black text-blue-600 mt-1">$0,00</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>