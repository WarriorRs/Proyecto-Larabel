<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex flex-col">
    <!-- Navbar -->
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

    <main class="flex-grow flex justify-center items-start px-4 py-8">
      <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-6">{{ isEditing ? 'Editar Compra' : 'Registrar Compra' }}</h2>

        <form @submit.prevent="addPurchase" class="space-y-4 mb-8">
          <div>
            <label class="block mb-1 text-gray-700">Proveedor</label>
            <select v-model="form.provider_id" class="w-full p-2 border rounded">
              <option disabled value="">Seleccione un proveedor</option>
              <option v-for="provider in providers" :key="provider.id" :value="provider.id">
                {{ provider.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block mb-1 text-gray-700">Producto</label>
            <select v-model="form.product_id" class="w-full p-2 border rounded">
              <option disabled value="">Seleccione un producto</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block mb-1 text-gray-700">Cantidad</label>
              <input type="number" v-model="form.quantity" class="w-full p-2 border rounded" />
            </div>
            <div>
              <label class="block mb-1 text-gray-700">Precio Unitario</label>
              <input type="number" v-model="form.unit_price" class="w-full p-2 border rounded" />
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
              {{ isEditing ? '💾 Guardar Cambios' : '➕ Registrar Compra' }}
            </button>
            <button v-if="isEditing" @click="cancelEdit" type="button" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded">
              ❌ Cancelar
            </button>
          </div>
        </form>

        <h3 class="text-xl font-semibold mb-4">Historial de Compras</h3>
        <table class="w-full text-left border border-gray-300 rounded overflow-hidden">
          <thead class="bg-indigo-100">
            <tr>
              <th class="p-2">Proveedor</th>
              <th class="p-2">Producto</th>
              <th class="p-2">Cantidad</th>
              <th class="p-2">Precio Unitario</th>
              <th class="p-2">Total</th>
              <th class="p-2">Fecha</th>
              <th class="p-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="purchase in purchases" :key="purchase.id" class="border-t">
              <td class="p-2">{{ purchase.provider_name }}</td>
              <td class="p-2">{{ purchase.product_name }}</td>
              <td class="p-2">{{ purchase.quantity }}</td>
              <td class="p-2">${{ purchase.unit_price }}</td>
              <td class="p-2">${{ (purchase.quantity * purchase.unit_price).toFixed(2) }}</td>
              <td class="p-2">{{ formatDate(purchase.created_at) }}</td>
              <td class="p-2 space-x-2">
                <button @click="editPurchase(purchase)" class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500">Editar</button>
                <button @click="deletePurchase(purchase.id)" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Eliminar</button>
              </td>
            </tr>
            <tr v-if="purchases.length === 0">
              <td colspan="7" class="text-center p-4 text-gray-500">No hay compras registradas.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios'

const router = useRouter()

const form = ref({
  provider_id: '',
  product_id: '',
  quantity: '',
  unit_price: ''
})

const isEditing = ref(false)
const editingId = ref(null)

const providers = ref([])
const products = ref([])
const purchases = ref([])

const addPurchase = async () => {
  try {
    if (isEditing.value) {
      // Editar compra existente
      await api.put(`/purchases/${editingId.value}`, form.value)

      const providerName = providers.value.find(p => p.id === form.value.provider_id)?.name || ''
      const productName = products.value.find(p => p.id === form.value.product_id)?.name || ''

      const index = purchases.value.findIndex(p => p.id === editingId.value)
      if (index !== -1) {
        purchases.value[index] = {
          id: editingId.value,
          provider_id: form.value.provider_id,
          product_id: form.value.product_id,
          quantity: form.value.quantity,
          unit_price: form.value.unit_price,
          provider_name: providerName,
          product_name: productName,
          created_at: purchases.value[index].created_at
        }
      }

      isEditing.value = false
      editingId.value = null
    } else {
      // Crear nueva compra
      const response = await api.post('/purchases', form.value)

      const providerName = providers.value.find(p => p.id === form.value.provider_id)?.name || ''
      const productName = products.value.find(p => p.id === form.value.product_id)?.name || ''

      const newPurchase = {
        ...form.value,
        id: response.data.id, // Usa el ID real del backend
        provider_name: providerName,
        product_name: productName,
        created_at: new Date().toISOString()
      }

      purchases.value.push(newPurchase)
    }

    // Limpiar formulario
    form.value = { provider_id: '', product_id: '', quantity: '', unit_price: '' }
  } catch (err) {
    alert('Error al guardar la compra')
  }
}

const editPurchase = (purchase) => {
  isEditing.value = true
  editingId.value = purchase.id
  form.value = {
    provider_id: purchase.provider_id,
    product_id: purchase.product_id,
    quantity: purchase.quantity,
    unit_price: purchase.unit_price
  }
}

const cancelEdit = () => {
  isEditing.value = false
  editingId.value = null
  form.value = { provider_id: '', product_id: '', quantity: '', unit_price: '' }
}

const deletePurchase = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar esta compra?')) return

  try {
    await api.delete(`/purchases/${id}`)
    purchases.value = purchases.value.filter(p => p.id !== id)
  } catch (err) {
    alert('Error al eliminar la compra')
  }
}

const fetchProviders = async () => {
  const response = await api.get('/providers')
  providers.value = response.data
}

const fetchProducts = async () => {
  const response = await api.get('/products')
  products.value = response.data
}

const fetchPurchases = async () => {
  const response = await api.get('/purchases')
  purchases.value = response.data
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleString('es-ES')
}

const goTo = (section) => {
  const routes = {
    products: '/admin-product',
    sales: '/admin/ventas',
    clients: '/admin/clients',
    buys: '/admin/buys',
    providers: '/admin/providers'
  }
  router.push(routes[section])
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  router.push('/login')
}

onMounted(() => {
  fetchProviders()
  fetchProducts()
  fetchPurchases()
})
</script>