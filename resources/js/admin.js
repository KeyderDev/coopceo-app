import { createApp } from 'vue'
import App from './components/App.vue'
import router from './admin-router'
import '@fortawesome/fontawesome-free/css/all.min.css';


createApp(App).use(router).mount('#admin-app')
