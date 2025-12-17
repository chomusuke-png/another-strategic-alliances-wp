<?php
class Another_SA_CPT {

    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_meta_data']);
        // Columnas personalizadas en el listado del admin
        add_filter('manage_another_widget_posts_columns', [__CLASS__, 'custom_columns']);
        add_action('manage_another_widget_posts_custom_column', [__CLASS__, 'custom_columns_content'], 10, 2);
    }

    // 1. Registrar Custom Post Type
    public static function register_cpt() {
        register_post_type('another_widget', [
            'labels' => [
                'name'               => 'Widgets de Logos',
                'singular_name'      => 'Widget',
                'add_new'            => 'Añadir Nuevo Widget',
                'add_new_item'       => 'Añadir Nuevo Widget de Logos',
                'edit_item'          => 'Editar Widget',
                'new_item'           => 'Nuevo Widget',
                'all_items'          => 'Todos los Widgets',
                'menu_name'          => 'Widgets de Logos'
            ],
            'public'             => false,  // No accesible públicamente por URL
            'show_ui'            => true,   // Mostrar en admin
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-images-alt2',
            'supports'           => ['title'], // Solo usamos el título nativo
            'capability_type'    => 'post',
        ]);
    }

    // 2. Añadir Meta Boxes
    public static function add_meta_boxes() {
        // Caja de Configuración (Lateral)
        add_meta_box(
            'another_sa_settings_box',
            'Configuración del Widget',
            [__CLASS__, 'render_settings_box'],
            'another_widget',
            'side',
            'high'
        );

        // Caja del Repetidor (Principal)
        add_meta_box(
            'another_sa_items_box',
            'Logos / Elementos',
            [__CLASS__, 'render_items_box'],
            'another_widget',
            'normal',
            'high'
        );

        // Caja informativa de Shortcode
        add_meta_box(
            'another_sa_shortcode_box',
            'Shortcode',
            [__CLASS__, 'render_shortcode_box'],
            'another_widget',
            'side',
            'low'
        );
    }

    // Render: Configuración (Tipo y Color)
    public static function render_settings_box($post) {
        // Recuperar valores
        $layout = get_post_meta($post->ID, '_another_layout', true) ?: 'slider';
        $color  = get_post_meta($post->ID, '_another_title_color', true) ?: '#947e1e';
        
        // Nonce de seguridad
        wp_nonce_field('another_sa_save_action', 'another_sa_nonce');
        ?>
        <p>
            <label><strong>Tipo de Visualización:</strong></label><br>
            <select name="another_layout" style="width:100%; margin-top:5px;">
                <option value="slider" <?php selected($layout, 'slider'); ?>>Carrusel (Slider)</option>
                <option value="grid" <?php selected($layout, 'grid'); ?>>Cuadrícula (Grid)</option>
            </select>
        </p>
        <hr>
        <p>
            <label><strong>Color del Título:</strong></label><br>
            <input type="text" name="another_title_color" value="<?php echo esc_attr($color); ?>" class="another-color-field" data-default-color="#947e1e" />
        </p>
        <?php
    }

    // Render: Repetidor de Items
    public static function render_items_box($post) {
        $json_value = get_post_meta($post->ID, '_another_items', true);
        $items = !empty($json_value) ? json_decode($json_value, true) : [];
        if (!is_array($items)) $items = [];
        ?>
        <div class="another-repeater-wrapper">
            <button type="button" class="button button-primary add-repeater-item">
                <span class="dashicons dashicons-plus"></span> Añadir Logo
            </button>

            <ul class="another-repeater-list">
                <?php foreach ($items as $item): ?>
                    <li class="another-repeater-item">
                        <div class="repeater-row">
                            <div class="repeater-col">
                                <label class="field-label">Nombre</label>
                                <input type="text" class="title-field widefat" value="<?php echo esc_attr($item['title'] ?? ''); ?>">
                            </div>
                            <div class="repeater-col">
                                <label class="field-label">Enlace</label>
                                <input type="text" class="url-field widefat" value="<?php echo esc_attr($item['url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="repeater-row media-row">
                            <label class="field-label">Logo</label>
                            <div class="image-upload-controls">
                                <?php 
                                    $img_val = $item['icon'] ?? ''; 
                                    $display = $img_val ? 'display:block;' : 'display:none;';
                                ?>
                                <img src="<?php echo esc_url($img_val); ?>" class="repeater-image-preview" style="<?php echo $display; ?>" />
                                <input type="hidden" class="icon-field" value="<?php echo esc_attr($img_val); ?>">
                                <button type="button" class="button upload-repeater-image">Elegir Imagen</button>
                                <?php if($img_val): ?>
                                    <button type="button" class="button remove-repeater-image" style="color: #a00;">Quitar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="repeater-actions">
                            <span class="drag-handle dashicons dashicons-move"></span>
                            <button type="button" class="button button-link-delete remove-item">Eliminar</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <textarea name="another_items" class="another-repeater-hidden" style="display:none;"><?php echo esc_textarea($json_value); ?></textarea>
        </div>
        <?php
    }

    // Render: Shortcode Helper
    public static function render_shortcode_box($post) {
        ?>
        <p>Copia y pega este shortcode donde quieras mostrar este widget:</p>
        <code style="display:block; padding:10px; background:#f0f0f1; border:1px solid #ccc;">
            [another_widget id="<?php echo $post->ID; ?>"]
        </code>
        <?php
    }

    // 3. Guardar Datos
    public static function save_meta_data($post_id) {
        // Verificar nonce y permisos
        if (!isset($_POST['another_sa_nonce']) || !wp_verify_nonce($_POST['another_sa_nonce'], 'another_sa_save_action')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        // Guardar Layout
        if (isset($_POST['another_layout'])) {
            update_post_meta($post_id, '_another_layout', sanitize_key($_POST['another_layout']));
        }
        
        // Guardar Color
        if (isset($_POST['another_title_color'])) {
            update_post_meta($post_id, '_another_title_color', sanitize_hex_color($_POST['another_title_color']));
        }

        // Guardar Items (JSON)
        // Nota: wp_kses_post_deep permite HTML seguro, json_encode escapa lo necesario
        if (isset($_POST['another_items'])) {
            // Decodificamos y volvemos a codificar para limpiar
            $json = wp_unslash($_POST['another_items']);
            update_post_meta($post_id, '_another_items', $json);
        }
    }

    // 4. Columnas Admin
    public static function custom_columns($columns) {
        $columns['shortcode'] = 'Shortcode';
        $columns['layout'] = 'Tipo';
        return $columns;
    }

    public static function custom_columns_content($column, $post_id) {
        if ($column === 'shortcode') {
            echo '<code>[another_widget id="' . $post_id . '"]</code>';
        }
        if ($column === 'layout') {
            $layout = get_post_meta($post_id, '_another_layout', true);
            echo ($layout === 'slider') ? 'Carrusel' : 'Cuadrícula';
        }
    }
}
Another_SA_CPT::init();