<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex flex-col">
    <!-- Barra superior -->
    <nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="/logo_botella.jpg" alt="Logo" class="w-11 h-11 object-contain rounded" />
        <span class="text-lg font-semibold text-gray-800">Sistema de Administración</span>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-gray-700">👤 Admin</span>
        <button @click="goBack" class="bg-gray-300 text-gray-800 px-3 py-2 rounded hover:bg-gray-400">
          ← Volver
        </button>
      </div>
    </nav>

    <!-- Formulario de edición -->
    <main class="flex-grow flex justify-center items-center px-4 py-8">
      <div class="w-full max-w-2xl bg-white p-6 md:p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Editar Usuario</h2>

        <form @submit.prevent="updateUser" class="space-y-4">
          <div>
            <label class="block text-gray-700 mb-1">Nombre</label>
            <input
              v-model="user.name"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
              required
            />
          </div>

          <div>
            <label class="block text-gray-700 mb-1">Correo</label>
            <input
              v-model="user.email"
              type="email"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
              required
            />
          </div>

          <div>
            <label class="block text-gray-700 mb-1">Rol</label>
            <input
              v-model="user.role"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
              placeholder="Ej: admin, cliente"
            />
          </div>

          <div class="flex justify-end space-x-2 pt-4">
            <button
              type="button"
              @click="goBack"
              class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            >
              Guardar
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../plugins/axios'

const route = useRoute()
const router = useRouter()
const user = ref({ name: '', email: '', role: '' })

const fetchUser = async () => {
  try {
    const response = await api.get(`/users/${route.params.id}`)
    user.value = response.data
  } catch (error) {
    alert('Error al cargar datos del usuario')
    console.error(error)
  }
}

const updateUser = async () => {
  try {
    await api.put(`/users/${route.params.id}`, user.value)
    alert('Usuario actualizado')
    router.push('/admin/clients')
  } catch (error) {
    alert('Error al actualizar usuario')
    console.error(error)
  }
}

const goBack = () => router.push('/admin/clients')

onMounted(fetchUser)
</script>