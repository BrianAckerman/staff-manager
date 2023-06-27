import { fileURLToPath, URL } from "node:url";

import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
  build: {
    outDir: "../../dist/",
    rollupOptions: {
      input: {
        archive_staff: "main.js",
      },
      output: {
        entryFileNames: `archive_staff-bundle.js`,
        chunkFileNames: `archive_staff-bundle.js`,
        assetFileNames: `archive_staff-bundle.[ext]`,
      },
    },
  },
});
