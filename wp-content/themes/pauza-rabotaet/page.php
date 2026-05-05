<?php
/**
 * Default page.
 *
 * @package PauzaRabotaet
 */

get_header();

while (have_posts()) :
    the_post();
    ?>
    <article>
        <section class="pauza-page-hero">
            <div class="pauza-container">
                <p class="pauza-eyebrow"><?php esc_html_e('Пауза работает', 'pauza-rabotaet'); ?></p>
                <h1><?php the_title(); ?></h1>
            </div>
        </section>
        <section class="pauza-section">
            <div class="pauza-container pauza-narrow">
                <div class="pauza-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();

