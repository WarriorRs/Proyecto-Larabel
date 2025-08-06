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
        <button @click="goTo('products')" class="text-gray-700 hover:text-indigo-600">Productos</button>
        <button @click="goTo('sales')" class="text-gray-700 hover:text-indigo-600">Ventas</button>
        <button @click="goTo('clients')" class="text-gray-700 hover:text-indigo-600">Clientes</button>
        <button @click="goTo('buys')" class="text-gray-700 hover:text-indigo-600 transition-colors">Compras</button>
        <button @click="goTo('providers')" class="text-gray-700 hover:text-indigo-600 transition-colors">Proveedores</button>
      </div>

      <div class="flex items-center space-x-4">
        <span class="text-gray-700">👤 Admin</span>
        <button
          @click="logout"
          class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700"
        >
          Cerrar sesión
        </button>
      </div>
    </nav>

    <!-- Formulario de edición -->
    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="p-6 md:p-8 w-full max-w-lg bg-white rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">Editar Producto</h2>

        <form @submit.prevent="updateProduct" class="space-y-4" v-if="loaded">
          <input
            v-model="product.name"
            placeholder="Nombre"
            required
            class="border p-2 rounded w-full"
          />
          <input
            v-model.number="product.price"
            type="number"
            step="0.01"
            placeholder="Precio"
            required
            class="border p-2 rounded w-full"
          />
          <input
            v-model.number="product.stock"
            type="number"
            min="0"
            placeholder="Stock disponible"
            required
            class="border p-2 rounded w-full"
          />
          <textarea
            v-model="product.description"
            placeholder="Descripción"
            class="border p-2 rounded w-full"
          ></textarea>

          <!-- Mostrar imagen actual -->
          <div v-if="product.image" class="flex flex-col items-center space-y-2">
            <img
              :src="getProductImageUrl(product.image)"
              alt="Imagen actual"
              class="w-40 h-40 object-cover rounded shadow-md"
            />
            <button
              type="button"
              @click="deleteImage"
              class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
            >
              Eliminar Imagen
            </button>
          </div>

          <!-- Subir nueva imagen -->
          <input
            type="file"
            @change="handleImageChange"
            accept="image/*"
            class="border p-2 rounded w-full"
          />

          <!-- Botones de acción -->
          <div class="flex justify-between space-x-2">
            <button
              type="button"
              @click="goBack"
              class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
            >
              ⬅️ Regresar
            </button>
            <button
              type="submit"
              class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            >
              Guardar Cambios
            </button>
          </div>
        </form>

        <p v-else class="text-center text-gray-600">Cargando producto...</p>
      </div>
    </main>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../plugins/axios'

const router = useRouter()
const route = useRoute()

const product = ref({
  name: '',
  price: null,
  stock: null,
  description: '',
  image: '',
})

const selectedImage = ref(null)
const loaded = ref(false)

const fetchProduct = async () => {
  try {
    const { id } = route.params
    const response = await api.get(`/products/${id}`)
    product.value = {
      name: response.data.name,
      price: Number(response.data.price),
      stock: Number(response.data.stock),
      description: response.data.description || '',
      image: response.data.image || '',
    }
    loaded.value = true
  } catch (error) {
    console.error('Error cargando producto:', error.response?.data || error)
    alert('No se pudo cargar el producto.')
    router.push('/admin-product')
  }
}

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedImage.value = file
  }
}

const updateProduct = async () => {
  try {
    const { id } = route.params
    const token = localStorage.getItem('token')

    const formData = new FormData()
    formData.append('name', product.value.name)
    formData.append('price', product.value.price)
    formData.append('stock', product.value.stock)
    formData.append('description', product.value.description)
    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    await api.post(`/products/${id}?_method=PUT`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${token}`,
      },
    })

    alert('Producto actualizado correctamente')
    router.push('/admin-product')
  } catch (error) {
    console.error('Error actualizando producto:', error.response?.data || error)
    alert('Error al actualizar producto')
  }
}

const deleteImage = async () => {
  if (!confirm('¿Estás seguro de eliminar la imagen de este producto?')) {
    return
  }
  try {
    const { id } = route.params
    const token = localStorage.getItem('token')
    await api.delete(`/products/${id}/image`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    product.value.image = '' // Refresca localmente
    alert('Imagen eliminada correctamente')
  } catch (error) {
    console.error('Error eliminando imagen:', error.response?.data || error)
    alert('No se pudo eliminar la imagen.')
  }
}

const getProductImageUrl = (imageName) => {
  if (!imageName) {
    return 'https://via.placeholder.com/100x100.png?text=Sin+Imagen'
  }
  return `http://localhost:8080/storage/${imageName}`
}

const goBack = () => {
  router.push('/admin-product')
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

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}

onMounted(fetchProduct)
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}
</style>