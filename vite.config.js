import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import autoprefixer from 'autoprefixer'

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: 'vendor/moonshine-editorjs',
            input: [
                'resources/assets/js/editor-init.js',
                'resources/assets/js/editor.js',
                'resources/assets/scss/editor-js.scss'
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [autoprefixer()]
        }
    },
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
