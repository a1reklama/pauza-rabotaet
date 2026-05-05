<?php
/**
 * Front page.
 *
 * @package PauzaRabotaet
 */

get_header();

$steps = pauza_steps_query(12);
$today = pauza_latest_today_query(1);
?>

<section class="pauza-home-hero">
    <div class="pauza-container pauza-home-hero__grid">
        <div class="pauza-home-hero__copy">
            <p class="pauza-eyebrow"><?php esc_html_e('12 шагов за 360 дней', 'pauza-rabotaet'); ?></p>
            <h1><?php esc_html_e('Начните спокойно. Сайт подскажет следующий шаг', 'pauza-rabotaet'); ?></h1>
            <p class="pauza-lead"><?php esc_html_e('Здесь не нужно разбираться во всем сразу. Выберите спонсора, откройте первый шаг и переходите дальше по простым кнопкам.', 'pauza-rabotaet'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Выбрать спонсора', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button(home_url('/calculator/'), __('Калькулятор', 'pauza-rabotaet')); ?>
            </div>
        </div>
        <div class="pauza-route" aria-label="<?php esc_attr_e('Маршрут новичка', 'pauza-rabotaet'); ?>">
            <div class="pauza-route__item">
                <span>1</span>
                <strong><?php esc_html_e('Написать спонсору', 'pauza-rabotaet'); ?></strong>
            </div>
            <div class="pauza-route__item">
                <span>2</span>
                <strong><?php esc_html_e('Начать 1 шаг', 'pauza-rabotaet'); ?></strong>
            </div>
            <div class="pauza-route__item">
                <span>3</span>
                <strong><?php esc_html_e('Открыть группу или бот', 'pauza-rabotaet'); ?></strong>
            </div>
        </div>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <p class="pauza-eyebrow"><?php esc_html_e('С чего начать', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Сделайте только первый понятный шаг', 'pauza-rabotaet'); ?></h2>
        </div>
        <div class="pauza-card-grid pauza-card-grid--three">
            <article class="pauza-card">
                <span class="pauza-card__number">01</span>
                <h3><?php esc_html_e('Выбрать спонсора', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Откройте список, выберите человека своего пола и сначала напишите сообщение.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Смотреть список', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">02</span>
                <h3><?php esc_html_e('Начать 1 шаг', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('На странице шага есть условия входа, задания и кнопки групп.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">03</span>
                <h3><?php esc_html_e('Вести калькулятор', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Результаты ежедневной работы отправляются спонсору или в группу.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/calculator/'), __('Открыть калькулятор', 'pauza-rabotaet')); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section pauza-section--muted">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <p class="pauza-eyebrow"><?php esc_html_e('Карта программы', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('12 шагов: открывайте только нужный шаг', 'pauza-rabotaet'); ?></h2>
        </div>
        <div class="pauza-step-map">
            <?php if ($steps->have_posts()) : ?>
                <?php while ($steps->have_posts()) : $steps->the_post(); ?>
                    <?php
                    $step_id = get_the_ID();
                    $number = pauza_meta($step_id, '_pauza_step_number');
                    $status = pauza_meta($step_id, '_pauza_step_status');
                    ?>
                    <a class="pauza-step-tile" href="<?php the_permalink(); ?>">
                        <span><?php echo esc_html($number); ?></span>
                        <strong><?php the_title(); ?></strong>
                        <?php if ($status) : ?>
                            <em><?php echo esc_html($status); ?></em>
                        <?php endif; ?>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
        <div class="pauza-section__footer">
            <?php echo pauza_internal_button(home_url('/12-shagov/'), __('Открыть всю карту', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
        </div>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container pauza-split">
        <div>
            <p class="pauza-eyebrow"><?php esc_html_e('Внешние инструменты', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Если нужна группа или бот, сайт даст кнопку', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Личные ответы не отправляются через сайт. Когда нужно перейти в Telegram, MAX или бот, вы увидите простую кнопку.', 'pauza-rabotaet'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_button(pauza_get_option('telegram_channel_url'), __('Видео в Telegram', 'pauza-rabotaet')); ?>
                <?php echo pauza_button(pauza_get_option('rutube_channel_url'), __('Rutube', 'pauza-rabotaet')); ?>
                <?php echo pauza_button(pauza_get_option('yandex_disk_url'), __('Яндекс.Диск', 'pauza-rabotaet')); ?>
            </div>
        </div>
        <aside class="pauza-panel">
            <h3><?php esc_html_e('Только сегодня', 'pauza-rabotaet'); ?></h3>
            <?php if ($today->have_posts()) : ?>
                <?php while ($today->have_posts()) : $today->the_post(); ?>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 26)); ?></p>
                    <?php echo pauza_internal_button(get_permalink(), __('Читать текст', 'pauza-rabotaet')); ?>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Владелец сможет добавлять тексты в админке.', 'pauza-rabotaet'); ?></p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php
get_footer();
