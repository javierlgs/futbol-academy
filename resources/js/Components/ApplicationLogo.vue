<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const logo = computed(() => page.props.settings?.academy_logo
   ? `/storage/${page.props.settings.academy_logo}`
    : '/img/logo-default.png'
);
</script>

<template>
    <img :src="logo" class="h-9 w-auto" alt="Logo">
</template>