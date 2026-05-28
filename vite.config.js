import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Bundle all CSS into single files for better performance
                'resources/css/app.css',
                'resources/css/public.css',
                // JS
                'resources/js/dashboard.js',
                'resources/js/invesmal-ui.js',
            ],
            refresh: false,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        hmr: {
            overlay: false,
        },
    },
});
