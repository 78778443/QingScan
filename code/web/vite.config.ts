import path from 'path'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  base: '/web/',
  build: {
    outDir: '../public/web',
    emptyOutDir: true,
  },
  server: {
    // 显式绑定 IPv4：WSL2 下 5173 端口会被 Windows 侧服务劫持(localhost 转发冲突)
    host: '127.0.0.1',
    port: 5199,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8080',
        changeOrigin: true,
      },
    },
  },
})
