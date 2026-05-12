<?php
/**
 * Pauza Rabotaet theme bootstrap.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PAUZA_THEME_VERSION', '1.0.1');
define('PAUZA_THEME_DIR', get_template_directory());
define('PAUZA_THEME_URI', get_template_directory_uri());

require_once PAUZA_THEME_DIR . '/inc/post-types.php';
require_once PAUZA_THEME_DIR . '/inc/meta-boxes.php';
require_once PAUZA_THEME_DIR . '/inc/options.php';
require_once PAUZA_THEME_DIR . '/inc/template-functions.php';
require_once PAUZA_THEME_DIR . '/inc/default-content.php';
require_once PAUZA_THEME_DIR . '/inc/admin-optimization.php';

function pauza_theme_setup(): void
{
    load_theme_textdomain('pauza-rabotaet', PAUZA_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Основное меню', 'pauza-rabotaet'),
        'footer'  => __('Меню в подвале', 'pauza-rabotaet'),
    ]);
}
add_action('after_setup_theme', 'pauza_theme_setup');

function pauza_enqueue_assets(): void
{
    wp_enqueue_style('pauza-style', get_stylesheet_uri(), [], PAUZA_THEME_VERSION);
    wp_enqueue_script(
        'pauza-theme',
        PAUZA_THEME_URI . '/assets/theme.js',
        [],
        PAUZA_THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'pauza_enqueue_assets');

function pauza_body_classes(array $classes): array
{
    $classes[] = 'pauza-site';

    return $classes;
}
add_filter('body_class', 'pauza_body_classes');
