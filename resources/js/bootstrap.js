import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  const coop = localStorage.getItem('coop');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  if (coop) {
    config.headers['X-Coop-Code'] = coop;
  }

  return config;
});
