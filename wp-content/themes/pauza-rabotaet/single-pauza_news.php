<?php
/**
 * Single news item.
 *
 * @package PauzaRabotaet
 */

get_header();

while (have_posts()) :
    the_post();
    $news_type = pauza_meta(get_the_ID(), '_pauza_news_type');
    $news_origin = pauza_meta(get_the_ID(), '_pauza_news_origin', 'project');
    $news_source = pauza_meta(get_the_ID(), '_pauza_news_source');
    $news_url = pauza_meta(get_the_ID(), '_pauza_news_url');
    $news_button = pauza_meta(get_the_ID(), '_pauza_news_button_label', __('Открыть', 'pauza-rabotaet'));
    ?>
    <article>
        <section class="pauza-page-hero">
            <div class="pauza-container">
                <p class="pauza-eyebrow"><?php echo esc_html(trim(($news_type ? $news_type . ' · ' : '') . get_the_date())); ?></p>
                <h1><?php the_title(); ?></h1>
                <?php echo 'external_test' === $news_origin ? pauza_origin_badge('external_test') : pauza_origin_badge('source', __('Новость проекта', 'pauza-rabotaet')); ?>
                <?php if ($news_source) : ?>
                    <p class="pauza-lead"><?php echo esc_html($news_source); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <section class="pauza-section">
            <div class="pauza-container pauza-narrow">
                <div class="pauza-content">
                    <?php the_content(); ?>
                    <?php echo pauza_smart_button($news_url, $news_button, 'pauza-button pauza-button--primary'); ?>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();
