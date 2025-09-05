import { createRouter, createWebHistory } from 'vue-router'
import AdminPanel from './components/AdminPanel.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import Users from './components/admin/users.vue'
import Logs from './components/admin/logs.vue'

const routes = [
  {
    path: '/', 
    component: AdminPanel,
    meta: { requiresAuth: true },
    children: [
      { path: 'users', component: Users, name: 'users' }, // hijos
      { path: 'logs', component: Logs, name: 'logs' }, // hijos
    ]
  },
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
]

const router = createRouter({
  history: createWebHistory('/admin-panel'), // base = /admin-panel
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
