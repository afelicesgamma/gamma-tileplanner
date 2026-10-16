import { defineConfig } from 'vite';

export default defineConfig({
  base: './',
  build: {
    manifest: 'manifest.json',
    outDir: 'dist',
    rollupOptions: {
      input: 'resources/scripts/app.ts',
    },
  },
});
