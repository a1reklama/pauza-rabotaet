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
                    <?php
                    $news_type = pauza_meta(get_the_ID(), '_pauza_news_type');
                    $news_origin = pauza_meta(get_the_ID(), '_pauza_news_origin', 'project');
                    $news_source = pauza_meta(get_the_ID(), '_pauza_news_source');
                    $news_url = pauza_meta(get_the_ID(), '_pauza_news_url');
                    $news_button = pauza_meta(get_the_ID(), '_pauza_news_button_label', __('Открыть', 'pauza-rabotaet'));
                    ?>
                    <article class="pauza-card">
                        <?php echo 'external_test' === $news_origin ? pauza_origin_badge('external_test') : pauza_origin_badge('source', __('Новость проекта', 'pauza-rabotaet')); ?>
                        <p class="pauza-tag"><?php echo esc_html(trim(($news_type ? $news_type . ' · ' : '') . get_the_date())); ?></p>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if ($news_source) : ?>
                            <p class="pauza-muted-line"><?php echo esc_html($news_source); ?></p>
                        <?php endif; ?>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <div class="pauza-actions">
                            <?php echo pauza_internal_button(get_permalink(), __('Читать', 'pauza-rabotaet')); ?>
                            <?php echo pauza_smart_button($news_url, $news_button); ?>
                        </div>
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
