import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './router';
import axios from 'axios';

// Axios varsayılan ayarları
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.baseURL = '/api';

// CSRF token ayarı
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Axios interceptor ekle
axios.interceptors.request.use(
    config => {
        console.log('API İsteği:', config.method.toUpperCase(), config.url);
        return config;
    },
    error => {
        console.error('İstek Hatası:', error);
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    response => {
        console.log('API Yanıtı:', response.status, response.config.url);
        return response;
    },
    error => {
        console.error('API Hatası:', error.response?.status, error.config?.url, error.response?.data);
        return Promise.reject(error);
    }
);

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

const app = createApp(App);

// Global axios örneğini Vue uygulamasına ekle
app.config.globalProperties.$axios = axios;

app.use(router);
app.mount('#app');
