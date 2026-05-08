<?php
/**
 * Today texts archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title(sprintf(__('Только сегодня, %s', 'pauza-rabotaet'), date_i18n('j F Y')), 'Редактируемые тексты без смешивания с навигацией по шагам.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <?php if (have_posts()) : ?>
            <div class="pauza-card-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="pauza-card">
                        <?php $date_label = pauza_meta(get_the_ID(), '_pauza_today_date'); ?>
                        <?php if ($date_label) : ?>
                            <p class="pauza-tag"><?php echo esc_html($date_label); ?></p>
                        <?php endif; ?>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php echo pauza_internal_button(get_permalink(), __('Читать', 'pauza-rabotaet')); ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('Тексты пока не добавлены.', 'pauza-rabotaet'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
