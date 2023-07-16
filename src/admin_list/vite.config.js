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
        "admin-list": "main.js",
      },
      output: {
        entryFileNames: `staff-list-bundle.js`,
        chunkFileNames: `staff-list-bundle.js`,
        assetFileNames: `staff-list-bundle.[ext]`,
      },
    },
  },
});
