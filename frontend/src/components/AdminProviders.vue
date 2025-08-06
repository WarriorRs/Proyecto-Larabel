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
        <span class="text-gray-700">👤 Admin</span>
        <button @click="logout" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Cerrar sesión</button>
      </div>
    </nav>

    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">Proveedores Registrados</h2>

        <div class="flex justify-end mb-4">
          <button @click="goToCreate" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            ➕ Crear Proveedor
          </button>
        </div>

        <div v-if="providers.length === 0" class="text-center text-gray-500">
          No hay proveedores registrados.
        </div>

        <ul>
          <li
            v-for="provider in providers"
            :key="provider.id"
            class="mb-4 p-4 border rounded flex flex-col md:flex-row md:items-center md:justify-between gap-4"
          >
            <div>
              <strong class="text-lg">{{ provider.name }}</strong>
              <p class="text-gray-600">{{ provider.phone }}</p>
              <p class="text-sm text-gray-500">Teléfono: {{ provider.address }}</p>
            </div>
            <div class="flex space-x-2 self-end md:self-auto">
              <button
                @click="editProvider(provider.id)"
                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
              >
                ✏️ Editar
              </button>
              <button
                @click="deleteProvider(provider.id)"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
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

const router = useRouter()
const providers = ref([])

const fetchProviders = async () => {
  try {
    const res = await api.get('/providers')
    providers.value = res.data
  } catch (err) {
    console.error('Error cargando proveedores:', err)
    alert('Error al cargar los proveedores')
  }
}

const deleteProvider = async (id) => {
  if (!confirm('¿Deseas eliminar este proveedor?')) return
  try {
    await api.delete(`/providers/${id}`)
    providers.value = providers.value.filter(p => p.id !== id)
  } catch (err) {
    console.error('Error al eliminar proveedor:', err)
    alert('No se pudo eliminar el proveedor')
  }
}

const goToCreate = () => {
  router.push('/admin/create-provider')
}

const editProvider = (id) => {
  router.push(`/admin/edit-provider/${id}`)
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
  }
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}

onMounted(fetchProviders)
</script>