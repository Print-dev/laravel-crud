<script setup>
import { Link,router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
const props = defineProps({
    product: {
        type: Object, // esperamos un objeto
        default: null, // si no se pasa un producto, será null
    },
    categories: {
        type: Array,
        default: null,
    },
    tags: {
        type: Array,
        default: () => []
    }
    
});
const page = usePage();// obtiene los props de la página actual, incluyendo errores de validación
const isSubmitting = ref(false); // para manejar el estado de envío del formulario
const name = ref(props.product?.name || '');
const description = ref(props.product?.description || '');
const price = ref(props.product?.price || 0);
const stock = ref(props.product?.stock || 0); 
const category_id = ref(props.product?.category_id || null);
const image = ref(null);
const imagePreview = ref(props.product?.image ? `/storage/${props.product.image}` : null);

const isEditing = computed(() => props.product !== null);
console.log("tags del producto: ", props.product?.tags);
const selectedTags = ref(props.product?.tags?.map(t => t.id) || [])

// nueva funcion para la subida de imagen
function handleImageChange(event){
    const file = event.target.files[0];
    if(file){
        image.value = file; // el file contendra el array de la imagen subida
        imagePreview.value = URL.createObjectURL(file);
    }
}

function guardarProducto() {
    isSubmitting.value = true; // establecemos que el formulario está siendo enviado
    const formData = new FormData(); // para poder enviar imagenes aparte de datos del producto
    formData.append('name', name.value);
    formData.append('description', description.value)   
    formData.append('price', price.value)   
    formData.append('stock', stock.value)   
    formData.append('category_id', category_id.value)   
    // agregar tags al formData
    selectedTags.value.forEach((tagId, index) => {
        formData.append(`tags[${index}]`, tagId);
    });

    if(image.value){
        formData.append('image', image.value)
    }

    if(isEditing.value) {
        // Para PUT con archivos, usar POST + _method
        formData.append('_method', 'PUT')
        router.post(route('products.update', props.product.id),formData,{forceFormData:true,onSuccess: () => {
            isSubmitting.value = false; // restablecemos el estado de envío
        },onError: () => {
            isSubmitting.value = false; // restablecemos el estado de envío
        }});
    } else {
        router.post(route('products.store'), formData, {
           forceFormData: true,
            onSuccess: () => {
                name.value = '';
                description.value = '';
                price.value = 0;
                stock.value = 0;
                category_id.value = null;
                selectedTags.value = []
                image.value = null;
                imagePreview.value = null;
                isSubmitting.value = false; // restablecemos el estado de envío

            },onError: () => {
                isSubmitting.value = false; // restablecemos el estado de envío
            }
        }); 
    }
}

</script>

<template>
    <form @submit.prevent="guardarProducto" class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6">
            {{ isEditing ? 'Editar producto' : 'Crear nuevo producto' }}
        </h2>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Nombre del producto
            </label>
            <input
                id="name"
                type="text"
                placeholder="Ej: Laptop HP"
                v-model="name"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting"
            >
            <p v-if="page.props.errors.name" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.name }}
            </p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Descripción
            </label>
            <input
                id="description"
                type="text"
                placeholder="Descripción del producto"
                v-model="description"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting"
            >
            <p v-if="page.props.errors.description" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.description }}
            </p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                Precio (S/)
            </label>
            <input
                id="price"
                type="number"
                placeholder="0.00"
                v-model="price"
                step="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting"
            >
            <p v-if="page.props.errors.price" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.price }}
            </p>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="stock">
                Stock
            </label>
            <input
                id="stock"
                type="number"
                placeholder="0"
                v-model="stock"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting"
            >
            <p v-if="page.props.errors.stock" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.stock }}
            </p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                Categoría
            </label>
            <select
                id="category"
                v-model="category_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting" 
            >
                <option :value="null">Sin categoría</option>
                <option 
                    v-for="category in categories" 
                    :key="category.id" 
                    :value="category.id"
                >
                    {{ category.name }} 
                </option>
            </select>
            <p v-if="page.props.errors.category_id" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.category_id }}
            </p>
        </div>
        <!-- Tags -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Etiquetas
            </label>
            
            <div class="flex flex-wrap gap-2">
                <label 
                    v-for="tag in tags" 
                    :key="tag.id"
                    class="inline-flex items-center px-3 py-1 rounded-full cursor-pointer transition"
                    :class="selectedTags.includes(tag.id) 
                        ? 'bg-blue-500 text-white' 
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                >
                    <input
                        type="checkbox"
                        :value="tag.id"
                        v-model="selectedTags"
                        class="hidden"
                    >
                    {{ tag.name }}
                </label>
            </div>
            
            <p v-if="page.props.errors.tags" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.tags }}
            </p>
        </div>
        <!-- ✨ Imagen -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Imagen del producto
            </label>
            
            <!-- Vista previa -->
            <div v-if="imagePreview" class="mb-3">
                <img 
                    :src="imagePreview" 
                    alt="Vista previa" 
                    class="w-32 h-32 object-cover rounded-lg border"
                >
            </div>
            
            <!-- Input de archivo -->
            <input
                type="file"
                accept="image/*"
                @change="handleImageChange"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isSubmitting"
            >
            
            <p v-if="page.props.errors.image" class="text-red-500 text-sm mt-1">
                {{ page.props.errors.image }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Formatos: JPG, PNG, GIF, WEBP. Máximo 2MB.
            </p>
        </div>

        <button 
            type="submit" 
            :disabled="isSubmitting"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-200"
            :class="{
                'opacity-50 cursor-not-allowed': isSubmitting
            }"
        >
            {{ isSubmitting ? 'Guardando...' : (isEditing ? 'Actualizar producto' : 'Guardar producto') }}
        </button>
    </form>
</template>