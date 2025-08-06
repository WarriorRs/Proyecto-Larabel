<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex flex-col">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img
          src="/logo_botella.jpg"
          alt="Icono"
          class="w-11 h-11 object-contain rounded"
        />
        <span class="text-lg font-semibold text-gray-800">Sistema de Productos</span>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-gray-700">👤 {{ userName }}</span>
        <button
          @click="logout"
          class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition-colors"
        >
          Cerrar sesión
        </button>
      </div>
    </nav>

    <main class="flex-grow flex flex-col items-center justify-center px-4 py-8">
      <div class="w-full max-w-6xl bg-white p-6 md:p-8 rounded-2xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Productos disponibles</h2>
          <div class="flex space-x-2">
            <button @click="goToCart" class="relative flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="7" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
              </svg>
              <span>Carrito</span>
              <span v-if="cartCount > 0"
                    class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-semibold rounded-full w-5 h-5 flex items-center justify-center">
                {{ cartCount }}
              </span>
            </button>

            <button @click="goToPurchases" class="flex items-center space-x-2 bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <span>Ver compras</span>
            </button>
          </div>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Cargando productos...</div>

        <div v-else>
          <div v-if="products.length === 0" class="text-center text-gray-500">No hay productos disponibles.</div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="product in products"
              :key="product.id"
              class="bg-gray-50 p-4 rounded-xl shadow hover:shadow-lg transition relative flex flex-col"
            >
              <img
                :src="getProductImageUrl(product.image)"
                alt="Imagen producto"
                class="w-full h-48 object-cover rounded-lg mb-4 border"
              />
              <h3 class="text-lg font-bold mb-1 text-gray-800">{{ product.name }}</h3>
              <p class="text-gray-600 text-sm flex-grow">{{ product.description }}</p>
              <p class="mt-1 text-sm text-gray-500">Stock: {{ product.stock ?? 'No registrado' }}</p>
              <p class="mt-1 font-semibold text-blue-600 text-lg">${{ product.price }}</p>

              <button
                @click="addToCart(product)"
                class="absolute top-2 right-2 bg-green-600 text-white rounded-full p-2 hover:bg-green-700 transition"
                title="Agregar al carrito">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios'

const router = useRouter()
const userName = ref('Usuario')
const products = ref([])
const loading = ref(true)
const cartCount = ref(0)

onMounted(() => {
  const storedName = localStorage.getItem('name')
  userName.value = storedName || 'Usuario'
  fetchProducts()
  updateCartCount()
})

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  localStorage.removeItem('name')
  router.push('/login')
}

const goToCart = () => {
  router.push('/usuario/carrito')
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await api.get('/products', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    products.value = response.data
  } catch (error) {
    console.error('Error cargando productos:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

const getProductImageUrl = (imageName) => {
  if (!imageName) {
    return 'https://via.placeholder.com/100x100.png?text=Sin+Imagen'
  }
  return `http://localhost:8080/storage/${imageName}`
}

const addToCart = (product) => {
  let cart = JSON.parse(localStorage.getItem('cart')) || []
  const existing = cart.find(p => p.id === product.id)
  if (existing) {
    existing.quantity += 1
  } else {
    cart.push({ ...product, quantity: 1 })
  }
  localStorage.setItem('cart', JSON.stringify(cart))
  updateCartCount()
  alert(`✅ ${product.name} agregado al carrito`)
}
const updateCartCount = () => {
  const cart = JSON.parse(localStorage.getItem('cart')) || []
  cartCount.value = cart.reduce((total, item) => total + item.quantity, 0)
}

const goToPurchases = () => {
  router.push('/usuario/compras');
};
</script>