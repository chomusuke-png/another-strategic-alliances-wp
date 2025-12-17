<?php

// SHORTCODE ALIADOS
function another_sa_partners_shortcode($atts) {
    $atts = shortcode_atts(['title' => 'Nuestros Aliados Estratégicos'], $atts);
    
    $partners = json_decode(get_option('another_partners_data'), true);
    // Recuperamos el color (Default: mostaza)
    $title_color = get_option('another_partners_title_color', '#947e1e');

    if (empty($partners)) return '';

    ob_start(); 
    ?>
    <section class="another-partners-section">
        <div class="another-container">
            <?php if($atts['title']): ?>
                <h3 class="another-section-title" style="color: <?php echo esc_attr($title_color); ?>;">
                    <?php echo esc_html($atts['title']); ?>
                </h3>
            <?php endif; ?>
            
            <div class="swiper another-partners-slider">
                <div class="swiper-wrapper">
                    <?php foreach ($partners as $partner): 
                         $name = esc_attr($partner['title'] ?? '');
                         $logo = esc_url($partner['icon'] ?? '');
                         $link = esc_url($partner['url'] ?? '');
                    ?>
                        <div class="swiper-slide partner-logo">
                            <?php if($link): ?>
                                <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('another_partners', 'another_sa_partners_shortcode');


// SHORTCODE MARCAS
function another_sa_brands_shortcode($atts) {
    $atts = shortcode_atts(['title' => 'Marcas que confían en nosotros'], $atts);
    
    $brands = json_decode(get_option('another_brands_data'), true);
    // Recuperamos el color
    $title_color = get_option('another_brands_title_color', '#947e1e');

    if (empty($brands)) return '';

    ob_start(); 
    ?>
    <section class="another-brands-section">
        <div class="another-container">
            <?php if($atts['title']): ?>
                <h3 class="another-section-title" style="color: <?php echo esc_attr($title_color); ?>;">
                    <?php echo esc_html($atts['title']); ?>
                </h3>
            <?php endif; ?>
            
            <div class="another-brands-grid">
                <?php foreach ($brands as $brand): 
                    $name = esc_attr($brand['title'] ?? '');
                    $logo = esc_url($brand['icon'] ?? '');
                    $link = esc_url($brand['url'] ?? '');
                ?>
                    <div class="another-brand-item">
                        <?php if($link): ?>
                            <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                                <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('another_brands', 'another_sa_brands_shortcode');