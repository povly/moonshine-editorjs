<x-moonshine::form.input :attributes="$attributes
    ->merge([
        'class' => 'hidden',
        'data-type' => 'editor-js',
    ])
    ->except('x-bind:id')">
    {!! $value ?? '' !!}
</x-moonshine::form.input>
<div class="p-editorjs" id="editorjs"></div>
