<template>
  <div class="min-h-screen w-full bg-gradient-to-br from-indigo-200 via-white to-sky-200 flex justify-center items-center px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-2xl">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h1>
      <form @submit.prevent="login" class="space-y-5">
        <!-- Email -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Ingresa tu correo"
            required
          />
        </div>
        <!-- Contraseña -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Contraseña</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Ingresa tu contraseña"
            required
          />
        </div>
        <!-- Botón -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200"
        >
          Entrar
        </button>
      </form>

      <!-- Mensaje de error -->
      <p v-if="errorMessage" class="text-red-500 text-sm mt-4 text-center">{{ errorMessage }}</p>

      <!-- Mensaje de éxito -->
      <div
        v-if="successMessage"
        class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg animate-bounce"
      >
        {{ successMessage }}
      </div>

      <!-- Enlace para registrar -->
      <div class="mt-6 text-center">
        <p class="text-gray-600">
          ¿No tienes cuenta?
          <RouterLink to="/register" class="text-blue-600 font-semibold hover:underline">
            Regístrate
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../axios'

const router = useRouter()
const form = reactive({
  email: '',
  password: '',
})

const errorMessage = ref('')
const successMessage = ref('')

const login = async () => {
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const response = await api.post('/login', form)
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('name', response.data.user.name)
    localStorage.setItem('role', response.data.role)

    successMessage.value = '¡Inicio de sesión exitoso!'
    setTimeout(() => (successMessage.value = ''), 3000)

    if (response.data.role === 'admin') {
      router.push('/admin-product')
    } else {
      router.push('/usuario-product')
    }
  } catch (error) {
    errorMessage.value = 'Credenciales inválidas o error en el servidor.'
  }
}
</script>