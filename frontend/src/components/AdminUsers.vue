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
        <button @click="logout" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
          Cerrar sesión
        </button>
      </div>
    </nav>

    <!-- Lista de usuarios -->
    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <button
          @click="goTo('products')"
          class="mb-4 text-sm bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
          ← Volver a productos
        </button>
        <h2 class="text-2xl font-bold mb-4">Usuarios Registrados</h2>
        <div v-if="users.length === 0" class="text-center text-gray-500">No hay usuarios registrados.</div>
        <ul>
          <li
            v-for="user in users"
            :key="user.id"
            class="mb-4 p-4 border rounded flex flex-col md:flex-row md:items-center md:justify-between gap-4"
          >
            <div>
              <p class="text-lg font-semibold">{{ user.name }}</p>
              <p class="text-gray-600">{{ user.email }}</p>
              <p class="text-sm text-gray-500">Rol: {{ user.role ?? 'N/A' }}</p>
            </div>
            <div class="flex space-x-2 self-end md:self-auto">
              <button
                @click="editUser(user.id)"
                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
              >
                ✏️ Editar
              </button>
              <button
                @click="deleteUser(user.id)"
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

const users = ref([])
const router = useRouter()

const fetchUsers = async () => {
  try {
    const response = await api.get('/users')
    users.value = response.data
  } catch (error) {
    console.error('Error al cargar usuarios:', error)
    alert('Error al cargar usuarios')
  }
}

const deleteUser = async (id) => {
  if (!confirm('¿Deseas eliminar este usuario?')) return
  try {
    await api.delete(`/users/${id}`)
    users.value = users.value.filter(u => u.id !== id)
  } catch (error) {
    console.error('Error eliminando usuario:', error)
    alert('Error eliminando usuario')
  }
}

const editUser = (id) => {
  router.push(`/admin/edit-clients/${id}`)
}

const goTo = (section) => {
  const routes = {
    products: '/admin-product',
    sales: '/admin/ventas',
    clients: '/admin/clients',
    buys: '/admin/buys',
    providers: '/admin/providers'
  }
  router.push(routes[section] || '/admin-product')
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}

onMounted(fetchUsers)
</script>