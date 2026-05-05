<?php
/**
 * Single material.
 *
 * @package PauzaRabotaet
 */

get_header();

while (have_posts()) :
    the_post();
    $url = pauza_meta(get_the_ID(), '_pauza_material_url');
    $label = pauza_meta(get_the_ID(), '_pauza_material_button_label', __('Открыть', 'pauza-rabotaet'));
    ?>
    <section class="pauza-page-hero">
        <div class="pauza-container">
            <p class="pauza-eyebrow"><?php esc_html_e('Материал', 'pauza-rabotaet'); ?></p>
            <h1><?php the_title(); ?></h1>
        </div>
    </section>
    <section class="pauza-section">
        <div class="pauza-container pauza-narrow">
            <div class="pauza-content">
                <?php the_content(); ?>
                <?php echo pauza_button($url, $label, 'pauza-button pauza-button--primary'); ?>
            </div>
        </div>
    </section>
<?php endwhile; ?>

<?php
get_footer();

