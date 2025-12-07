import axios from 'axios';

axios.defaults.baseURL = import.meta.env.VITE_APP_URL;

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  const coop = localStorage.getItem('coop_codigo');

  if (token) config.headers.Authorization = `Bearer ${token}`;
  if (coop) config.headers['X-Coop-Code'] = coop;

  return config;
});

import { createApp } from 'vue';
import App from './components/App.vue';
import router from './user-router';
import '@fortawesome/fontawesome-free/css/all.min.css';

createApp(App).use(router).mount('#user-app');
