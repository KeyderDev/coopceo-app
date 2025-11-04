import { createRouter, createWebHistory } from 'vue-router'
import AdminPanel from './components/AdminPanel.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import Users from './components/admin/users.vue'
import Logs from './components/admin/logs.vue'
import terminal from './components/admin/terminal.vue'
import Productos from './components/admin/productos.vue'
import cuadre from './components/admin/cuadre.vue'
import cuadres from './components/admin/cuadres.vue'
import posTransactions from './components/admin/pos-transactions.vue'
import email from './components/admin/email.vue'
import Calendar from './components/admin/calendar.vue'
import horarios from './components/admin/horarios.vue'
import ganancias from './components/admin/ganancias.vue'
import registroCompras from './components/admin/registro-compras.vue'
import documentos from './components/admin/documentos.vue'
import inventario from './components/admin/inventario.vue'
import ForgotPassword from './components/ForgotPassword.vue'

const routes = [
  {
    path: '/', 
    component: AdminPanel,
    meta: { requiresAuth: true },
    children: [
      { path: 'users', component: Users, name: 'users' }, 
      { path: 'logs', component: Logs, name: 'logs' }, 
      { path: 'terminal', component: terminal, name: 'terminal' },
      { path: 'productos', component: Productos, name: 'productos' },
      { path: 'cuadre', component: cuadre, name: 'reconciliation' },
      { path: 'cuadres', component: cuadres, name: 'cuadres' },
      { path: 'pos-transactions', component: posTransactions, name: 'pos-transactions' },
      { path: 'email', component: email, name: 'email' },
      { path: 'calendar', component: Calendar, name: 'calendar' },
      { path: 'horarios', component: horarios, name: 'horarios' },
      { path: 'ganancias', component: ganancias, name: 'ganancias' },
      { path: 'registro-compras', component: registroCompras, name: 'registro-compras' },
      { path: 'documentos', component: documentos, name: 'documentos' },
      { path: 'inventario', component: inventario, name: 'inventario' },
    ]
  },
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
  { path: '/forgot-password', component: ForgotPassword, name: 'forgot-password' },

]

const router = createRouter({
  history: createWebHistory('/admin-panel'), 
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
  } else if (to.name === 'login' && token) {
    next('/')
  } else {
    next()
  }
})

export default router
