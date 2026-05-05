<?php
/**
 * Calculator page.
 *
 * @package PauzaRabotaet
 */

get_header();

$embed = pauza_get_option('calculator_embed');
$intro = pauza_get_option('calculator_intro');
$instruction_url = pauza_get_option('calculator_instruction_url');
?>

<section class="pauza-page-hero">
    <div class="pauza-container">
        <p class="pauza-eyebrow"><?php esc_html_e('Ежедневная практика', 'pauza-rabotaet'); ?></p>
        <h1><?php the_title(); ?></h1>
        <p class="pauza-lead"><?php echo esc_html($intro); ?></p>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container pauza-content-grid">
        <div class="pauza-content">
            <?php if ($embed) : ?>
                <div class="pauza-embed">
                    <?php echo do_shortcode($embed); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php else : ?>
                <div class="pauza-empty">
                    <h2><?php esc_html_e('Готовый калькулятор подключается в админке', 'pauza-rabotaet'); ?></h2>
                    <p><?php esc_html_e('Вставьте shortcode или HTML готового калькулятора в разделе "Пауза работает" → настройки. Пока здесь отображается понятная заглушка для MVP.', 'pauza-rabotaet'); ?></p>
                </div>
            <?php endif; ?>

            <?php while (have_posts()) : the_post(); ?>
                <?php if (trim(get_the_content())) : ?>
                    <div class="pauza-wp-content">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>

        <aside class="pauza-sidebar">
            <div class="pauza-panel">
                <h2><?php esc_html_e('Как использовать', 'pauza-rabotaet'); ?></h2>
                <ol class="pauza-task-list">
                    <li><?php esc_html_e('Заполнять каждый день.', 'pauza-rabotaet'); ?></li>
                    <li><?php esc_html_e('Смотреть итоговую цифру по сферам.', 'pauza-rabotaet'); ?></li>
                    <li><?php esc_html_e('Отправлять результат спонсору или в группу текущего шага.', 'pauza-rabotaet'); ?></li>
                </ol>
                <?php echo pauza_button($instruction_url, __('Инструкция к калькулятору', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </div>
        </aside>
    </div>
</section>

<?php
get_footer();

