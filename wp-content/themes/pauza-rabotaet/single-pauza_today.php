<?php
/**
 * Single today text.
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
                <p class="pauza-eyebrow"><?php echo esc_html(pauza_meta(get_the_ID(), '_pauza_today_date', __('Только сегодня', 'pauza-rabotaet'))); ?></p>
                <h1><?php the_title(); ?></h1>
            </div>
        </section>
        <section class="pauza-section">
            <div class="pauza-container pauza-narrow">
                <div class="pauza-content">
                    <?php echo pauza_today_question_answer_html(get_post_field('post_content', get_the_ID())); ?>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();
