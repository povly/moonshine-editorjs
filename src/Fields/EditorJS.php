<?php

declare(strict_types=1);

namespace Povly\MoonshineEditorjs\Fields;

use Illuminate\Support\Facades\Vite;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\UI\Fields\Text;

class EditorJS extends Text
{
    protected string $view = 'povly-moonshine-editorjs::fields.editor-js';

    protected function assets(): array
    {
        $manifestPath = base_path('public/vendor/povly/moonshine-editorjs/manifest.json');
        $manifest = json_decode(file_get_contents($manifestPath), true);

        $editorFile = $manifest['resources/assets/js/editor.js']['file'] ?? '';
        $initFile = $manifest['resources/assets/js/editor-init.js']['file'] ?? '';
        $cssFile = $manifest['resources/assets/scss/editor-js.scss']['file'] ?? '';

        return [
            Css::make(Vite::asset("public/vendor/povly/moonshine-editorjs/$cssFile")),
            Js::make(Vite::asset("public/vendor/povly/moonshine-editorjs/$editorFile")),
            Js::make(Vite::asset("public/vendor/povly/moonshine-editorjs/$initFile")),
        ];
    }
}
