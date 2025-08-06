import { createRouter, createWebHistory } from 'vue-router'

import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import UsuarioProduct from '../components/UsuarioProduct.vue'
import AdminProduct from '../components/AdminProduct.vue'

const routes = [
  { path: '/', redirect: '/login' },

  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },

  {
    path: '/usuario-product',
    name: 'UsuarioProduct',
    component: UsuarioProduct,
    meta: { requiresAuth: true, role: 'user' },
  },

  {
    path: '/admin-product',
    name: 'AdminProduct',
    component: AdminProduct,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/clients',
    name: 'AdminClients',
    component: () => import('../components/AdminUsers.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/buys',
    name: 'AdminPurchase',
    component: () => import('../components/AdminPurchase.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/providers',
    name: 'AdminProviders',
    component: () => import('../components/AdminProviders.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  ,
  {
    path: '/admin/create-provider',
    name: 'AdminCreateProvider',
    component: () => import('../components/AdminCreateProvider.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/edit-clients/:id',
    name: 'EditClients',
    component: () => import('../components/AdminEditUser.vue'),
    props: true,
  },
  // Gestión de Productos Administrador
  {
    path: '/admin/create-product',
    name: 'CreateProduct',
    component: () => import('../components/AdminProductCreate.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/edit-product/:id',
    name: 'EditProduct',
    component: () => import('../components/AdminProductEdit.vue'),
    props: true,
    meta: { requiresAuth: true, role: 'admin' },
  },
  ,
  {
    path: '/admin/ventas',
    name: 'VentasAdmin',
    component: () => import('../components/AdminVentas.vue'),
    props: true,
    meta: { requiresAuth: true, role: 'admin' },
  },
  ,
  {
    path: '/usuario/carrito',
    name: 'Carrito',
    component: () => import('../components/Carrito.vue'),
    props: true,
    meta: { requiresAuth: true, role: 'user' },
  },
  {
    path: '/usuario/compras',
    name: 'Compras',
    component: () => import('../components/ComprasUsuario.vue'),
    props: true,
    meta: { requiresAuth: true, role: 'user' },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

/**
 * Protección de rutas según autenticación y rol
 * - Redirige a /login si no hay token.
 * - Redirige a /login si el rol del usuario no coincide con el requerido.
 */
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('token')
    const role = localStorage.getItem('role')

    if (!token) {
      next('/login')
    } else if (to.meta.role && to.meta.role !== role) {
      next('/login')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router