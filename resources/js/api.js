import axios from 'axios';

const api = axios.create({
  baseURL: '/api'
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  const coop = localStorage.getItem('coop_codigo');

  if (token) config.headers.Authorization = `Bearer ${token}`;
  if (coop) config.headers['X-Coop-Code'] = coop;

  return config;
});

export default api;
