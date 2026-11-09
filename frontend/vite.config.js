import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    // готовое приложение кладём в папку web/ в корне проекта, её отдаёт веб-сервер
    outDir: '../web',
    emptyOutDir: true,
  },
  server: {
    proxy: {
      // запросы /api/... идут в PHP (OpenServer)
      '/api': { target: 'http://friends', changeOrigin: true },
    },
  },
})
