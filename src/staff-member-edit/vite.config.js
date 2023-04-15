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
        "staff-member-edit": "main.js",
      },
      output: {
        entryFileNames: `staff-edit-bundle.js`,
        chunkFileNames: `staff-edit-bundle.js`,
        assetFileNames: `staff-edit-bundle.[ext]`,
      },
    },
  },
});
