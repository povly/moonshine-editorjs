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
        return [
            Js::make(Vite::asset('public/vendor/povly/moonshine-editorjs/js/editor.js')),
            Js::make(Vite::asset('public/vendor/povly/moonshine-editorjs/js/editor-init.js')),
        ];
    }
}
