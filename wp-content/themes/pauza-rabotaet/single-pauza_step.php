<?php
/**
 * Single step.
 *
 * @package PauzaRabotaet
 */

get_header();

while (have_posts()) :
    the_post();
    $step_id = get_the_ID();
    $number = pauza_meta($step_id, '_pauza_step_number');
    $status = pauza_meta($step_id, '_pauza_step_status');
    $goal = pauza_meta($step_id, '_pauza_step_goal');
    $requirements = pauza_lines(pauza_meta($step_id, '_pauza_step_requirements'));
    $tasks = pauza_lines(pauza_meta($step_id, '_pauza_step_tasks'));
    $full_text = pauza_meta($step_id, '_pauza_step_full_text');
    $telegram = pauza_meta($step_id, '_pauza_step_telegram_url');
    $max = pauza_meta($step_id, '_pauza_step_max_url');
    $video = pauza_meta($step_id, '_pauza_step_video_url');
    $next_label = pauza_meta($step_id, '_pauza_step_next_label');
    $next_url = pauza_meta($step_id, '_pauza_step_next_url');
    ?>
    <article>
        <section class="pauza-page-hero">
            <div class="pauza-container pauza-step-hero">
                <div>
                    <p class="pauza-eyebrow"><?php echo esc_html(sprintf(__('Шаг %s', 'pauza-rabotaet'), $number)); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <?php if ($goal) : ?>
                        <p class="pauza-lead"><?php echo esc_html($goal); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($status) : ?>
                    <div class="pauza-status"><?php echo esc_html($status); ?></div>
                <?php endif; ?>
            </div>
        </section>

        <section class="pauza-section">
            <div class="pauza-container pauza-simple-step">
                <div class="pauza-next-box">
                    <div>
                        <p class="pauza-eyebrow"><?php esc_html_e('Что сделать сейчас', 'pauza-rabotaet'); ?></p>
                        <h2><?php esc_html_e('Откройте обзор и двигайтесь по одному пункту', 'pauza-rabotaet'); ?></h2>
                    </div>
                    <div class="pauza-actions">
                        <?php echo pauza_button($telegram, __('Открыть Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                        <?php echo pauza_button($max, __('Открыть MAX', 'pauza-rabotaet')); ?>
                    </div>
                </div>

                <div class="pauza-tabs" data-pauza-tabs>
                    <div class="pauza-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Разделы шага', 'pauza-rabotaet'); ?>">
                        <button class="is-active" type="button" role="tab" id="pauza-tab-overview" aria-selected="true" aria-controls="pauza-panel-overview" data-tab-target="overview"><?php esc_html_e('Обзор', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-requirements" aria-selected="false" aria-controls="pauza-panel-requirements" data-tab-target="requirements"><?php esc_html_e('Условия', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-tasks" aria-selected="false" aria-controls="pauza-panel-tasks" data-tab-target="tasks"><?php esc_html_e('Что делать', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-full" aria-selected="false" aria-controls="pauza-panel-full" data-tab-target="full"><?php esc_html_e('Полный текст', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-next" aria-selected="false" aria-controls="pauza-panel-next" data-tab-target="next"><?php esc_html_e('Дальше', 'pauza-rabotaet'); ?></button>
                    </div>

                    <div class="pauza-tabs__panel is-active" role="tabpanel" id="pauza-panel-overview" aria-labelledby="pauza-tab-overview" data-tab-panel="overview">
                        <div class="pauza-content">
                            <h2><?php esc_html_e('Коротко о шаге', 'pauza-rabotaet'); ?></h2>
                            <?php the_content(); ?>
                            <div class="pauza-friendly-note">
                                <?php esc_html_e('Не нужно читать всё сразу. Начните с условий, потом откройте список действий.', 'pauza-rabotaet'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="pauza-tabs__panel" role="tabpanel" id="pauza-panel-requirements" aria-labelledby="pauza-tab-requirements" data-tab-panel="requirements" hidden>
                        <h2><?php esc_html_e('Перед началом', 'pauza-rabotaet'); ?></h2>
                        <?php if ($requirements) : ?>
                            <ul class="pauza-check-list">
                                <?php foreach ($requirements as $item) : ?>
                                    <li><?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p><?php esc_html_e('Особых условий для этого шага пока не указано.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="pauza-tabs__panel" role="tabpanel" id="pauza-panel-tasks" aria-labelledby="pauza-tab-tasks" data-tab-panel="tasks" hidden>
                        <h2><?php esc_html_e('Делайте по порядку', 'pauza-rabotaet'); ?></h2>
                        <?php if ($tasks) : ?>
                            <ol class="pauza-task-list">
                                <?php foreach ($tasks as $item) : ?>
                                    <li><?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else : ?>
                            <p><?php esc_html_e('Задания пока не добавлены.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="pauza-tabs__panel pauza-tabs__panel--long" role="tabpanel" id="pauza-panel-full" aria-labelledby="pauza-tab-full" data-tab-panel="full" hidden>
                        <h2><?php esc_html_e('Полный текст', 'pauza-rabotaet'); ?></h2>
                        <?php if ($full_text) : ?>
                            <?php pauza_render_plain_text($full_text); ?>
                        <?php else : ?>
                            <p><?php esc_html_e('Полный текст для этого шага пока не добавлен.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="pauza-tabs__panel" role="tabpanel" id="pauza-panel-next" aria-labelledby="pauza-tab-next" data-tab-panel="next" hidden>
                        <h2><?php esc_html_e('Когда закончите', 'pauza-rabotaet'); ?></h2>
                        <p><?php esc_html_e('Личные ответы и отчеты не отправляются через сайт. Используйте спонсора, группу или внешний бот.', 'pauza-rabotaet'); ?></p>
                        <div class="pauza-actions">
                            <?php echo pauza_button($telegram, __('Открыть группу Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                            <?php echo pauza_button($max, __('Открыть группу MAX', 'pauza-rabotaet')); ?>
                            <?php echo pauza_button($video, __('Открыть видео', 'pauza-rabotaet')); ?>
                            <?php echo $next_url && $next_label ? pauza_smart_button($next_url, $next_label, 'pauza-button pauza-button--accent') : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();
