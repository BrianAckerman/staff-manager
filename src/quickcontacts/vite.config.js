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
        admin_quick_contacts: "main.js",
      },
      output: {
        entryFileNames: `admin_quickcontacts.js`,
        chunkFileNames: `admin_quickcontacts.js`,
        assetFileNames: `admin_quickcontacts.[ext]`,
      },
    },
  },
});
