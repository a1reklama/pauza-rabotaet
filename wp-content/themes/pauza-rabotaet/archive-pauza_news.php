<?php
/**
 * News archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('Новости', 'Объявления, новые видео, обновления групп и важные сообщения.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <?php if (have_posts()) : ?>
            <div class="pauza-card-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="pauza-card">
                        <p class="pauza-tag"><?php echo esc_html(get_the_date()); ?></p>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php echo pauza_internal_button(get_permalink(), __('Читать', 'pauza-rabotaet')); ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="pauza-empty">
                <h2><?php esc_html_e('Новостей пока нет', 'pauza-rabotaet'); ?></h2>
                <p><?php esc_html_e('Владелец сможет добавлять объявления и обновления через админку WordPress.', 'pauza-rabotaet'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

