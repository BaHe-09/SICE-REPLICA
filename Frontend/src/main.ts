import { createApp } from 'vue'
import App from './App.vue'
import router from './router'           // ← esta línea es crítica

const app = createApp(App)
app.use(router)                         // ← y esta
app.mount('#app')