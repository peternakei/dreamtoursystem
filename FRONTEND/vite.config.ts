import path from 'node:path'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import { defineConfig, loadEnv } from 'vite'
export default defineConfig(({mode}) => {
 const env = loadEnv(mode, process.cwd(), '')
 return {
  plugins: [vue(), tailwindcss()],
  resolve: {alias: {'@': path.resolve(__dirname, './src')}},
  server: {host: '127.0.0.1', port: 5174, strictPort: true, proxy: {
   '/backend': {target: env.VITE_BACKEND_URL || 'http://127.0.0.1:8001', changeOrigin: false, rewrite: p => p.replace(/^\/backend/, '')}
  }},
  define: {'import.meta.env.VITE_BUILD_DATE': JSON.stringify(new Date().getFullYear().toString())}
 }
})
