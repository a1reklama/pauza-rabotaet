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

<section class="pauza-home-hero">
    <div class="pauza-container pauza-home-hero__grid">
        <div class="pauza-home-hero__copy">
            <h1><?php esc_html_e('12 шагов для ВСЕХ', 'pauza-rabotaet'); ?></h1>
            <p class="pauza-lead"><?php esc_html_e('Сначала выбери спонсора, потом посмотри, откуда быстрее скачиваются 360 видео, начинай смотреть по одному видео в день и пройди 12 шагов за 360 дней', 'pauza-rabotaet'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Выбрать спонсора', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                <?php echo pauza_internal_button(home_url('/materialy/'), __('Открыть 360 видео', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button(home_url('/bot-4-shaga/'), __('Бот 4 шага', 'pauza-rabotaet')); ?>
                <?php echo pauza_internal_button(pauza_calculator_url(), __('Калькулятор', 'pauza-rabotaet')); ?>
            </div>
        </div>
    </div>
</section>

<section class="pauza-section pauza-section--muted">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <p class="pauza-eyebrow"><?php esc_html_e('С чего начать', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Один понятный маршрут без лишних разделов', 'pauza-rabotaet'); ?></h2>
        </div>
        <div class="pauza-card-grid pauza-card-grid--three">
            <article class="pauza-card">
                <span class="pauza-card__number">01</span>
                <h3><?php esc_html_e('Выбрать спонсора', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Открой список, выбери свой пол и сначала напиши короткое сообщение. Не звони без предварительной переписки.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Смотреть список', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">02</span>
                <h3><?php esc_html_e('Открыть 360 видео', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Перед первым шагом указаны Telegram, Rutube и Яндекс.Диск с видео. Показываем их до карты шагов.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/materialy/'), __('Открыть материалы', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">03</span>
                <h3><?php esc_html_e('Начать 1 шаг', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('На странице шага сначала видны пункты работы. Длинный текст руководителя открыт отдельной вкладкой.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">04</span>
                <h3><?php esc_html_e('Идти по переходу шага', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Группы, боты и калькуляторы показываются внутри того шага, где они действительно нужны.', 'pauza-rabotaet'); ?></p>
                <?php echo pauza_internal_button(home_url('/12-shagov/'), __('Открыть карту шагов', 'pauza-rabotaet')); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php esc_html_e('360 видео можно скачать здесь', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Этот блок стоит перед шагами, потому что он нужен до первого шага.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-card-grid pauza-card-grid--three">
            <article class="pauza-card">
                <h3><?php esc_html_e('Telegram-канал 360 видео', 'pauza-rabotaet'); ?></h3>
                <?php echo pauza_button(pauza_get_option('telegram_channel_url'), __('Открыть Telegram-канал 360 видео', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            </article>
            <article class="pauza-card">
                <h3><?php esc_html_e('Rutube-канал', 'pauza-rabotaet'); ?></h3>
                <?php echo pauza_button(pauza_get_option('rutube_channel_url'), __('Открыть Rutube-канал', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <h3><?php esc_html_e('Скачать видео', 'pauza-rabotaet'); ?></h3>
                <?php echo pauza_button(pauza_get_option('yandex_disk_url'), __('Открыть Яндекс.Диск', 'pauza-rabotaet')); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section pauza-section--muted" id="steps">
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
                        <strong><?php echo esc_html(pauza_step_display_title($number)); ?></strong>
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

<section class="pauza-section pauza-section--muted" id="services">
    <div class="pauza-container pauza-split">
        <div>
            <p class="pauza-eyebrow"><?php esc_html_e('Быстрые ссылки', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Бот 4 шага и калькулятор', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('Это внешние инструменты. Сайт дает быстрый вход, но не хранит ответы и не заменяет работу со спонсором.', 'pauza-rabotaet'); ?></p>
        </div>
        <div class="pauza-actions">
            <?php echo pauza_internal_button(home_url('/bot-4-shaga/'), __('Бот 4 шага', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
            <?php echo pauza_internal_button(pauza_calculator_url(), __('Калькулятор', 'pauza-rabotaet')); ?>
        </div>
    </div>
</section>

<section class="pauza-section" id="today">
    <div class="pauza-container">
        <div class="pauza-section__heading">
            <h2><?php echo esc_html(sprintf(__('Только сегодня, %s', 'pauza-rabotaet'), date_i18n('j F Y'))); ?></h2>
            <p><?php esc_html_e('Тексты редактируются в WordPress-админке в разделе «Только сегодня».', 'pauza-rabotaet'); ?></p>
        </div>
        <?php if ($today->have_posts()) : ?>
            <div class="pauza-card-grid">
                <?php while ($today->have_posts()) : $today->the_post(); ?>
                    <article class="pauza-card">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php echo pauza_today_question_answer_html(get_post_field('post_content', get_the_ID()), true); ?>
                        <?php echo pauza_internal_button(get_permalink(), __('Читать', 'pauza-rabotaet')); ?>
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
