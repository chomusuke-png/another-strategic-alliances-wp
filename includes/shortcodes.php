<?php

function another_sa_widget_render($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'title' => '' 
    ], $atts);

    $post_id = intval($atts['id']);
    if (!$post_id) return '';

    // Recuperamos metadatos
    $layout = get_post_meta($post_id, '_another_layout', true) ?: 'slider';
    $color  = get_post_meta($post_id, '_another_title_color', true) ?: '#947e1e';
    $show_names = get_post_meta($post_id, '_another_show_names', true); // Checkbox
    
    $items_json = get_post_meta($post_id, '_another_items', true);
    $items  = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) return '';

    $display_title = !empty($atts['title']) ? $atts['title'] : get_the_title($post_id);
    
    // Clase extra para CSS si hay nombres activados
    $extra_class = $show_names ? 'has-names' : '';

    ob_start();
    ?>
    
    <?php if ($layout === 'slider'): ?>
        <section class="another-partners-section">
            <div class="another-container">
                <?php if($display_title): ?>
                    <h3 class="another-section-title" style="color: <?php echo esc_attr($color); ?>;">
                        <?php echo esc_html($display_title); ?>
                    </h3>
                <?php endif; ?>
                
                <div class="swiper another-partners-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ($items as $item): 
                            $name = esc_attr($item['title'] ?? '');
                            $logo = esc_url($item['icon'] ?? '');
                            $link = esc_url($item['url'] ?? '');
                        ?>
                            <div class="swiper-slide partner-logo <?php echo $extra_class; ?>">
                                <?php if($link): ?>
                                    <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                                        <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                                <?php endif; ?>

                                <?php if($show_names && $name): ?>
                                    <span class="another-item-name"><?php echo $name; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

    <?php else: ?>
        <section class="another-brands-section">
            <div class="another-container">
                <?php if($display_title): ?>
                    <h3 class="another-section-title" style="color: <?php echo esc_attr($color); ?>;">
                        <?php echo esc_html($display_title); ?>
                    </h3>
                <?php endif; ?>
                
                <div class="another-brands-grid">
                    <?php foreach ($items as $item): 
                        $name = esc_attr($item['title'] ?? '');
                        $logo = esc_url($item['icon'] ?? '');
                        $link = esc_url($item['url'] ?? '');
                    ?>
                        <div class="another-brand-item <?php echo $extra_class; ?>">
                            <?php if($link): ?>
                                <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                            <?php endif; ?>

                            <?php if($show_names && $name): ?>
                                <span class="another-item-name"><?php echo $name; ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <?php endif; ?>

    <?php
    return ob_get_clean();
}
add_shortcode('another_widget', 'another_sa_widget_render');