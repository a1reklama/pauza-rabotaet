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
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=109249467', 'ym');

        ym(109249467, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <!-- /Yandex.Metrika counter -->
    <link rel="icon" href="<?php echo esc_url(PAUZA_THEME_URI . '/assets/favicon.svg'); ?>" type="image/svg+xml">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<!-- Yandex.Metrika counter -->
<noscript><div><img src="https://mc.yandex.ru/watch/109249467" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<a class="pauza-skip-link" href="#content"><?php esc_html_e('Перейти к содержанию', 'pauza-rabotaet'); ?></a>

<header class="pauza-header">
    <div class="pauza-container pauza-header__inner">
        <a class="pauza-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('На главную', 'pauza-rabotaet'); ?>">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="pauza-brand__mark">12</span>
                <span class="pauza-brand__text">12 шагов для ВСЕХ</span>
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
                'depth'          => 2,
            ]);
            ?>
            <button class="pauza-nav__help-trigger" type="button" data-help-open aria-haspopup="dialog" aria-expanded="false" aria-controls="pauza-help-modal">
                <?php esc_html_e('Помощь', 'pauza-rabotaet'); ?>
            </button>
        </nav>
    </div>
</header>

<main id="content" class="pauza-main">
