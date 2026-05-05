<?php
/**
 * 404 template.
 *
 * @package PauzaRabotaet
 */

get_header();
?>

<section class="pauza-page-hero">
    <div class="pauza-container">
        <p class="pauza-eyebrow"><?php esc_html_e('404', 'pauza-rabotaet'); ?></p>
        <h1><?php esc_html_e('Страница не найдена', 'pauza-rabotaet'); ?></h1>
        <p class="pauza-lead"><?php esc_html_e('Лучше вернуться к карте программы или выбрать спонсора.', 'pauza-rabotaet'); ?></p>
        <div class="pauza-actions">
            <?php echo pauza_internal_button(home_url('/12-shagov/'), __('Открыть 12 шагов', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            <?php echo pauza_internal_button(home_url('/sponsory/'), __('Выбрать спонсора', 'pauza-rabotaet')); ?>
        </div>
    </div>
</section>

<?php
get_footer();

