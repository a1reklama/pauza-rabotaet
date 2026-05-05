<?php
/**
 * Front page.
 *
 * @package PauzaRabotaet
 */

get_header();

$steps = pauza_steps_query(12);
$today = pauza_latest_today_query(1);
$news = pauza_latest_news_query(2);
?>

<section class="pauza-home-hero">
    <div class="pauza-container pauza-home-hero__grid">
        <div class="pauza-home-hero__copy">
            <p class="pauza-eyebrow"><?php esc_html_e('12 шагов за 360 дней', 'pauza-rabotaet'); ?></p>
            <h1><?php esc_html_e('Пауза работает', 'pauza-rabotaet'); ?></h1>
            <p class="pauza-lead"><?php esc_html_e('Выберите спонсора и начните первый шаг. Остальные материалы открывайте только тогда, когда они нужны по шагу.', 'pauza-rabotaet'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Выбрать спонсора', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
            </div>
        </div>
        <div class="pauza-route" aria-label="<?php esc_attr_e('Маршрут новичка', 'pauza-rabotaet'); ?>">
            <div class="pauza-route__item">
                <span>1</span>
                <strong><?php esc_html_e('Выбрать спонсора', 'pauza-rabotaet'); ?></strong>
            </div>
            <div class="pauza-route__item">
                <span>2</span>
                <strong><?php esc_html_e('Начать 1 шаг', 'pauza-rabotaet'); ?></strong>
            </div>
            <div class="pauza-route__item">
                <span>3</span>
                <strong><?php esc_html_e('Открыть группу или инструмент шага', 'pauza-rabotaet'); ?></strong>
            </div>
        </div>
    </div>
</section>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-source-legend">
            <strong><?php esc_html_e('Метки:', 'pauza-rabotaet'); ?></strong>
            <?php echo pauza_origin_badge('source'); ?>
            <?php echo pauza_origin_badge('editorial'); ?>
            <?php echo pauza_origin_badge('external_test'); ?>
            <?php echo pauza_origin_badge('verify'); ?>
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
                <p><?php esc_html_e('Откройте список, выберите свой пол и сначала напишите короткое сообщение. Не звоните без предварительной переписки.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                <?php echo pauza_internal_button(home_url('/sponsory/'), __('Смотреть список', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">02</span>
                <h3><?php esc_html_e('Начать 1 шаг', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('На странице шага сначала видны пункты работы из DOCX. Длинный текст руководителя открыт отдельной вкладкой.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Начать 1 шаг', 'pauza-rabotaet')); ?>
            </article>
            <article class="pauza-card">
                <span class="pauza-card__number">03</span>
                <h3><?php esc_html_e('Открыть группу или инструмент', 'pauza-rabotaet'); ?></h3>
                <p><?php esc_html_e('Группы, боты и калькуляторы показываются внутри того шага, где они действительно нужны.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                <?php echo pauza_internal_button(home_url('/12-shagov/'), __('Открыть карту шагов', 'pauza-rabotaet')); ?>
            </article>
        </div>
    </div>
</section>

<section class="pauza-section" id="steps">
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

<section class="pauza-section pauza-section--muted">
    <div class="pauza-container pauza-split">
        <div>
            <p class="pauza-eyebrow"><?php esc_html_e('Дополнительные сервисы', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Калькуляторы не стоят в начале маршрута', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('По DOCX калькулятор появляется в первом шаге: инструкция, ежедневная работа и отправка результата спонсору или в группу. Отдельная страница остается только справочным входом к внешним ботам.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
            <div class="pauza-actions">
                <?php echo pauza_internal_button(home_url('/12-shagov/pervyy-shag/'), __('Открыть 1 шаг', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                <?php echo pauza_internal_button(home_url('/calculator/'), __('Калькуляторы', 'pauza-rabotaet')); ?>
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

<section class="pauza-section">
    <div class="pauza-container pauza-split">
        <div>
            <p class="pauza-eyebrow"><?php esc_html_e('Новости', 'pauza-rabotaet'); ?></p>
            <h2><?php esc_html_e('Новости отдельно от пути новичка', 'pauza-rabotaet'); ?></h2>
            <p><?php esc_html_e('В этом блоке можно публиковать объявления проекта. Сейчас для проверки формата показаны внешние тематические новости с синей меткой.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
            <?php echo pauza_internal_button(home_url('/novosti/'), __('Открыть новости', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
        </div>
        <div class="pauza-card-grid">
            <?php if ($news->have_posts()) : ?>
                <?php while ($news->have_posts()) : $news->the_post(); ?>
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
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ($news_source) : ?>
                            <p class="pauza-muted-line"><?php echo esc_html($news_source); ?></p>
                        <?php endif; ?>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 18)); ?></p>
                        <?php echo pauza_smart_button($news_url, $news_button); ?>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <article class="pauza-card">
                    <h3><?php esc_html_e('Новостей пока нет', 'pauza-rabotaet'); ?></h3>
                    <p><?php esc_html_e('Владелец сможет добавить их в админке.', 'pauza-rabotaet'); ?></p>
                </article>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
