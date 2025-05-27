<?php

declare(strict_types=1);

namespace Povly\MoonshineEditorjs\Fields;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\UI\Fields\Text;

class EditorJS extends Text
{
    protected string $view = 'povly-moonshine-editorjs::fields.editor-js';

    protected function assets(): array
    {
        return [
            // Css::make('vendor/moonshine-quill/css/quill.snow.css'),
            Js::make('vendor/povly/moonshine-editorjs/js/editor.js'),
            Js::make('vendor/povly/moonshine-editorjs/js/editor-init.js'),
        ];
    }
}
