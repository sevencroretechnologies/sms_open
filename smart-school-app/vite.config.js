import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/rtl.css',
                'resources/js/app.js',
                'resources/js/rtl-support.js'
            ],
            refresh: true,
        }),
    ],
});
