<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'success', // 'success', 'error', 'warning', 'info'
    },
    duration: {
        type: Number,
        default: 3000, // 3 segundos
    },
});

// Emitimos evento cuando se cierra
const emit = defineEmits(['close']); // esto servira para crear un evento que se llamara close y que el padre podra escuchar para saber cuando se cerro el toast, sin esto el padre no sabra cuando se cerro el toast y no podra hacer nada al respecto, por eso es importante emitir este evento para que el padre pueda reaccionar a ello

// Controla si el toast está visible
const isVisible = ref(false);

// Función para mostrar el toast
function show() {
    isVisible.value = true;
    
    // Auto-cerrar después de `duration` ms
    if (props.duration > 0) {
        setTimeout(() => { // cuando el duration llegue a 0, se ejecutará la función hide
            hide();
        }, props.duration);
    }
}

// Función para ocultar el toast
function hide() { // cuando el boton de cerrar se presione se ocultara el toast y de paso le avisara al padre que se cerro el toast
    isVisible.value = false;
    emit('close'); // ← Emitimos evento al padre
}

// Exponer la función show para que el padre pueda llamarla
defineExpose({
    show,
});

// Colores según el tipo
function getTypeClasses() {
    const types = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
    };
    return types[props.type] || types.info;
}
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div 
            v-if="isVisible"
            class="fixed bottom-4 right-4 z-50 max-w-sm w-full"
        >
            <div :class="[getTypeClasses(), 'text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between']">
                <div class="flex items-center gap-2">
                    <!-- Icono según tipo -->
                    <span v-if="type === 'success'">✅</span>
                    <span v-else-if="type === 'error'">❌</span>
                    <span v-else-if="type === 'warning'">⚠️</span>
                    <span v-else>ℹ️</span>
                    
                    <span>{{ message }}</span>
                </div>
                
                <!-- Botón de cerrar -->
                <button 
                    @click="hide"
                    class="ml-4 text-white hover:text-gray-200 font-bold"
                >
                    ✕
                </button>
            </div>
        </div>
    </Transition>
</template>