<?php
/**
 * Calculator page.
 *
 * @package PauzaRabotaet
 */

get_header();

$instruction_url = pauza_get_option('calculator_instruction_url');
?>

<section class="pauza-page-hero">
    <div class="pauza-container">
        <h1><?php esc_html_e('Калькулятор выздоровления', 'pauza-rabotaet'); ?></h1>
        <p class="pauza-lead"><?php esc_html_e('Калькулятор открыт как отдельный веб-сервис. Сайт ведет на него и не хранит ответы пользователя.', 'pauza-rabotaet'); ?></p>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container pauza-content-grid">
        <div class="pauza-content">
            <div class="pauza-card-grid">
                <article class="pauza-card">
                    <h2><?php esc_html_e('Открыть калькулятор', 'pauza-rabotaet'); ?></h2>
                    <p><?php esc_html_e('Используйте один внешний веб-сервис калькулятора. Результат после заполнения отправляйте спонсору или в группу текущего шага.', 'pauza-rabotaet'); ?></p>
                    <?php echo pauza_internal_button(pauza_calculator_url(), __('Открыть', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
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
