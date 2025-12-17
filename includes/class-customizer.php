<?php
/**
 * Clase y configuraciones del Customizer para el plugin.
 */

if (!class_exists('WP_Customize_Control')) {
    return;
}

/**
 * Class Another_Repeater_Control
 * Control personalizado para listas repetibles (Logos, Aliados).
 */
class Another_Repeater_Control extends WP_Customize_Control {
    public $type = 'another_repeater';
    public $button_text = 'Añadir elemento';
    public $mode = 'image'; // Forzamos modo imagen para este plugin
    
    public $input_labels = [
        'title' => 'Nombre',
        'icon'  => 'Logo / Imagen',
        'url'   => 'Enlace'
    ];

    public function __construct($manager, $id, $args = array()) {
        parent::__construct($manager, $id, $args);
        if (isset($args['button_text'])) $this->button_text = $args['button_text'];
        if (isset($args['input_labels'])) $this->input_labels = array_merge($this->input_labels, $args['input_labels']);
    }

    public function render_content() {
        $value = $this->value();
        $value = $value ? json_decode($value, true) : [];
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        </label>

        <div class="another-repeater-wrapper <?php echo esc_attr($this->id); ?>" data-mode="image">
            <button type="button" class="button add-repeater-item"><?php echo esc_html($this->button_text); ?></button>

            <ul class="another-repeater-list">
                <?php if (!empty($value) && is_array($value)): ?>
                    <?php foreach ($value as $item): ?>
                        <li class="another-repeater-item">
                            <label class="field-label"><?php echo esc_html($this->input_labels['title']); ?></label>
                            <input type="text" class="title-field" value="<?php echo esc_attr($item['title'] ?? ''); ?>">

                            <label class="field-label"><?php echo esc_html($this->input_labels['icon']); ?></label>
                            <div class="image-upload-controls">
                                <?php 
                                    $img_val = $item['icon'] ?? ''; 
                                    $display = $img_val ? 'display:block;' : 'display:none;';
                                ?>
                                <img src="<?php echo esc_url($img_val); ?>" class="repeater-image-preview" style="<?php echo $display; ?>" />
                                <input type="hidden" class="icon-field" value="<?php echo esc_attr($img_val); ?>">
                                <button type="button" class="button upload-repeater-image">Seleccionar Imagen</button>
                                <?php if($img_val): ?>
                                    <button type="button" class="button remove-repeater-image" style="color: #a00;">X</button>
                                <?php endif; ?>
                            </div>

                            <label class="field-label"><?php echo esc_html($this->input_labels['url']); ?></label>
                            <input type="text" class="url-field" placeholder="https://..." value="<?php echo esc_attr($item['url'] ?? ''); ?>">

                            <span class="drag-handle">☰</span>
                            <button type="button" class="button remove-item">Eliminar</button>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <input type="hidden" class="another-repeater-hidden" <?php $this->link(); ?> value="<?php echo esc_attr($this->value()); ?>">
        </div>
        <?php
    }
}

/**
 * Registra las secciones y controles en el Customizer.
 * * @param WP_Customize_Manager $wp_customize
 */
function another_sa_register_customizer($wp_customize) {
    
    // Panel Principal
    $wp_customize->add_panel('another_sa_panel', [
        'title' => __('Another Strategic Alliances', 'another-sa'),
        'priority' => 100,
        'description' => 'Gestiona los sliders y grids de aliados.'
    ]);

    // SECCIÓN 1: ALIADOS (SLIDER)
    $wp_customize->add_section('another_sa_partners', [
        'title' => __('Aliados (Slider)', 'another-sa'),
        'panel' => 'another_sa_panel',
    ]);

    $wp_customize->add_setting('another_partners_data', [
        'default' => '', 
        'type' => 'option', // Importante: Guarda en wp_options, independiente del tema
        'sanitize_callback' => 'wp_kses_post'
    ]);

    $wp_customize->add_control(new Another_Repeater_Control($wp_customize, 'another_partners_data', [
        'label' => __('Logos del Slider', 'another-sa'),
        'section' => 'another_sa_partners',
        'button_text' => 'Añadir Aliado',
        'input_labels' => ['title' => 'Empresa', 'icon' => 'Logo', 'url' => 'Sitio Web']
    ]));

    // SECCIÓN 2: MARCAS (GRID)
    $wp_customize->add_section('another_sa_brands', [
        'title' => __('Marcas (Grilla)', 'another-sa'),
        'panel' => 'another_sa_panel',
    ]);

    $wp_customize->add_setting('another_brands_data', [
        'default' => '', 
        'type' => 'option', 
        'sanitize_callback' => 'wp_kses_post'
    ]);

    $wp_customize->add_control(new Another_Repeater_Control($wp_customize, 'another_brands_data', [
        'label' => __('Logos de Marcas', 'another-sa'),
        'section' => 'another_sa_brands',
        'button_text' => 'Añadir Marca',
        'input_labels' => ['title' => 'Marca', 'icon' => 'Logo', 'url' => 'Sitio Web']
    ]));
}
add_action('customize_register', 'another_sa_register_customizer');