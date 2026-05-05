<?php
/**
 * Footer.
 *
 * @package PauzaRabotaet
 */

$footer_note = pauza_get_option('footer_note', 'Сайт помогает ориентироваться в программе и ведет во внешние группы, боты и видеоматериалы.');
?>
</main>

<footer class="pauza-footer">
    <div class="pauza-container pauza-footer__grid">
        <div>
            <h2><?php esc_html_e('Пауза работает', 'pauza-rabotaet'); ?></h2>
            <p><?php echo esc_html($footer_note); ?></p>
        </div>
        <nav aria-label="<?php esc_attr_e('Меню в подвале', 'pauza-rabotaet'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'pauza-footer__links',
                'fallback_cb'    => false,
                'depth'          => 1,
            ]);
            ?>
        </nav>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

