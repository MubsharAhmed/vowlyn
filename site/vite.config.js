import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                // Display face — geometric confidence (DESIGN.md)
                bunny('Montserrat', {
                    weights: [400, 500, 600, 700, 800],
                    display: 'swap',
                }),
                // Body face — systematic precision (DESIGN.md)
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    display: 'swap',
                }),
                // Mono accent — for section labels like "01 / SERVICES"
                bunny('JetBrains Mono', {
                    weights: [400, 500],
                    display: 'swap',
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
