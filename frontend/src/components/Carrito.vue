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
          <h2 class="text-2xl font-bold text-gray-800">🛒 Tu carrito</h2>
          <button @click="goBack" class="bg-indigo-600 text-white px-4 py-2 rounded-full shadow hover:bg-indigo-700 transition"
          >
          ⬅ Regresar
          </button>
        </div>
        <div v-if="cart.length === 0" class="text-center text-gray-500">El carrito está vacío.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full border rounded-lg overflow-hidden">
            <thead>
              <tr class="bg-gray-100 text-gray-700">
                <th class="p-3 text-left">Producto</th>
                <th class="p-3">Cantidad</th>
                <th class="p-3">Precio</th>
                <th class="p-3">Subtotal</th>
                <th class="p-3">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cart" :key="item.id" class="border-t hover:bg-gray-50">
                <td class="p-3">{{ item.name }}</td>
                <td class="p-3 text-center">{{ item.quantity }}</td>
                <td class="p-3 text-center">${{ item.price }}</td>
                <td class="p-3 text-center">${{ (item.price * item.quantity).toFixed(2) }}</td>
                <td class="p-3 text-center">
                  <button @click="removeFromCart(item.id)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">🗑</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="text-right mt-6">
            <p class="text-xl font-semibold text-gray-800">Total: ${{ total.toFixed(2) }}</p>
            <button @click="checkout" class="mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Finalizar Compra</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../plugins/axios'

const router = useRouter()
const cart = ref([])
const userName = ref('Usuario')

onMounted(() => {
  const storedCart = JSON.parse(localStorage.getItem('cart')) || []
  cart.value = storedCart

  const storedName = localStorage.getItem('name')
  userName.value = storedName || 'Usuario'
})

const goBack = () => {
  router.back()
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  localStorage.removeItem('name')
  localStorage.removeItem('cart')
  cart.value = [] 
  router.push('/login')
}

const removeFromCart = (productId) => {
  cart.value = cart.value.filter(item => item.id !== productId)
  localStorage.setItem('cart', JSON.stringify(cart.value))
}

const total = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
})

const checkout = async () => {
  try {
    const token = localStorage.getItem('token');

    const payload = {
      items: cart.value.map(item => ({
        id: item.id,
        name: item.name,
        quantity: item.quantity,
        price: item.price
      })),
      total: total.value
    };

    await api.post('/checkout', payload, {
      headers: { Authorization: `Bearer ${token}` }
    });

    alert('✅ Compra realizada con éxito');
    cart.value = [];
    localStorage.removeItem('cart');
    router.push('/'); // O donde desees redirigir tras compra

  } catch (error) {
    console.error(error);
    alert('❌ Error al procesar la compra');
  }
};
</script>
