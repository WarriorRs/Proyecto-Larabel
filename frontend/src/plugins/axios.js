// src/plugins/axios.js
import axios from 'axios'

const api = axios.create({
  baseURL: 'http://127.0.0.1:8080/api', // Cambia al URL de tu backend
  headers: {
    Accept: 'application/json',
  },
})

// Agrega token si usas autenticación y está guardado en localStorage
api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => Promise.reject(error));

export default api
