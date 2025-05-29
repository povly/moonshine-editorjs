<?php

namespace Povly\MoonshineEditorjs\Providers;

use Illuminate\Support\ServiceProvider;

class MoonshineEditorjsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'povly-moonshine-editorjs');
        $this->publishes([
            __DIR__ . '/../../public/vendor' => public_path('vendor/povly'),
        ], 'povly-moonshine-editorjs-assets');
    }
}
