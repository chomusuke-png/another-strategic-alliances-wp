<?php
class Another_SA_Admin {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function add_admin_menu() {
        add_menu_page(
            'Aliados y Marcas',
            'Aliados y Marcas',
            'manage_options',
            'another-sa-settings',
            [__CLASS__, 'render_page'],
            'dashicons-groups',
            25
        );
    }

    public static function register_settings() {
        // --- SECCIÓN ALIADOS ---
        register_setting('another_sa_group', 'another_partners_data', ['sanitize_callback' => 'wp_kses_post']);
        // Nuevo: Color Título Aliados
        register_setting('another_sa_group', 'another_partners_title_color', ['sanitize_callback' => 'sanitize_hex_color']);

        add_settings_section('another_sa_partners_section', 'Configuración de Aliados', null, 'another-sa-settings');

        add_settings_field(
            'another_partners_color_field',
            'Color del Título',
            [__CLASS__, 'render_color_picker'],
            'another-sa-settings',
            'another_sa_partners_section',
            ['label_for' => 'another_partners_title_color'] // Pasamos el ID del campo
        );

        add_settings_field(
            'another_partners_field',
            'Listado de Aliados',
            [__CLASS__, 'render_repeater_partners'],
            'another-sa-settings',
            'another_sa_partners_section'
        );

        // --- SECCIÓN MARCAS ---
        register_setting('another_sa_group', 'another_brands_data', ['sanitize_callback' => 'wp_kses_post']);
        // Nuevo: Color Título Marcas
        register_setting('another_sa_group', 'another_brands_title_color', ['sanitize_callback' => 'sanitize_hex_color']);

        add_settings_section('another_sa_brands_section', 'Configuración de Marcas', null, 'another-sa-settings');

        add_settings_field(
            'another_brands_color_field',
            'Color del Título',
            [__CLASS__, 'render_color_picker'],
            'another-sa-settings',
            'another_sa_brands_section',
            ['label_for' => 'another_brands_title_color']
        );

        add_settings_field(
            'another_brands_field',
            'Listado de Marcas',
            [__CLASS__, 'render_repeater_brands'],
            'another-sa-settings',
            'another_sa_brands_section'
        );
    }

    public static function render_page() {
        ?>
        <div class="wrap">
            <h1>Gestión de Aliados y Marcas</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('another_sa_group');
                do_settings_sections('another-sa-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Callback genérico para el Color Picker
    public static function render_color_picker($args) {
        $option_name = $args['label_for'];
        $color = get_option($option_name, '#947e1e'); // Valor por defecto
        ?>
        <input type="text" name="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($color); ?>" class="another-color-field" data-default-color="#947e1e" />
        <p class="description">Selecciona el color para el título de esta sección.</p>
        <?php
    }

    public static function render_repeater_partners() {
        $value = get_option('another_partners_data', '');
        self::render_repeater_html('another_partners_data', $value, 'Añadir Aliado');
    }

    public static function render_repeater_brands() {
        $value = get_option('another_brands_data', '');
        self::render_repeater_html('another_brands_data', $value, 'Añadir Marca');
    }

    private static function render_repeater_html($input_name, $json_value, $btn_text) {
        $items = !empty($json_value) ? json_decode($json_value, true) : [];
        if (!is_array($items)) $items = [];
        ?>
        <div class="another-repeater-wrapper">
            <button type="button" class="button button-secondary add-repeater-item">
                <span class="dashicons dashicons-plus"></span> <?php echo esc_html($btn_text); ?>
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
            <textarea name="<?php echo esc_attr($input_name); ?>" class="another-repeater-hidden" style="display:none;"><?php echo esc_textarea($json_value); ?></textarea>
        </div>
        <?php
    }
}
Another_SA_Admin::init();