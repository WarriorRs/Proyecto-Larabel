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
      <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-2xl shadow-lg">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-800">🧾 Historial de Compras</h2>
          <button @click="goBack" class="bg-indigo-600 text-white px-4 py-2 rounded-full shadow hover:bg-indigo-700 transition">
            ⬅ Regresar
          </button>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Cargando compras...</div>
        <div v-else-if="purchases.length === 0" class="text-center text-gray-500">No tienes compras registradas.</div>
        <div v-else class="space-y-6">
          <div v-for="purchase in purchases" :key="purchase.id" class="border rounded-lg p-4 shadow hover:bg-gray-50 transition">
            <div class="flex justify-between items-center mb-2">
              <h3 class="text-lg font-semibold text-gray-800">
                Compra #{{ purchase.id }} - {{ formatDate(purchase.fecha) }}
              </h3>
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
                  <td class="p-2 text-center">
                    ${{ (Number(item.price) * Number(item.quantity)).toFixed(2) }}
                  </td>

                </tr>
              </tbody>
            </table>
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
const purchases = ref([])
const userName = ref('Usuario')
const loading = ref(true)

onMounted(async () => {
  const storedName = localStorage.getItem('name')
  userName.value = storedName || 'Usuario'

  try {
    const token = localStorage.getItem('token')
    const response = await api.get('/compras', {
      headers: { Authorization: `Bearer ${token}` }
    })
    console.log('Compras recibidas:', response.data) // <-- agrega esto
    purchases.value = response.data
  } catch (error) {
    console.error(error)
    alert('❌ Error al cargar las compras')
  } finally {
    loading.value = false
  }
})

const goBack = () => {
  router.back()
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

</script>