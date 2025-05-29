import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: 'vendor/moonshine-editorjs',
            input: {
                'editor-init': 'resources/js/editor-init.js',
                'editor': 'resources/js/editor.js'
            },
            refresh: true,
        }),
    ]
})
