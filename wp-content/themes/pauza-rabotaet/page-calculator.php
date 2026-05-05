<?php
/**
 * Calculator page.
 *
 * @package PauzaRabotaet
 */

get_header();

$intro = pauza_get_option('calculator_intro');
$instruction_url = pauza_get_option('calculator_instruction_url');
$calculator_telegram_url = pauza_get_option('calculator_telegram_url');
$calculator_max_url = pauza_get_option('calculator_max_url');
?>

<section class="pauza-page-hero">
    <div class="pauza-container">
        <p class="pauza-eyebrow"><?php esc_html_e('Внешние инструменты', 'pauza-rabotaet'); ?></p>
        <h1><?php esc_html_e('Калькуляторы', 'pauza-rabotaet'); ?></h1>
        <p class="pauza-lead"><?php echo esc_html($intro); ?></p>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container pauza-content-grid">
        <div class="pauza-content">
            <div class="pauza-card-grid">
                <article class="pauza-card">
                    <p class="pauza-tag"><?php esc_html_e('Telegram', 'pauza-rabotaet'); ?></p>
                    <h2><?php esc_html_e('Калькулятор в Telegram', 'pauza-rabotaet'); ?></h2>
                    <p><?php esc_html_e('Откройте бот, заполните данные и отправьте результат спонсору или в группу шага.', 'pauza-rabotaet'); ?></p>
                    <?php echo pauza_button($calculator_telegram_url, __('Открыть Telegram-бот', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                </article>
                <article class="pauza-card">
                    <p class="pauza-tag"><?php esc_html_e('MAX', 'pauza-rabotaet'); ?></p>
                    <h2><?php esc_html_e('Калькулятор в MAX', 'pauza-rabotaet'); ?></h2>
                    <p><?php esc_html_e('Если удобнее MAX, используйте отдельную ссылку на бот калькулятора.', 'pauza-rabotaet'); ?></p>
                    <?php echo pauza_button($calculator_max_url, __('Открыть MAX-бот', 'pauza-rabotaet')); ?>
                </article>
            </div>

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
                <?php echo pauza_button($instruction_url, __('Открыть инструкцию', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </div>
        </aside>
    </div>
</section>

<?php
get_footer();
