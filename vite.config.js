import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
<<<<<<< Updated upstream
            input: ["resources/js/app.js", "resources/css/app.css"],
=======
            input: ["resources/css/app.css", "resources/js/app.js"],
>>>>>>> Stashed changes
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["laravel-echo", "pusher-js"],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
        include: ["laravel-echo", "pusher-js"],
    },
});
