<?php
/**
 * Materials archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('Материалы', 'Основной вход к 360 видео: Telegram, Rutube и Яндекс.Диск. Остальные инструменты показываются внутри нужного шага.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <?php if (have_posts()) : ?>
            <div class="pauza-card-grid pauza-card-grid--three">
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $url = pauza_meta(get_the_ID(), '_pauza_material_url');
                    $label = pauza_meta(get_the_ID(), '_pauza_material_button_label', __('Открыть', 'pauza-rabotaet'));
                    ?>
                    <article class="pauza-card">
                        <h2><?php the_title(); ?></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php echo pauza_button($url, $label, 'pauza-button pauza-button--primary'); ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('Материалы пока не добавлены.', 'pauza-rabotaet'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
