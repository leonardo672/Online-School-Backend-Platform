import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import path from 'path'; 

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/categories.css',
                'resources/js/app.js',
                'resources/js/pages/certificates/create.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
        },
    },
});