<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import InstallPWA from '@/Components/InstallPWA.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    UserGroupIcon, 
    RectangleStackIcon, 
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
    TrophyIcon,
    ChartBarIcon,
    Bars3Icon,
    XMarkIcon,
    BanknotesIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);

// 📋 Menú de navegación unificado
const coreMenuItems = [
    { name: 'Dashboard', href: 'dashboard', icon: HomeIcon },
    { name: 'Jugadores', href: 'jugadores.index', icon: UserGroupIcon },
    { name: 'Categorías', href: 'categorias.index', icon: RectangleStackIcon },
    { name: 'Entrenamientos', href: 'entrenamientos.index', icon: ClipboardDocumentListIcon },
    { name: 'Agenda Canchas', href: 'asignaciones.index', icon: CalendarDaysIcon },
    { name: 'Torneos', href: 'torneos.index', icon: TrophyIcon },
    { name: 'Rendimiento', href: 'reportes.index', icon: ChartBarIcon },
    { name: 'Finanzas', href: 'pagos.index', icon: BanknotesIcon },
];

const isAdmin = computed(() => user.value.roles?.some(r => r.name === 'superadmin' || r.name === 'administrador') || user.value.es_superadmin === 1);
const isEntrenador = computed(() => user.value.roles?.some(r => r.name === 'entrenador'));
</script>

<template>
    <div class="min-h-screen bg-amber-50/40 flex flex-col md:flex-row relative">
        
        <aside class="hidden md:flex md:w-64 bg-white border-r-4 border-gray-900 flex-col sticky top-0 h-screen z-20">
            <div class="p-6 border-b-4 border-gray-900 flex justify-center bg-gray-50">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="h-10 w-auto fill-current text-gray-800" />
                </Link>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto" v-if="isAdmin || isEntrenador">
                <Link v-for="item in coreMenuItems" :key="item.name" 
                    :href="route(item.href)"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 text-xs font-black uppercase tracking-tight transition-all duration-150"
                    :class="route().current(item.href.split('.')[0] + '*') 
                        ? 'bg-amber-300 text-gray-900 border-gray-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] translate-x-0.5' 
                        : 'text-gray-700 border-transparent hover:bg-gray-100 hover:border-gray-900'">
                    <component :is="item.icon" class="w-5 h-5 stroke-[2.5]" />
                    {{ item.name }}
                </Link>

                <Link :href="route('radar.index')" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 text-xs font-black uppercase tracking-tight transition-all duration-150 mt-4"
                    :class="route().current('radar.index') 
                        ? 'bg-rose-400 text-gray-900 border-gray-900 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]' 
                        : 'text-gray-700 border-transparent hover:bg-rose-50 hover:border-rose-400 hover:text-rose-600'">
                    <span>🧭</span> Radar Alertas
                </Link>
            </nav>

            <div class="p-4 border-t-4 border-gray-900 bg-gray-50">
                <Dropdown align="top" width="48">
                    <template #trigger>
                        <button type="button" class="w-full inline-flex items-center justify-between gap-2 border-4 border-gray-900 bg-amber-300 px-3 py-2 text-xs font-black uppercase rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-y-0.5">
                            <span class="truncate">👤 {{ user.name }}</span>
                            <svg class="h-3 w-3 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')" class="font-bold text-xs uppercase"> Mi Perfil </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button" class="font-bold text-xs uppercase text-red-600"> Cerrar Sesión </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </aside>

        <nav class="md:hidden border-b-4 border-gray-900 bg-white sticky top-0 z-20 w-full">
            <div class="px-4 py-3 flex h-16 justify-between items-center">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="h-8 w-auto fill-current text-gray-800" />
                </Link>

                <button @click="showingNavigationDropdown = !showingNavigationDropdown" 
                    class="inline-flex items-center justify-center p-2 rounded-xl border-2 border-gray-900 bg-amber-300 text-gray-900 font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <Bars3Icon v-if="!showingNavigationDropdown" class="h-5 w-5 stroke-[2.5]" />
                    <XMarkIcon v-else class="h-5 w-5 stroke-[2.5]" />
                </button>
            </div>

            <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="bg-white border-t-2 border-gray-900 animate-fadeIn">
                <div class="space-y-1 pb-3 pt-2 px-2" v-if="isAdmin || isEntrenador">
                    <Link v-for="item in coreMenuItems" :key="item.name" 
                        :href="route(item.href)" 
                        :class="route().current(item.href.split('.')[0] + '*') ? 'bg-amber-300' : ''"
                        class="font-black uppercase text-xs tracking-tight flex items-center gap-2 py-3 px-4 rounded-xl">
                        <component :is="item.icon" class="w-4 h-4 stroke-[2.5]" />
                        {{ item.name }}
                    </Link>

                    <Link :href="route('radar.index')" 
                        class="flex items-center gap-2 mx-2 my-2 px-4 py-3 text-xs font-black uppercase rounded-xl border-2 transition-all block text-center"
                        :class="route().current('radar.index') ? 'bg-rose-400 text-gray-900 border-gray-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-gray-700 border-gray-200 bg-gray-50'">
                        🧭 Radar Alertas
                    </Link>
                </div>
            </div>
        </nav>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="relative z-10 p-2 sm:p-4">
                <slot />
            </main>
        </div>

        <InstallPWA />
    </div>
</template>