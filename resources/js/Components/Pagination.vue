<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links: Array,
});

// Verificación temporal - muestra los datos en consola
/* console.log("link 0 url: ", props.links[0]?.url);
console.log('Pagination links:', props.links); */

// Función para obtener etiqueta traducida
function getLinkLabel(link) {
    if (link.label === '&laquo; Previous') return 'Anterior';
    if (link.label === 'Next &raquo;') return 'Siguiente';
    return link.label;
}
</script>

<template>
    <!-- Verificación temporal - muestra el contenido de links -->
    <!-- <div class="bg-yellow-100 p-2 mb-4 text-sm">
        <strong>Debug:</strong> Links: {{ links ? links.length : 'No hay links' }}
    </div> -->

    <div class="flex justify-center items-center gap-2 mt-6">
        <!-- Enlace "Anterior" -->
         <!-- <pre class="bg-gray-100 p-4 rounded mb-4 text-sm">
            {{ links[0]?.url }}
        </pre>
          -->
        <Link
             v-if="links && links[0] && links[0].url"
             :href="links[0]?.url"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md transition"
            :class="{ 'opacity-50 cursor-not-allowed': !links[0]?.url }"
            :preserve-state="true"
            preserve-scroll
        >
            Anterior
        </Link>
        <span v-else-if="links && links[0]" class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">Anterior</span>

        <!-- Enlaces de páginas -->
        <Link
            v-for="(link, index) in links.slice(1, -1)"
            :key="index"
            :href="link.url || '#'"
            class="px-4 py-2 rounded-md transition"
            :class="{
                'bg-blue-500 text-white': link.active,
                'bg-gray-200 hover:bg-gray-300': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
            }"
            :preserve-state="true"
            preserve-scroll
        >
            {{ link.label }}
        </Link>

        <!-- Enlace "Siguiente" -->
        <Link
            v-if="links && links[links.length - 1] && links[links.length - 1].url"
            :href="links[links.length - 1]?.url"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md transition"
            :class="{ 'opacity-50 cursor-not-allowed': !links[links.length - 1]?.url }"
            :preserve-state="true"
            preserve-scroll
        >
            Siguiente
        </Link>
        <!-- Si no tiene URL, mostramos un span deshabilitado -->
        <span
            v-else-if="links && links[links.length - 1]"
            class="px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed"
        >
            Siguiente
        </span>
    </div>
</template>