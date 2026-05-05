<?php
/**
 * Bot transition page.
 *
 * @package PauzaRabotaet
 */

get_header();

$bot_url = pauza_get_option('four_step_bot_url', 'https://t.me/FourStepForAllBot');
?>

<section class="pauza-page-hero">
    <div class="pauza-container">
        <p class="pauza-eyebrow"><?php esc_html_e('Внешний инструмент', 'pauza-rabotaet'); ?></p>
        <h1><?php the_title(); ?></h1>
        <p class="pauza-lead"><?php esc_html_e('Четвертый шаг не переносим в сайт на первом запуске. Работа идет во внешнем Telegram-боте, а сайт только объясняет момент перехода.', 'pauza-rabotaet'); ?></p>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container pauza-content-grid">
        <div class="pauza-content">
            <h2><?php esc_html_e('Когда переходить', 'pauza-rabotaet'); ?></h2>
            <ol class="pauza-task-list">
                <li><?php esc_html_e('Выбран спонсор или проводник.', 'pauza-rabotaet'); ?></li>
                <li><?php esc_html_e('Первые три шага завершены и прочитаны/согласованы.', 'pauza-rabotaet'); ?></li>
                <li><?php esc_html_e('Есть готовность выполнять письменную работу без публикации личных ответов на сайте.', 'pauza-rabotaet'); ?></li>
            </ol>

            <details class="pauza-details" open>
                <summary><?php esc_html_e('Что остается на сайте', 'pauza-rabotaet'); ?></summary>
                <p><?php esc_html_e('На сайте остается навигация, объяснение маршрута и ссылки. Ответы, проверки и рабочая логика четвертого шага остаются в боте или у спонсора.', 'pauza-rabotaet'); ?></p>
            </details>
        </div>

        <aside class="pauza-sidebar">
            <div class="pauza-panel">
                <h2><?php esc_html_e('Открыть бот', 'pauza-rabotaet'); ?></h2>
                <p><?php esc_html_e('Кнопка ведет во внешний Telegram-бот. Сайт не получает и не хранит ответы пользователя.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_button($bot_url, __('Перейти в бот 4 шага', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </div>
        </aside>
    </div>
</section>

<?php
get_footer();

