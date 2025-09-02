import { createApp } from 'vue'
import AdminApp from './components/AdminPanel.vue'
import router from './admin-router'
import '@fortawesome/fontawesome-free/css/all.min.css';


createApp(AdminApp).use(router).mount('#admin-app')
