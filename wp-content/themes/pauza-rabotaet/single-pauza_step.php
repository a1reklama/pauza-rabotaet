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
    $tasks = pauza_lines(pauza_meta($step_id, '_pauza_step_tasks'));
    $materials = pauza_lines(pauza_meta($step_id, '_pauza_step_materials'));
    $full_text = pauza_meta($step_id, '_pauza_step_full_text');
    $telegram = pauza_meta($step_id, '_pauza_step_telegram_url');
    $max = pauza_meta($step_id, '_pauza_step_max_url');
    $video = pauza_meta($step_id, '_pauza_step_video_url');
    $next_label = pauza_meta($step_id, '_pauza_step_next_label');
    $next_url = pauza_meta($step_id, '_pauza_step_next_url');
    $source_work = pauza_step_numbered_lines($full_text);
    $source_materials = pauza_step_material_lines($full_text);
    $calculator_instruction = pauza_get_option('calculator_instruction_url');
    $calculator_telegram = pauza_get_option('calculator_telegram_url');
    $calculator_max = pauza_get_option('calculator_max_url');
    ?>
    <article>
        <section class="pauza-page-hero">
            <div class="pauza-container pauza-step-hero">
                <div class="pauza-step-hero__icon">
                    <?php echo pauza_step_icon_html($number); ?>
                </div>
                <div>
                    <p class="pauza-eyebrow"><?php echo esc_html(sprintf(__('Шаг %s', 'pauza-rabotaet'), $number)); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <?php if ($goal) : ?>
                        <p class="pauza-lead"><?php echo esc_html($goal); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
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
                        <p class="pauza-eyebrow"><?php esc_html_e('Работа по документу', 'pauza-rabotaet'); ?></p>
                        <h2><?php esc_html_e('Сначала откройте работу по шагу', 'pauza-rabotaet'); ?></h2>
                        <p><?php esc_html_e('Ниже фактические пункты из DOCX. Подсказки интерфейса отдельно помечены цветом.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                    </div>
                    <div class="pauza-actions">
                        <?php echo pauza_button($telegram, __('Открыть Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                        <?php echo pauza_button($max, __('Открыть MAX', 'pauza-rabotaet')); ?>
                    </div>
                </div>

                <div class="pauza-tabs" data-pauza-tabs>
                    <div class="pauza-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Разделы шага', 'pauza-rabotaet'); ?>">
                        <button class="is-active" type="button" role="tab" id="pauza-tab-work" aria-selected="true" aria-controls="pauza-panel-work" data-tab-target="work"><?php esc_html_e('Работа по шагу', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-materials" aria-selected="false" aria-controls="pauza-panel-materials" data-tab-target="materials"><?php esc_html_e('Материалы шага', 'pauza-rabotaet'); ?></button>
                        <button type="button" role="tab" id="pauza-tab-source" aria-selected="false" aria-controls="pauza-panel-source" data-tab-target="source"><?php esc_html_e('Текст руководителя', 'pauza-rabotaet'); ?></button>
                    </div>

                    <div class="pauza-tabs__panel is-active" role="tabpanel" id="pauza-panel-work" aria-labelledby="pauza-tab-work" data-tab-panel="work">
                        <div class="pauza-content">
                            <h2><?php esc_html_e('Работа по шагу', 'pauza-rabotaet'); ?> <?php echo $source_work ? pauza_origin_badge('source') : pauza_origin_badge('editorial'); ?></h2>
                            <?php if ($source_work) : ?>
                                <?php pauza_render_source_list($source_work); ?>
                            <?php elseif ($tasks) : ?>
                                <?php pauza_render_source_list($tasks); ?>
                            <?php else : ?>
                                <p><?php esc_html_e('Пункты работы пока не добавлены.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('verify'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="pauza-tabs__panel" role="tabpanel" id="pauza-panel-materials" aria-labelledby="pauza-tab-materials" data-tab-panel="materials" hidden>
                        <div class="pauza-content">
                            <h2><?php esc_html_e('Материалы шага', 'pauza-rabotaet'); ?> <?php echo $source_materials ? pauza_origin_badge('source') : pauza_origin_badge('verify'); ?></h2>
                            <?php if ($source_materials) : ?>
                                <?php pauza_render_source_list($source_materials, 'ul'); ?>
                            <?php elseif ($materials) : ?>
                                <?php pauza_render_source_list($materials, 'ul'); ?>
                            <?php else : ?>
                                <p><?php esc_html_e('Материалы для этого шага пока не указаны.', 'pauza-rabotaet'); ?></p>
                            <?php endif; ?>

                            <?php if ('1' === (string) $number) : ?>
                                <div class="pauza-source-card">
                                    <h3><?php esc_html_e('Калькулятор выздоровления', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('source'); ?></h3>
                                    <p><?php esc_html_e('В DOCX калькулятор указан внутри первого шага: сначала инструкция, затем ежедневная работа и отправка результата спонсору или в группу.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                                    <div class="pauza-actions">
                                        <?php echo pauza_button($calculator_instruction, __('Инструкция к калькулятору', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                                        <?php echo pauza_button($calculator_telegram, __('Открыть Telegram-бот', 'pauza-rabotaet')); ?>
                                        <?php echo pauza_button($calculator_max, __('Открыть MAX-бот', 'pauza-rabotaet')); ?>
                                        <?php if (!$calculator_instruction && !$calculator_telegram && !$calculator_max) : ?>
                                            <?php echo pauza_origin_badge('verify', __('Ссылки на калькулятор нужно подтвердить', 'pauza-rabotaet')); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ('4' === (string) $number) : ?>
                                <div class="pauza-source-card">
                                    <h3><?php esc_html_e('Бот 4 шага', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('source'); ?></h3>
                                    <p><?php esc_html_e('Этот бот показывается как инструмент конкретного четвертого шага, а не как общий пункт сайта.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                                    <div class="pauza-actions">
                                        <?php echo pauza_button(pauza_get_option('four_step_bot_url'), __('Открыть бот 4 шага', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="pauza-actions">
                                <?php echo pauza_button($telegram, __('Группа Telegram этого шага', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                                <?php echo pauza_button($max, __('Группа MAX этого шага', 'pauza-rabotaet')); ?>
                                <?php echo pauza_button($video, __('Открыть видео', 'pauza-rabotaet')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="pauza-tabs__panel pauza-tabs__panel--long" role="tabpanel" id="pauza-panel-source" aria-labelledby="pauza-tab-source" data-tab-panel="source" hidden>
                        <div class="pauza-content">
                            <h2><?php esc_html_e('Текст руководителя', 'pauza-rabotaet'); ?> <?php echo $full_text ? pauza_origin_badge('source') : pauza_origin_badge('verify'); ?></h2>
                            <?php if ($full_text && '8' === (string) $number) : ?>
                                <?php pauza_render_step_source_sections($full_text); ?>
                            <?php elseif ($full_text) : ?>
                                <?php pauza_render_step_source_sections($full_text); ?>
                            <?php else : ?>
                                <p><?php esc_html_e('Исходный текст для этого шага пока не добавлен.', 'pauza-rabotaet'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="pauza-next-box">
                    <div>
                        <p class="pauza-eyebrow"><?php esc_html_e('Дальше', 'pauza-rabotaet'); ?></p>
                        <h2><?php esc_html_e('После выполнения шага', 'pauza-rabotaet'); ?></h2>
                        <p><?php esc_html_e('Ответы и отчеты не отправляются через сайт. Используйте спонсора, группу или внешний бот, если он указан в этом шаге.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('editorial'); ?></p>
                    </div>
                    <div class="pauza-actions">
                        <?php echo $next_url && $next_label ? pauza_smart_button($next_url, $next_label, 'pauza-button pauza-button--accent') : ''; ?>
                        <?php echo pauza_button($telegram, __('Открыть группу Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                        <?php echo pauza_button($max, __('Открыть группу MAX', 'pauza-rabotaet')); ?>
                    </div>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();
