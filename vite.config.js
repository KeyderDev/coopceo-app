import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(), 
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/portal.js',
                'resources/js/admin.js',
                'resources/js/user.js',
            ],
            refresh: true,
        }),
    ],
});
