<?php

declare(strict_types=1);

namespace Povly\MoonshineEditorJs\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class EditorJsBlockManager
{
    private string $blocksPath;
    private Collection $blocks;

    public function __construct()
    {
        $this->blocksPath = config('moonshine-editorjs.blocks_path');
        $this->blocks = collect();
    }

    /**
     * Получить все доступные блоки
     */
    public function getAvailableBlocks(): Collection
    {
        if ($this->blocks->isEmpty()) {
            $this->scanDirectory($this->blocksPath);
        }

        return $this->blocks;
    }

    /**
     * Рекурсивное сканирование директорий
     */
    private function scanDirectory(string $path): void
    {
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $blockName = basename($directory);

            $fullBlockName = $blockName;

            // Проверяем, есть ли в папке fields.json и render.blade.php
            $fieldsFile = $directory . '/fields.json';
            $renderFile = $directory . '/render.blade.php';

            if (File::exists($fieldsFile) && File::exists($renderFile)) {
                $this->blocks->put($fullBlockName, [
                    'name' => $fullBlockName,
                    'path' => $directory,
                    'fields' => $this->parseFields($fieldsFile),
                    'render' => $renderFile,
                ]);
            } else {
                // Продолжаем сканирование вложенных папок
                $this->scanDirectory($directory);
            }
        }
    }

    /**
     * Парсинг полей из fields.json
     */
    private function parseFields(string $fieldsFile): array
    {
        try {
            $content = File::get($fieldsFile);
            $fields = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            return is_array($fields) ? $fields : [];
        } catch (\JsonException $e) {
            return [];
        }
    }

    /**
     * Рендер блока
     */
    public function renderBlock(string $blockName, array $data = []): string
    {
        $blocks = $this->getAvailableBlocks();

        if (!$blocks->has($blockName)) {
            return '';
        }

        $block = $blocks->get($blockName);

        // Преобразуем путь в view name для Laravel
        $viewName = 'editorjs.blocks.' . str_replace('/', '.', $blockName);

        try {
            return View::make($viewName, ['data' => $data])->render();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Получить конфигурацию блока для Editor.js
     */
    public function getBlockConfig(string $blockName): array
    {
        $blocks = $this->getAvailableBlocks();

        if (!$blocks->has($blockName)) {
            return [];
        }

        $block = $blocks->get($blockName);

        return [
            'name' => $blockName,
            'title' => ucfirst(str_replace(['/', '_'], [' ', ' '], $blockName)),
            'fields' => $block['fields'],
        ];
    }

    /**
     * Получить все конфигурации блоков для Editor.js
     */
    public function getAllBlockConfigs(): array
    {
        $blocks = $this->getAvailableBlocks();
        $configs = [];

        foreach ($blocks as $blockName => $block) {
            $configs[$blockName] = $this->getBlockConfig($blockName);
        }

        return $configs;
    }

    /**
     * Создать view файлы для блоков в resources/views
     */
    public function publishBlockViews(): void
    {
        $blocks = $this->getAvailableBlocks();

        foreach ($blocks as $blockName => $block) {
            $viewPath = resource_path('views/editorjs/blocks/' . str_replace('/', '/', $blockName) . '.blade.php');
            $viewDir = dirname($viewPath);

            if (!File::exists($viewDir)) {
                File::makeDirectory($viewDir, 0755, true);
            }

            if (!File::exists($viewPath)) {
                File::copy($block['render'], $viewPath);
            }
        }
    }
}
