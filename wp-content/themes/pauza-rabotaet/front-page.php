<?php
/**
 * Front page.
 *
 * @package PauzaRabotaet
 */

get_header();

$steps = pauza_steps_query(12);
$today = pauza_latest_today_query(4);
?>

<section class="pauza-home-hero" id="start">
    <div class="pauza-container pauza-home-hero__grid">
        <div class="pauza-home-hero__copy">
            <h1><?php esc_html_e('12 шагов для ВСЕХ', 'pauza-rabotaet'); ?></h1>
            <p class="pauza-lead"><?php esc_html_e('Сначала выбери спонсора, потом посмотри, откуда быстрее скачиваются 360 видео, начинай смотреть по одному видео в день и пройди 12 шагов за 360 дней', 'pauza-rabotaet'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_internal_button('#sponsors', __('Выбрать спонсора', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                <?php echo pauza_internal_button('#materials', __('Открыть 360 видео', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button('#step-1', __('Начать 1 шаг', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button('#bot-4', __('Бот 4 шага', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button(pauza_calculator_url(), __('Калькулятор', 'pauza-rabotaet')); ?>
            </div>
        </div>
    </div>
</section>

<section class="pauza-section" id="sponsors">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php esc_html_e('Сначала выбери спонсора своего пола', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Этот блок идет до шагов, потому что выбор спонсора нужен до начала работы.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-next-box pauza-sponsor-consent">
            <div>
                <h3><?php esc_html_e('Сначала напиши, не звони', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Напиши сообщение в доступный мессенджер или SMS, представься и коротко расскажи о себе.', 'pauza-rabotaet'); ?></p>
            </div>
            <button class="pauza-button pauza-button--primary" type="button" data-sponsor-consent aria-expanded="false"><?php esc_html_e('Я прочитал и понимаю', 'pauza-rabotaet'); ?></button>
        </div>
        <div class="pauza-filter" role="group" aria-label="<?php esc_attr_e('Выбор списка спонсоров', 'pauza-rabotaet'); ?>" data-sponsor-controls hidden>
            <button type="button" data-sponsor-filter="female"><?php esc_html_e('Женщины', 'pauza-rabotaet'); ?></button>
            <button type="button" data-sponsor-filter="male"><?php esc_html_e('Мужчины', 'pauza-rabotaet'); ?></button>
        </div>
        <div class="pauza-sponsor-grid is-collapsed" data-sponsor-list data-nosnippet aria-live="polite" hidden></div>
    </div>
</section>

<section class="pauza-section pauza-section--muted" id="materials">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php esc_html_e('360 видео можно скачать здесь', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Этот блок стоит до первого шага, поэтому в маршруте он идет перед картой шагов.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-card-grid pauza-card-grid--three">
            <article class="pauza-card">
                <h3><?php esc_html_e('Telegram-канал 360 видео', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('ТЕЛЕГРАМ', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_button(pauza_get_option('telegram_channel_url'), __('Открыть', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </article>
            <article class="pauza-card">
                <h3><?php esc_html_e('Rutube-канал', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('RUTUBE', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_button(pauza_get_option('rutube_channel_url'), __('Открыть', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <h3><?php esc_html_e('Скачать видео', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('ЯНДЕКС ДИСК', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_button(pauza_get_option('yandex_disk_url'), __('Открыть', 'pauza-rabotaet')); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section" id="steps">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php esc_html_e('12 шагов для ВСЕХ', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Открыта только одна папка. Внутри каждого шага: работа по шагу и следующее действие.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-step-folders" id="full-step-folders">
            <?php if ($steps->have_posts()) : ?>
                <?php while ($steps->have_posts()) : $steps->the_post(); ?>
                    <?php
                    $step_id = get_the_ID();
                    $number = pauza_meta($step_id, '_pauza_step_number');
                    $full_text = pauza_meta($step_id, '_pauza_step_full_text');
                    pauza_render_step_folder($number, $full_text, '1' === (string) $number, '#sponsors');
                    ?>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Шаги пока не добавлены.', 'pauza-rabotaet'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="pauza-section pauza-section--muted" id="bot-4">
    <div class="pauza-container pauza-split">
        <div>
            <h2><?php esc_html_e('Бот 4 шага', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Внешний инструмент для работы по четвертому шагу. В основном маршруте он появляется после 3 шага, но теперь есть и быстрый вход с главной страницы.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-actions">
            <?php echo pauza_button(pauza_get_option('four_step_bot_url'), __('Открыть Telegram-бот', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            <?php echo pauza_button(pauza_get_option('four_step_max_bot_url'), __('Открыть MAX-бот', 'pauza-rabotaet')); ?>
        </div>
    </div>
</section>

<section class="pauza-section" id="calculator">
    <div class="pauza-container">
        <div class="pauza-content">
            <h2><?php esc_html_e('Калькулятор выздоровления', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Калькулятор открыт как отдельный веб-сервис. Сайт ведет на него и не хранит ответы пользователя.', 'pauza-rabotaet'); ?></p>
            <article class="pauza-card">
                <h3><?php esc_html_e('Открыть калькулятор', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Результат после заполнения отправляй спонсору или в группу текущего шага.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(pauza_calculator_url(), __('Открыть', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section pauza-section--muted" id="today">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php echo esc_html(sprintf(__('Только сегодня, %s', 'pauza-rabotaet'), date_i18n('j F Y'))); ?></h2>
        </div>
        <?php if ($today->have_posts()) : ?>
            <div class="pauza-card-grid pauza-card-grid--three">
                <?php while ($today->have_posts()) : $today->the_post(); ?>
                    <?php $parts = pauza_today_parts(get_post_field('post_content', get_the_ID())); ?>
                    <article class="pauza-card">
                        <h3><?php the_title(); ?></h3>
                        <?php if ($parts['question']) : ?>
                            <div class="pauza-qa pauza-qa--question">
                                <span class="pauza-qa__label"><?php esc_html_e('Вопрос', 'pauza-rabotaet'); ?></span>
                                <p><?php echo esc_html(wp_trim_words($parts['question'], 18, '...')); ?></p>
                            </div>
                        <?php endif; ?>
                        <details class="pauza-details">
                            <summary><?php esc_html_e('Читать', 'pauza-rabotaet'); ?></summary>
                            <div class="pauza-content pauza-qa-list">
                                <?php echo pauza_today_question_answer_html(get_post_field('post_content', get_the_ID()), false); ?>
                            </div>
                        </details>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <article class="pauza-card">
                <h3><?php esc_html_e('Текстов пока нет', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Владелец сможет добавить их в WordPress-админке.', 'pauza-rabotaet'); ?></p>
            </article>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
