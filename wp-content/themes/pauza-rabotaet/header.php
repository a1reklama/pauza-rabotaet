<?php
/**
 * Header.
 *
 * @package PauzaRabotaet
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="pauza-skip-link" href="#content"><?php esc_html_e('Перейти к содержанию', 'pauza-rabotaet'); ?></a>

<header class="pauza-header">
    <div class="pauza-container pauza-header__inner">
        <a class="pauza-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('На главную', 'pauza-rabotaet'); ?>">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="pauza-brand__mark">П</span>
                <span class="pauza-brand__text">Пауза работает</span>
            <?php endif; ?>
        </a>

        <button class="pauza-menu-toggle" type="button" aria-expanded="false" aria-controls="pauza-primary-menu">
            <span></span>
            <span></span>
            <span></span>
            <span class="screen-reader-text"><?php esc_html_e('Открыть меню', 'pauza-rabotaet'); ?></span>
        </button>

        <nav class="pauza-nav" id="pauza-primary-menu" aria-label="<?php esc_attr_e('Основное меню', 'pauza-rabotaet'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'pauza-nav__list',
                'fallback_cb'    => 'pauza_fallback_menu',
                'depth'          => 1,
            ]);
            ?>
        </nav>
    </div>
</header>

<main id="content" class="pauza-main">
