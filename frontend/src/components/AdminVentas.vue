<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex flex-col">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="/logo_botella.jpg" alt="Logo" class="w-11 h-11 object-contain rounded" />
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
        <span class="text-gray-700">👤 {{ userName }}</span>
        <button
          @click="logout"
          class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 transition-colors"
        >
          Cerrar sesión
        </button>
      </div>
    </nav>

    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="w-full max-w-6xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">🧾 Historial de Compras Registradas</h2>

        <div v-if="loading" class="text-center text-gray-500">Cargando compras...</div>
        <div v-else-if="purchases.length === 0" class="text-center text-gray-500">No hay compras registradas.</div>

        <ul v-else class="space-y-4">
          <li
            v-for="purchase in purchases"
            :key="purchase.id"
            class="border rounded-lg p-4 shadow hover:bg-gray-50 transition"
          >
            <div class="flex justify-between items-center mb-2">
              <div>
                <h3 class="text-lg font-semibold text-gray-800">
                  Compra #{{ purchase.id }} - {{ formatDate(purchase.fecha) }}
                </h3>
                <p class="text-sm text-gray-600">Comprador: {{ purchase.user_name || 'Desconocido' }}</p>
              </div>
              <span class="text-green-700 font-semibold">
                Total: ${{ Number(purchase.total).toFixed(2) }}
              </span>
            </div>
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-gray-100 text-gray-700">
                  <th class="p-2 text-left">Producto</th>
                  <th class="p-2 text-center">Cantidad</th>
                  <th class="p-2 text-center">Precio</th>
                  <th class="p-2 text-center">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in purchase.items" :key="item.id" class="border-t">
                  <td class="p-2">{{ item.name }}</td>
                  <td class="p-2 text-center">{{ item.quantity }}</td>
                  <td class="p-2 text-center">${{ Number(item.price).toFixed(2) }}</td>
                  <td class="p-2 text-center">${{ (Number(item.price) * Number(item.quantity)).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
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

const router = useRouter()
const purchases = ref([])
const userName = ref('Admin')
const loading = ref(true)

const fetchPurchases = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await api.get('/admin/ventas', {
      headers: { Authorization: `Bearer ${token}` }
    })
    purchases.value = response.data
  } catch (error) {
    console.error(error)
    alert('❌ Error al cargar las compras')
  } finally {
    loading.value = false
  }
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
  localStorage.clear()
  router.push('/login')
}

const formatDate = (dateStr, showTime = false) => {
  if (!dateStr) return 'Fecha desconocida'
  const date = new Date(dateStr)
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  if (showTime) {
    options.hour = '2-digit'
    options.minute = '2-digit'
  }
  return date.toLocaleDateString('es-ES', options)
}

onMounted(() => {
  userName.value = 'Admin'
  fetchPurchases()
})

</script>