import { createRouter, createWebHistory } from 'vue-router'
import UserPanel from './components/UserPanel.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import transactions from './components/user/transactions.vue'




const routes = [
  {
    path: '/', 
    component: UserPanel,
    meta: { requiresAuth: true },
    children: [
      { path: 'transactions', component: transactions, name: 'transactions' }, 
    ]
  },
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
]


const router = createRouter({
  history: createWebHistory('/user-panel'), 
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


