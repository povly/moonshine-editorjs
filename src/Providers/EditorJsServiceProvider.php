<?php

declare(strict_types=1);

namespace Povly\MoonshineEditorJs\Providers;

use Illuminate\Support\ServiceProvider;

class EditorJsServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Публикуем конфигурацию
        $this->publishes([
            __DIR__ . '/../../config/moonshine-editorjs.php' => config_path('moonshine-editorjs.php'),
        ], 'povly-moonshine-editorjs-config');

        // Публикуем public
        $this->publishes([
            __DIR__ . '/../../public/vendor' => public_path('vendor/povly'),
        ], 'povly-moonshine-editorjs-assets');

        // Загружаем view файлы из пакета
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'povly-moonshine-editorjs-views');
    }
}
