document.addEventListener('DOMContentLoaded', function () {

    const editorJSElements = document.querySelectorAll('.p-editorjs');
    const blocksConfig = povlyMoonshineEditorJs.blocks || {};

    // Подготавливаем инструменты для Editor.js
    const tools = {
    };

    // Добавляем кастомные блоки
    Object.keys(blocksConfig).forEach(blockName => {
        const blockConfig = blocksConfig[blockName];
        tools[blockName.replace('/', '_')] = createCustomTool(blockName, blockConfig);
    });

    // Функция для создания кастомного инструмента из конфигурации блока
    function createCustomTool(blockName, blockConfig) {
        return class CustomBlock {
            static get toolbox() {
                return {
                    title: blockConfig.title,
                    icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-67 49v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
                };
            }

            constructor({ data }) {
                this.data = data || {};
                this.wrapper = undefined;
                this.blockConfig = blockConfig;
            }

            render() {
                this.wrapper = document.createElement('div');
                this.wrapper.classList.add('custom-block-wrapper');

                // Создаем форму на основе fields.json
                const form = this.createForm();
                this.wrapper.appendChild(form);

                return this.wrapper;
            }

            createForm() {
                const form = document.createElement('div');
                form.classList.add('custom-block-form');

                this.blockConfig.fields.forEach(field => {
                    const fieldWrapper = document.createElement('div');
                    fieldWrapper.classList.add('field-wrapper');

                    const label = document.createElement('label');
                    label.textContent = field.label;
                    label.classList.add('field-label');
                    fieldWrapper.appendChild(label);

                    const input = this.createInput(field);
                    fieldWrapper.appendChild(input);

                    form.appendChild(fieldWrapper);
                });

                return form;
            }

            createInput(field) {
                let input;

                switch (field.type) {
                    case 'text':
                        input = document.createElement('input');
                        input.type = 'text';
                        input.placeholder = field.placeholder || '';
                        input.value = this.data[field.id] || '';
                        break;

                    case 'textarea':
                        input = document.createElement('textarea');
                        input.placeholder = field.placeholder || '';
                        input.value = this.data[field.id] || '';
                        break;

                    case 'image':
                        input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        // Можно добавить логику загрузки изображений
                        break;

                    default:
                        input = document.createElement('input');
                        input.type = 'text';
                        input.value = this.data[field.id] || '';
                }

                input.classList.add('field-input');
                input.dataset.fieldId = field.id;

                // Обновляем данные при изменении
                input.addEventListener('change', () => {
                    this.data[field.id] = input.value;
                });

                return input;
            }

            save() {
                // Собираем данные из формы
                const inputs = this.wrapper.querySelectorAll('[data-field-id]');
                const data = {};

                inputs.forEach(input => {
                    data[input.dataset.fieldId] = input.value;
                });

                return data;
            }
        };
    }

    if (editorJSElements[0]){
        editorJSElements.forEach(element => {
            const moonshineFieldInput = element.closest('.moonshine-field').querySelector('[data-type="editor-js"]');

            // Инициализация Editor.js
            if (typeof EditorJS !== 'undefined') {
                const editor = new EditorJS({
                    holder: element,
                    tools: tools,
                    data: JSON.parse(element.dataset.value || '{}'),
                    onChange: function () {
                        editor.save().then((outputData) => {
                            moonshineFieldInput.value = JSON.stringify(outputData);
                        });
                    }
                });
            }
        });
    }
});
