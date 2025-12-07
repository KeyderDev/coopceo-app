import axios from "axios"

axios.defaults.baseURL = import.meta.env.VITE_APP_URL

axios.interceptors.request.use((config) => {
  config.headers['Authorization'] = `Bearer ${localStorage.getItem('auth_token')}`
  config.headers['X-Coop-Code'] = localStorage.getItem('coop_codigo')
  return config
})


import { createApp } from 'vue';
import App from './components/App.vue';
import router from './admin-router';
import '@fortawesome/fontawesome-free/css/all.min.css';

createApp(App).use(router).mount('#admin-app');
