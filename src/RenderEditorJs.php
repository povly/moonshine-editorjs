<?php

declare(strict_types=1);

namespace Sckatik\MoonshineEditorJs;

use EditorJS\EditorJS;
use EditorJS\EditorJSException;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Sckatik\MoonshineEditorJs\Support\EditorJsToolRegistry;

final class RenderEditorJs
{

    /**
     * Render blocks
     *
     * @param string $data
     * @return string
     * @throws Exception
     */
    public function render(string $data): string
    {
        try {
            $data = $this->normalizeListBlocks($data);

            $registry = app(EditorJsToolRegistry::class);

            $renderSettings = array_merge_recursive(
                (array) config('moonshine-editor-js.renderSettings', []),
                $registry->getRenderSettings(),
            );

            $configJson = json_encode($renderSettings ?: []);

            $editor = new EditorJS($data, $configJson);

            $renderedBlocks = [];
            foreach ($editor->getBlocks() as $block) {
                $registeredView = $registry->getBlockView($block['type']);

                $viewName = $registeredView
                    ?? "moonshine-editorjs::blocks." . Str::snake($block['type'], '-');

                if (!View::exists($viewName)) {
                    $viewName = 'moonshine-editorjs::blocks.not-found';
                }

                $renderedBlocks[] = View::make($viewName, [
                    'type' => $block['type'],
                    'data' => $block['data']
                ])->render();
            }

            return implode($renderedBlocks);
        } catch (EditorJSException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Приводит list-блоки к легаси-формату валидатора: новый @editorjs/list
     * 2.x сохраняет элементы объектами {content, items, meta} и добавляет
     * meta на уровне блока, а конфигурация валидации ждёт плоские строки.
     * Невалидный JSON идёт дальше как есть — своё исключение бросит EditorJS.
     */
    private function normalizeListBlocks(string $data): string
    {
        $decoded = json_decode($data, true);

        if (! is_array($decoded) || ! is_array($decoded['blocks'] ?? null)) {
            return $data;
        }

        foreach ($decoded['blocks'] as $index => $block) {
            if (! is_array($block) || ($block['type'] ?? null) !== 'list' || ! is_array($block['data'] ?? null)) {
                continue;
            }

            unset($decoded['blocks'][$index]['data']['meta']);

            if (is_array($decoded['blocks'][$index]['data']['items'] ?? null)) {
                $decoded['blocks'][$index]['data']['items'] = $this->flattenListItems($decoded['blocks'][$index]['data']['items']);
            }
        }

        return (string) json_encode($decoded, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Элементы нового формата — объекты {content, items, meta}: content
     * становится строкой пункта, вложенные подпункты разворачиваются в тот
     * же плоский уровень (вью рендерит одноуровневый список). Строки
     * старого формата проходят без изменений.
     *
     * @param  list<mixed>  $items
     * @return list<string>
     */
    private function flattenListItems(array $items): array
    {
        $flat = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $content = trim((string) ($item['content'] ?? ''));

                if ($content !== '') {
                    $flat[] = $content;
                }

                $nested = is_array($item['items'] ?? null) ? $item['items'] : [];
                $flat = [...$flat, ...$this->flattenListItems($nested)];
            } elseif (is_string($item)) {
                $flat[] = $item;
            }
        }

        return $flat;
    }

}
