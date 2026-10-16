import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  base: './',
  plugins: [tailwindcss()],
  build: {
    manifest: 'manifest.json',
    outDir: 'dist',
    rollupOptions: {
      input: 'resources/scripts/app.ts',
    },
  },
});
