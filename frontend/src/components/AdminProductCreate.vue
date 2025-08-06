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
        <button @click="goTo('providers')" class="text-gray-700 hover:text-indigo-600">Proveedores</button>
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
        <h2 class="text-2xl font-bold mb-4">Crear Producto</h2>

        <form @submit.prevent="createProduct" class="space-y-4">
          <input v-model="newProduct.name" placeholder="Nombre" required class="input" />
          <input v-model.number="newProduct.price" placeholder="Precio" type="number" step="0.01" required class="input" />
          <input v-model.number="newProduct.stock" placeholder="Stock disponible" type="number" min="0" required class="input" />
          <textarea v-model="newProduct.description" placeholder="Descripción" class="input"></textarea>
          <input type="file" @change="handleImageChange" accept="image/*" />
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
              Crear Producto
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios'

const router = useRouter()

const newProduct = reactive({
  name: '',
  price: null,
  stock: null,
  description: '',
})

const selectedImage = ref(null)

const handleImageChange = (event) => {
  selectedImage.value = event.target.files[0]
}

const createProduct = async () => {
  try {
    const token = localStorage.getItem('token')
    const formData = new FormData()

    formData.append('name', newProduct.name)
    formData.append('description', newProduct.description)
    formData.append('price', newProduct.price)
    formData.append('stock', newProduct.stock)

    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    await api.post('/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${token}`,
      },
    })

    alert('Producto creado correctamente')
    router.push('/admin-product')
  } catch (error) {
    console.error('Error creando producto:', error.response?.data || error)
    alert('Error creando producto')
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
  } else {
    console.warn(`Sección '${section}' no configurada aún.`)
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
}
</style>