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
        "admin_staff-edit": "main.js",
      },
      output: {
        entryFileNames: `admin_staff-edit-bundle.js`,
        chunkFileNames: `admin_staff-edit.js`,
        assetFileNames: `admin_staff-edit-bundle.[ext]`,
      },
    },
  },
});
