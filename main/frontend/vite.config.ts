import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  // Served from a subpath (…/public/) under XAMPP — relative chunk URLs
  // so dynamic imports resolve to public/assets/ instead of server root.
  base: './',
  plugins: [tailwindcss()],
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost/gate%20exam%20preparation/public',
    },
  },
  build: {
    outDir: '../public/assets',
    emptyOutDir: true,
    cssCodeSplit: false,
    // No modulepreload transform at all: restores true lazy import()
    // so non-3D pages never evaluate the 3D stack.
    modulePreload: false,
    rollupOptions: {
      input: 'src/main.ts',
      output: {
        entryFileNames: 'app.js',
        chunkFileNames: 'chunk-[name].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'app.css';
          }
          return '[name][extname]';
        },
        manualChunks(id: string) {
          if (id.includes('node_modules')) {
            // 3D family in ONE chunk: three/model-viewer/lit/gainmap were
            // split across chunk-three/chunk-model-viewer/chunk-vendor with
            // edges in both directions -> TDZ "Cannot access 'Kn'".
            // NOTE: plain 'lit' substring would false-match e.g. split-type,
            // so lit packages match on node_modules path segments only.
            const p = id.replace(/\\/g, '/');
            if (
              id.includes('three') ||
              id.includes('model-viewer') ||
              id.includes('@google') ||
              id.includes('gainmap') ||
              p.includes('node_modules/lit/') ||
              p.includes('node_modules/lit-html/') ||
              p.includes('node_modules/lit-element/') ||
              p.includes('node_modules/@lit/')
            ) return 'three';
            if (id.includes('echarts') || id.includes('zrender')) return 'echarts';
            if (id.includes('dompurify')) return 'sanitize';
            if (id.includes('marked') && !id.includes('marked-')) return 'marked';
            if (id.includes('idb-keyval') || id.includes('/idb/')) return 'idb';
            if (
              id.includes('mermaid') || id.includes('/d3-') || id.includes('/d3/') ||
              id.includes('dagre') || id.includes('graphlib') || id.includes('khroma') ||
              id.includes('stylis') || id.includes('cytoscape') || id.includes('cose') ||
              id.includes('robust-predicates') || id.includes('lodash') ||
              id.includes('/uuid') || id.includes('/entities')
            ) return 'mermaid';
            if (id.includes('@fullcalendar')) return 'fullcalendar';
            if (id.includes('three')) return 'three';
            if (id.includes('pdfjs-dist')) return 'pdfjs';
            if (id.includes('katex')) return 'katex';
            if (id.includes('lucide')) return 'lucide';
            if (id.includes('tippy') || id.includes('@popperjs')) return 'tippy';
            if (id.includes('driver.js')) return 'driver';
            if (id.includes('highlight.js')) return 'hljs';
            if (id.includes('dayjs')) return 'dayjs';
            if (id.includes('fuse')) return 'fuse';
            if (id.includes('canvas-confetti')) return 'confetti';
            if (id.includes('lottie-web')) return 'lottie';
            if (id.includes('gsap')) return 'gsap';
            if (id.includes('@tsparticles') || id.includes('tsparticles')) return 'particles';
            if (id.includes('lenis')) return 'lenis';
            if (id.includes('nprogress')) return 'nprogress';
            if (id.includes('auto-animate') || id.includes('@formkit')) return 'animate';
            if (id.includes('split-type')) return 'split';
            return 'vendor';
          }
          return undefined;
        },
      },
    },
  },
});