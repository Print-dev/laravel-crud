<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
const page = usePage();
// el page obtiene los datos de la página actual, incluyendo las propiedades de autenticación del usuario. Esto nos permite acceder a la información del usuario logueado y sus permisos.
/* ejemplo de page-.props.auth.user: 

{
    "auth": {
        "user": {
            "id": 1,
            "name": "Juan",
            "is_admin": true
        }
    }
}

*/
// computed sirve para crear propiedades reactivas que se actualizan automáticamente cuando cambian sus dependencias. En este caso, estamos creando tres propiedades computadas: isAdmin, isLoggedIn y userName. Estas propiedades nos permiten determinar si el usuario actual es un administrador, si está logueado y obtener su nombre de usuario, respectivamente.
console.log("page ->", page.props.auth);

// compùted: Dime cuál es el valor actual de estas propiedades basándote en el usuario actual. 
const isAdmin = computed(() => page.props.auth?.user?.is_admin == true);
const isLoggedIn = computed(() => page.props.auth?.user !== null);
const userName = computed(() => page.props.auth?.user?.name || 'Invitado');

console.log(route('products.index'));
console.log(route('products.edit', 1));
console.log(route('admin.dashboard'));

function cerrarSesion() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Encabezado -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">
                    <Link :href="route('products.index')" class="hover:text-blue-600">
                        Mi Tienda
                    </Link>
                </h1>
                
                <nav class="flex gap-4">
                    <Link 
                        :href="route('products.index')"
                        class="text-gray-600 hover:text-blue-600 transition"
                        :class="{ 'text-blue-600 font-semibold': $page.url === '/products' }"
                    >
                        Productos
                    </Link>
                    
                    <!-- Aquí agregaremos más enlaces en el futuro -->
                     <!-- 🔒 Admin con computed -->
                    <Link 
                        v-if="isAdmin"
                        :href="route('admin.dashboard')"
                        class="text-gray-600 hover:text-blue-600 transition"
                        :class="{ 'text-blue-600 font-semibold': $page.url.startsWith('/admin') }"
                    >
                        Admin
                    </Link>
                    <!-- Usuario logueado -->
                    <div v-if="isLoggedIn" class="flex items-center gap-3 ml-4 pl-4 border-l">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ userName }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ page.props.auth.user.email }}
                            </p>
                        </div>
                        
                        <button 
                            @click="cerrarSesion"
                            class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"
                        >
                            Cerrar sesión
                        </button>
                    </div>
                    
                    <!-- Usuario NO logueado -->
                    <div v-else class="flex gap-2 ml-4 pl-4 border-l">
                        <Link :href="route('login')" class="text-sm text-blue-500 hover:text-blue-700">
                            Iniciar sesión
                        </Link>
                        <Link :href="route('register')" class="text-sm text-blue-500 hover:text-blue-700">
                            Registrarse
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="max-w-7xl mx-auto px-4 py-6">
            <slot />
        </main>

        <!-- Pie de página -->
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-sm">
                &copy; 2026 Mi Tienda - Todos los derechos reservados
            </div>
        </footer>
    </div>
</template>