import { createApp } from 'vue'
import App from './components/App.vue'
import router from './user-router'

createApp(App).use(router).mount('#user-app')
