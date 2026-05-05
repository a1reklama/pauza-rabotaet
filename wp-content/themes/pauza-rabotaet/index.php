<?php
/**
 * Fallback template.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title(get_the_archive_title() ?: get_bloginfo('name'), get_the_archive_description());
?>

<section class="pauza-section">
    <div class="pauza-container">
        <?php if (have_posts()) : ?>
            <div class="pauza-card-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="pauza-card">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php echo pauza_internal_button(get_permalink(), __('Открыть', 'pauza-rabotaet')); ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('Записи не найдены.', 'pauza-rabotaet'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

