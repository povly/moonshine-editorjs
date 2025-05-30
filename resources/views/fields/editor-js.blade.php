<x-moonshine::form.input :attributes="$attributes
    ->merge([
        'class' => 'hidden',
        'data-type' => 'editor-js',
    ])
    ->except('x-bind:id')">
    {!! $value ?? '' !!}
</x-moonshine::form.input>
<div class="p-editorjs" id="editorjs" data-value="{{ $value }}"></div>
@push('scripts')
    <script>
        const povlyMoonshineEditorJs = {
            blocks: @json(new \Povly\MoonshineEditorJs\Services\EditorJsBlockManager()->getAllBlockConfigs()),
        };
    </script>
@endpush
