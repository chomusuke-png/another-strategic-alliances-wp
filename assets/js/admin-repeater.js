jQuery(document).ready(function ($) {

    // INICIALIZAR COLOR PICKER
    // Buscamos los inputs con nuestra clase y activamos el plugin de WP
    $('.another-color-field').wpColorPicker();

    // ... (El resto del código del repetidor sigue igual que antes) ...
    
    function updateField(wrapper) {
        let items = [];
        wrapper.find('.another-repeater-item').each(function () {
            items.push({
                title: $(this).find('.title-field').val(),
                icon: $(this).find('.icon-field').val(),
                url: $(this).find('.url-field').val()
            });
        });
        wrapper.find('.another-repeater-hidden').val(JSON.stringify(items));
    }

    $('.another-repeater-wrapper').each(function () {
        const wrapper = $(this);
        const list = wrapper.find('.another-repeater-list');

        list.sortable({
            handle: '.drag-handle',
            placeholder: 'ui-sortable-placeholder',
            update: function () { updateField(wrapper); }
        });

        wrapper.on('click', '.add-repeater-item', function () {
            const itemHtml = `
                <li class="another-repeater-item">
                    <div class="repeater-row">
                        <div class="repeater-col">
                            <label class="field-label">Nombre</label>
                            <input type="text" class="title-field widefat">
                        </div>
                        <div class="repeater-col">
                            <label class="field-label">Enlace</label>
                            <input type="text" class="url-field widefat">
                        </div>
                    </div>
                    <div class="repeater-row media-row">
                        <label class="field-label">Logo</label>
                        <div class="image-upload-controls">
                            <img src="" class="repeater-image-preview" style="display:none;" />
                            <input type="hidden" class="icon-field">
                            <button type="button" class="button upload-repeater-image">Elegir Imagen</button>
                        </div>
                    </div>
                    <div class="repeater-actions">
                        <span class="drag-handle dashicons dashicons-move"></span>
                        <button type="button" class="button button-link-delete remove-item">Eliminar</button>
                    </div>
                </li>
            `;
            list.append(itemHtml);
            updateField(wrapper);
        });

        wrapper.on('click', '.remove-item', function () {
            if (confirm('¿Eliminar elemento?')) {
                $(this).closest('.another-repeater-item').remove();
                updateField(wrapper);
            }
        });

        wrapper.on('input change', 'input', function () {
            updateField(wrapper);
        });
    });

    $('body').on('click', '.upload-repeater-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var container = button.closest('.image-upload-controls');
        var frame = wp.media({
            title: 'Seleccionar Logo',
            button: { text: 'Usar imagen' },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            container.find('.icon-field').val(attachment.url).trigger('change');
            container.find('.repeater-image-preview').attr('src', attachment.url).show();
            if(container.find('.remove-repeater-image').length === 0) {
                container.append('<button type="button" class="button remove-repeater-image" style="color: #a00; margin-left: 10px;">Quitar</button>');
            }
        });
        frame.open();
    });

    $('body').on('click', '.remove-repeater-image', function(e) {
        e.preventDefault();
        var container = $(this).closest('.image-upload-controls');
        container.find('.icon-field').val('').trigger('change');
        container.find('.repeater-image-preview').hide().attr('src', '');
        $(this).remove();
    });
});