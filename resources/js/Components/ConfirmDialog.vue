<script setup>
import { ref } from 'vue';

// Props para personalizar el diálogo
const props = defineProps({
    title: {
        type: String,
        default: 'Confirmar acción',
    },
    message: {
        type: String,
        default: '¿Estás seguro de realizar esta acción?',
    },
    confirmText: {
        type: String,
        default: 'Confirmar',
    },
    cancelText: {
        type: String,
        default: 'Cancelar',
    },
    confirmColor: {
        type: String,
        default: 'red', // 'red' o 'blue'
    },
});

// Estado para mostrar/ocultar el diálogo
const isOpen = ref(false);

// Función para abrir el diálogo
function open() {
    isOpen.value = true;
}

// Función para cerrar el diálogo
function close() {
    isOpen.value = false;
}

// Emitir eventos para que el componente padre sepa qué pasó
const emit = defineEmits(['confirm', 'cancel']);

// Confirmar acción
function confirmAction() {
    emit('confirm');
    close();
}

// Cancelar acción
function cancelAction() {
    emit('cancel');
    close();
}

// Exponer la función open para usarla desde el padre
defineExpose({
    open,
    close,
});
</script>

<template>
    <!-- Overlay (fondo oscuro) -->
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <!-- Ventana del diálogo -->
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl">
            <h3 class="text-lg font-bold mb-2">{{ title }}</h3>
            <p class="text-gray-600 mb-6">{{ message }}</p>
            
            <div class="flex justify-end gap-3">
                <button 
                    @click="cancelAction"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md transition"
                >
                    {{ cancelText }}
                </button>
                <button 
                    @click="confirmAction"
                    :class="[
                        'px-4 py-2 rounded-md text-white transition',
                        confirmColor === 'red' ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'
                    ]"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>