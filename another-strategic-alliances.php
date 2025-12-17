<?php
/**
 * Plugin Name: Another Strategic Alliances
 * Description: Plugin modular para gestionar Aliados y Marcas desde el WP-Admin (con opciones de color).
 * Version: 1.2.0
 * Author: Zumito
 * Text Domain: another-sa
 */

if (!defined('ABSPATH')) exit; 

define('ANOTHER_SA_PATH', plugin_dir_path(__FILE__));
define('ANOTHER_SA_URL', plugin_dir_url(__FILE__));

require_once ANOTHER_SA_PATH . 'includes/class-admin.php';
require_once ANOTHER_SA_PATH . 'includes/shortcodes.php';

// Frontend Assets
function another_sa_enqueue_scripts() {
    wp_enqueue_style('another-sa-style', ANOTHER_SA_URL . 'assets/css/style.css', [], '1.0.0');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.0.0');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.0.0', true);
    wp_enqueue_script('another-sa-frontend', ANOTHER_SA_URL . 'assets/js/frontend.js', ['swiper-js'], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'another_sa_enqueue_scripts');

// Admin Assets
function another_sa_admin_assets($hook) {
    // Solo cargamos en nuestra página
    if (strpos($hook, 'another-sa-settings') === false) {
        return;
    }

    // 1. Media Uploader
    wp_enqueue_media(); 
    
    // 2. Color Picker (Nativo de WP)
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // 3. Scripts Propios
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script(
        'another-sa-repeater-js',
        ANOTHER_SA_URL . 'assets/js/admin-repeater.js',
        ['jquery', 'jquery-ui-sortable', 'wp-color-picker'], // Dependencia agregada
        '1.2.0',
        true
    );

    wp_enqueue_style('another-sa-repeater-css', ANOTHER_SA_URL . 'assets/css/admin-repeater.css', [], '1.0.0');
}
add_action('admin_enqueue_scripts', 'another_sa_admin_assets');