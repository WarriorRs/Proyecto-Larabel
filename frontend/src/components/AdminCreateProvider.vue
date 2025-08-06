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
      <div class="p-6 md:p-8 w-full max-w-lg bg-white rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Proveedor</h2>

        <form @submit.prevent="createProvider" class="space-y-4">
          <input
            v-model="provider.name"
            type="text"
            placeholder="Nombre"
            required
            class="input"
          />
          <input
            v-model="provider.phone"
            type="text"
            placeholder="Teléfono"
            required
            class="input"
          />
          <input
            v-model="provider.address"
            type="text"
            placeholder="Dirección"
            required
            class="input"
          />

          <div class="flex justify-between space-x-2">
            <button
              type="button"
              @click="goBack"
              class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
            >
              ⬅️ Regresar
            </button>
            <button
              type="submit"
              class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors"
            >
              Guardar Proveedor
            </button>
          </div>
        </form>

        <div v-if="message" :class="messageClass" class="mt-4 text-center">
          {{ message }}
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const provider = reactive({
  name: '',
  phone: '',
  address: ''
})

const message = ref('')
const messageClass = ref('')

const createProvider = async () => {
  try {
    // URL completa del API backend
    const response = await axios.post('http://127.0.0.1:8080/api/providers', provider)
    message.value = 'Proveedor registrado exitosamente.'
    messageClass.value = 'text-green-600' // Mensaje de éxito
    provider.name = ''
    provider.phone = ''
    provider.address = ''
  } catch (error) {
    console.error(error)
    message.value = 'Error al registrar el proveedor.'
    messageClass.value = 'text-red-600' // Mensaje de error
  }
}

const goTo = (section) => {
  const routes = {
    products: '/admin-product',
    sales: '/admin/sales',
    clients: '/admin/clients',
    buys: '/admin/buys',
    providers: '/admin/providers'
  }
  if (routes[section]) {
    router.push(routes[section])
  }
}

const goBack = () => {
  router.back()
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 2px #6366f1;
}
</style>