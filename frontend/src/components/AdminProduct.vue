<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex flex-col">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img
          src="/logo_botella.jpg"
          alt="Logo"
          class="w-11 h-11 object-contain rounded"
        />
        <span class="text-lg font-semibold text-gray-800">Sistema de Administración</span>
      </div>

      <div class="flex items-center space-x-6">
        <button @click="goTo('products')" class="text-gray-700 hover:text-indigo-600 transition-colors">Productos</button>
        <button @click="goTo('sales')" class="text-gray-700 hover:text-indigo-600 transition-colors">Ventas</button>
        <button @click="goTo('clients')" class="text-gray-700 hover:text-indigo-600 transition-colors">Clientes</button>
        <button @click="goTo('buys')" class="text-gray-700 hover:text-indigo-600 transition-colors">Compras</button>
        <button @click="goTo('providers')" class="text-gray-700 hover:text-indigo-600 transition-colors">Proveedores</button>
      </div>

      <div class="flex items-center space-x-4">
        <span class="text-gray-700">👤 Admin</span>
        <button
          @click="logout"
          class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition-colors"
        >
          Cerrar sesión
        </button>
      </div>
    </nav>

    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">Productos Registrados</h2>

        <div class="flex justify-end mb-4">
          <button
            @click="goToCreate"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors"
          >
            ➕ Crear Producto
          </button>
        </div>

        <div v-if="products.length === 0" class="text-center text-gray-500">
          No hay productos registrados.
        </div>

        <ul>
          <li
            v-for="product in products"
            :key="product.id"
            class="mb-4 p-4 border rounded flex flex-col md:flex-row md:items-center md:justify-between gap-4"
          >
            <div class="flex items-center gap-4">
              <img
                :src="getProductImageUrl(product.image)"
                alt="Imagen producto"
                class="w-20 h-20 max-h-24 aspect-square object-contain bg-white p-1 rounded-md border border-gray-300"
              />
              <div>
                <strong class="text-lg">{{ product.name }}</strong> - ${{ product.price.toFixed(2) }}
                <p class="text-gray-600">{{ product.description }}</p>
                <p class="text-sm text-gray-500">Stock disponible: {{ product.stock ?? 'No registrado' }}</p>
              </div>
            </div>
            <div class="flex space-x-2 self-end md:self-auto">
              <button
                @click="editProduct(product.id)"
                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition-colors"
              >
                ✏️ Editar
              </button>
              <button
                @click="deleteProduct(product.id)"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition-colors"
              >
                🗑️ Eliminar
              </button>
            </div>
          </li>
        </ul>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios'

const products = ref([])
const router = useRouter()

const fetchProducts = async () => {
  try {
    const response = await api.get('/products')
    if (Array.isArray(response.data)) {
      products.value = response.data.map(p => ({
        ...p,
        price: Number(p.price),
        stock: p.stock ?? 0
      }))
    } else if (response.data.data && Array.isArray(response.data.data)) {
      products.value = response.data.data.map(p => ({
        ...p,
        price: Number(p.price),
        stock: p.stock ?? 0
      }))
    } else {
      console.error('Formato inesperado de respuesta:', response.data)
      products.value = []
    }
  } catch (error) {
    console.error('Error al obtener productos:', error)
    alert('Error al cargar productos')
  }
}

const deleteProduct = async (id) => {
  if (!confirm('¿Deseas eliminar este producto?')) return
  try {
    await api.delete(`/products/${id}`)
    products.value = products.value.filter(p => p.id !== id)
  } catch (error) {
    console.error('Error eliminando producto:', error)
    alert('Error eliminando producto')
  }
}

const goToCreate = () => {
  router.push('/admin/create-product')
}

const editProduct = (id) => {
  router.push(`/admin/edit-product/${id}`)
}

const goTo = (section) => {
  const routes = {
    products: '/admin-product',
    sales: '/admin/ventas',
    clients: '/admin/clients',
    buys: '/admin/buys',
    providers: '/admin/providers'
  }
  if (routes[section]) {
    router.push(routes[section])
  } else {
    console.warn(`Sección '${section}' no configurada aún.`)
  }
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}

const getProductImageUrl = (imageName) => {
  if (!imageName) {
    return 'https://via.placeholder.com/100x100.png?text=Sin+Imagen'
  }
  return `http://localhost:8080/storage/${imageName}`
}

onMounted(fetchProducts)
</script>