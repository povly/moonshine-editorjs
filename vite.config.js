import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: 'vendor/moonshine-editorjs',
            input: ['resources/assets/js/editor-init.js', 'resources/assets/js/editor.js'],
            refresh: true,
        }),
    ],
    build: {
        sourcemap: false,
        rollupOptions: {
            output: {
                globals: {
                    '@editorjs/editorjs': 'EditorJS'
                }
            }
        }
    }
})
