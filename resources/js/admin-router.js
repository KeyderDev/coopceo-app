import { createRouter, createWebHistory } from 'vue-router';
import Admin from './components/AdminPanel.vue';
import Login from './components/Login.vue'


const routes = [
  { path: '/', component: Admin, meta: { requiresAuth: true } },
  { path: '/login', component: Login, name: 'login' },
]

const router = createRouter({
  history: createWebHistory('/admin-panel/'), 
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

export default router;


