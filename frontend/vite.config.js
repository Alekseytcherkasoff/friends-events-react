import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      // запросы /api/... идут в PHP (OpenServer)
      '/api': { target: 'http://friends', changeOrigin: true },
    },
  },
})
