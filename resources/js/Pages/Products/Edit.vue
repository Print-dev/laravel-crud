<!-- <script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
});

const name = ref(props.product.name);
const description = ref(props.product.description);
const price = ref(props.product.price);
const stock = ref(props.product.stock);

function actualizarProducto() {
    router.put(`/products/${props.product.id}`, {
        name: name.value,
        description: description.value,
        price: price.value,
        stock: stock.value,
    });
}
</script>

<template>
    <h1>Editar producto</h1>
    <p>Producto: {{ product.name }}</p>
    <p>Precio: {{ product.price }}</p>
    <form @submit.prevent="actualizarProducto">
        <input type="text" v-model="name">

        <input type="text" v-model="description">

        <input type="number" v-model="price">

        <input type="number" v-model="stock">

        <button type="submit">
            Actualizar producto
        </button>
    </form>
</template> -->
<script setup>
import ProductForm from '@/Components/ProductForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { App } from '@inertiajs/vue3';
const props= defineProps({
    product: Object,
    categories: Array,
    tags: Array,
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Editar producto</h1>
            <ProductForm :product="props.product" :categories="categories" :tags="tags" />
        </div>
    </AppLayout>
</template>