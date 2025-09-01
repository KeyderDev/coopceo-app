import { createApp } from 'vue'
import AdminApp from './components/AdminPanel.vue'
import router from './admin-router'

createApp(AdminApp).use(router).mount('#admin-app')
