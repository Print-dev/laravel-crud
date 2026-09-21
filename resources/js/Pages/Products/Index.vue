<template>
    <AppLayout>
        <h1 class="text-3xl font-bold mb-6">Lista de productos</h1>
        <pre>{{ categories }}</pre>
        <!-- Mensaje flash -->
        <!-- <div v-if="page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ page.props.flash.success }}
        </div> -->
        <!-- el ":" sirve para enviar una expresion de jvascript por atributos del componente al hijo, y desde el hijo desde sus props definidos sabra que tipo de datos es, sin el ":" es como enviarle un string/texto simple, por eso en duration se manda 3000 ya que es un dato tipo Number de javascript y es lo que espera la propiedad definida de Toast.vue  -->
        

        <!-- 🔍 BARRA DE FILTROS -->
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <!-- Búsqueda por nombre -->
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <!-- EL V-MODEL sirve para vincular el valor del input con la variable searchTerm, es como si fuera un value normal pero reactivo -->
                         <!-- el @input sirve para aplicar los filtros en tiempo real mientras el usuario escribe , osea ejecutara la funcion aplicarFiltros -->
                        <input
                            type="text"
                            v-model="searchTerm" 
                            @input="aplicarFiltros"
                            placeholder="🔍 Buscar productos por nombre..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <!-- Botón para limpiar búsqueda (solo si hay texto) -->
                        <button
                            v-if="searchTerm"
                            @click="searchTerm = ''; aplicarFiltros()"
                            class="absolute right-3 top-2 text-gray-400 hover:text-gray-600"
                        >
                            ✕
                        </button>
                    </div>

                </div>
                <label class="font-semibold text-gray-700">Filtrar por categoría:</label>
                <select 
                    v-model="selectedCategory"
                    @change="aplicarFiltros"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Todas las categorías</option>
                    <option 
                        v-for="category in categories" 
                        :key="category.id" 
                        :value="category.id"
                    >
                        {{ category.name }} ({{ category.products_count }})
                    </option>
                </select>
            </div>

            
            <!-- Botón para limpiar filtro (solo visible cuando hay filtro activo) -->
            <button 
                v-if="selectedCategory"
                @click="limpiarFiltro"
                class="text-sm text-red-500 hover:text-red-700"
            >
                ✕ Limpiar filtro
            </button>
        </div>
        <!-- ✨ Filtro por tags -->
            <div class="mt-4 pt-4 border-t">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Filtrar por etiquetas:
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
                            @change="aplicarFiltros"
                            class="hidden"
                        >
                        {{ tag.name }}
                    </label>
                </div>
            </div>

        <!-- <pre>{{ products }}</pre> -->
        <!-- 🔑 Formulario solo si puede crear -->
        <ProductForm v-if="canCreate" :categories="categories" :tags="tags"/>
        <!-- Mensaje si no está logueado -->
        <div v-else class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <Link href="/login" class="font-semibold underline">Inicia sesión</Link> para crear productos.
        </div>
        <hr class="my-8">

        <!-- Lista de productos -->
        <div v-if="products.data.length > 0">
        <div v-for="product in products.data" :key="product.id" class="border rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <!-- MOSTRAR IMAGEN DEL PRODUCTO -->
                <div class="flex-shrink-0">
                    <img 
                        v-if="product.image" 
                        :src="`/storage/${product.image}`" 
                        :alt="product.name"
                        class="w-24 h-24 object-cover rounded-lg"
                    >
                    <div v-else class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                        <span class="text-gray-400 text-3xl">📦</span>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold">{{ product.name }}</h2>
                    <p class="text-gray-600">{{ product.description || 'Sin descripción' }}</p>
                    
                    <!-- Categoría con colores -->
                    <div class="mt-2">
                        <span 
                            v-if="product.category"
                            class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                            :class="getCategoryColor(product.category.id)"
                        >
                            {{ product.category.name }}
                        </span>
                        <span 
                            v-else
                            class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-gray-200 text-gray-600"
                        >
                            Sin categoría
                        </span>
                    </div>
                    <!-- ✨ Tags -->
                    <div v-if="product.tags && product.tags.length > 0" class="mt-2 flex flex-wrap gap-1">
                        <span 
                            v-for="tag in product.tags" 
                            :key="tag.id"
                            class="inline-block px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs"
                        >
                            #{{ tag.name }}
                        </span>
                    </div>
                    <div class="mt-2 flex gap-4">
                        <p class="text-lg font-semibold text-blue-600">S/ {{ product.price }}</p>
                        <p class="text-sm text-gray-500">Stock: {{ product.stock }}</p>
                    </div>
                </div>
                
                <div class="flex gap-2 ml-4">
                    <Link 
                        :href="route('products.edit', product.id)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition"
                    >
                        Editar
                    </Link>
                    
                    <button 
                        @click="confirmarEliminacion(product.id)"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        </div>
        <!-- Mensaje cuando no hay productos -->
        <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="mt-4 text-gray-500 text-lg">No hay productos en esta categoría</p>
            <button 
                @click="limpiarFiltro" 
                class="mt-2 inline-block text-blue-500 hover:text-blue-700"
            >
                Ver todos los productos
            </button>
        </div>

        <Pagination :links="products.links" />
        <Toast
            ref="toast"
            type="success"
            :message="page.props.flash.success || ''"
            :duration="3000"
            @close="() => {}" 
        ></Toast>
        <ConfirmDialog 
            ref="confirmDialog"
            title="Eliminar producto"
            message="¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer."
            confirmText="Sí, eliminar"
            cancelText="Cancelar"
            confirmColor="red"
            @confirm="eliminarProducto"
            @cancel="cancelarEliminacion"
        />
    </AppLayout>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import ProductForm from '@/Components/ProductForm.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, onMounted, ref, watch } from 'vue';

const page = usePage();
const toast = ref(null); // aqui creo una referencia de toast para luego poder llamar a la funcion show() desde este componente usando @ref y mostrar el toast cuando haya un mensaje flash de exito, y tambien para poder llamar a la funcion hide() desde este componente cuando se cierre el toast
let flashMessage  = null;

const props = defineProps({
    products: Object,
    categories: Array,
    tags: Array,
    filters: Object // esto es object porque puede contener diferentes tipos de filtros, no solo strings
});
const selectedCategory = ref(props.filters?.category || ''); // esta variable se inicializa con el valor del filtro de categoría si existe, de lo contrario se inicializa como una cadena vacía,  y se ira actualizando cada vez que el usuario seleccione una categoría diferente en el select de categorías, y se usará para aplicar el filtro de categoría al hacer la petición al servidor
const searchTerm = ref(props.filters?.search || '');
const selectedTags = ref(props.filters?.tags || []);
console.log("filters recarados: ",selectedTags);
function aplicarFiltros() {
    const query = {};
    if (selectedCategory.value) {
        query.category = selectedCategory.value;
    }
    if (searchTerm.value) {
        query.search = searchTerm.value;
    }
    /* selectedTags.value.forEach((tagId) => {
        params.append('tags[]', tagId);
    }); */
     if (selectedTags.value.length > 0) {
        query.tags = selectedTags.value;
    }
    router.get(route('products.index'), query, { preserveState: true, replace: true });
}

//const aplicarFiltrosDebounced  = debounce(aplicarFiltros, 300); // 300ms de retardo para evitar demasiadas peticiones al servidor mientras el usuario escribe

function limpiarFiltro() {
    selectedCategory.value = '';
    searchTerm.value = '';
    selectedTags.value = [];
    router.get('/products', {}, { preserveState: true, replace: true });
}

watch(() => props.filters, (newFilters) => { // el newFilters es el nuevo valor de los filtros que se recibe desde el servidor, y se usa para actualizar las variables selectedCategory y searchTerm cada vez que cambian los filtros en el servidor, por ejemplo cuando se aplica un filtro de categoría o de búsqueda

    if (newFilters){
        console.log("newfiltres ", newFilters);
        selectedCategory.value = newFilters?.category || '';
        searchTerm.value = newFilters?.search || '';
        selectedTags.value = (newFilters.tags || []).map(Number); // esto convierte trodo lo de adentro en NUMBER
    }
}, {deep: true}); // el deep true sirve para que el watcher detecte cambios en propiedades anidadas del objeto filters, como category y search, y actualice selectedCategory y searchTerm en consecuencia


// primero se ejecuta watch gracias a immediate: true, asi que asta el momento newMessage y flashMessage ya contiene algo, pero toast.value aun sera null, asi que automaticamente pasara  a ejecutar onMounted
// sin immediate:true el watch se ejecutara  solo cuanda el page.props.flash.success suceda un cambio, osea no se ejecutara apenas inicie el index.vue
watch(() => page.props.flash.success, async (newMessage) => {
    if (newMessage) { // verificar que toast value exista
        flashMessage = newMessage;
        if(toast.value){
            toast.value.show();
        }
    }
   /* if(newMessage){
    await nextTick();
    if(toast.value){
        toast.value.show();
    }
   } */
}, { immediate: true});  // gracias al innmediate se ejecuta primero que OnMounted detecta el mensaje flash

// onMounted se ejecutara despues de q el componente index.vue fue montado y cargado, para este momento toast.value ya tendra valor, asi que newMessage tambien, entonces mostrara el mensaje toast
onMounted(() => {
    console.log("filters en onmounted: ", selectedTags);
    if(selectedTags){
        selectedTags.value = (selectedTags.value || []).map(Number);
    }
    if (flashMessage && toast.value){
        toast.value.show();
    }
})

// Función para asignar colores a las categorías
function getCategoryColor(categoryId) {
    const colors = {
        1: 'bg-blue-100 text-blue-800',
        2: 'bg-green-100 text-green-800',
        3: 'bg-purple-100 text-purple-800',
        4: 'bg-yellow-100 text-yellow-800',
        5: 'bg-pink-100 text-pink-800',
        6: 'bg-indigo-100 text-indigo-800',
        7: 'bg-red-100 text-red-800',
        8: 'bg-orange-100 text-orange-800',
    };
    return colors[categoryId] || 'bg-gray-100 text-gray-800';
}

const confirmDialog = ref(null);
const productToDelete = ref(null);

function confirmarEliminacion(id) {
    productToDelete.value = id;
    confirmDialog.value.open();
}

function eliminarProducto() {
    if (productToDelete.value) {
        router.delete(`/products/${productToDelete.value}`);
        productToDelete.value = null;
    }
}

function cancelarEliminacion() {
    productToDelete.value = null;
}

// 🔑 Verificar si el usuario puede editar un producto
function canEdit(product){
    const user = page.props.auth?.user;
    if (!user) return false;
    return user.id === product.user_id || user.id_asdmin
}
// 🔑 Verificar si el usuario puede eliminar un producto
function canDelete(product){
    const user = page.props.auth?.user; // se obtiene del usuario desde la prop page por defecto
    if(!user) return false;
    return user.id === product.user_id || user.is_admin; // se verifica si el id del usuario logeado es igual al del producto registrado (user_id)
}
// 🔑 Verificar si el usuario puede crear productos
const canCreate = computed(() => { // el computed se usa para Se calcula una vez y se cachea. Solo se recalcula si page.props.auth?.user cambia. 
    return page.props.auth?.user !== null;
});
</script>