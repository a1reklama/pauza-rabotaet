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
    $full_text = pauza_meta($step_id, '_pauza_step_full_text');
    $telegram = pauza_meta($step_id, '_pauza_step_telegram_url');
    $max = pauza_meta($step_id, '_pauza_step_max_url');
    $source_work = pauza_step_numbered_lines($full_text);
    $source_transition = pauza_step_transition_lines($full_text, (int) $number);
    $telegram_intro_class = '1' === (string) $number ? 'pauza-button' : 'pauza-button pauza-button--primary';
    ?>
    <article>
        <section class="pauza-page-hero">
            <div class="pauza-container pauza-step-hero">
                <div class="pauza-step-hero__icon">
                    <?php echo pauza_step_icon_html($number); ?>
                </div>
                <div>
                    <p class="pauza-eyebrow"><?php echo esc_html(sprintf(__('Шаг %s', 'pauza-rabotaet'), $number)); ?></p>
                    <h1><?php echo esc_html(pauza_step_display_title($number)); ?></h1>
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
                        <h2><?php esc_html_e('Сначала открой работу по шагу', 'pauza-rabotaet'); ?></h2>
                        <p><?php esc_html_e('Ниже идут пункты работы по шагу. Двигайся по ним последовательно и открывай ссылки по мере прохождения.', 'pauza-rabotaet'); ?></p>
                        <?php if ('1' === (string) $number) : ?>
                            <p class="pauza-muted"><?php esc_html_e('Если спонсор еще не выбран, сначала открой список спонсоров.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="pauza-actions">
                        <?php if ('1' === (string) $number) : ?>
                            <?php echo pauza_internal_button(home_url('/sponsory/'), __('Выбрать спонсора', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                        <?php endif; ?>
                        <?php echo pauza_button($telegram, __('Открыть Telegram', 'pauza-rabotaet'), $telegram_intro_class); ?>
                        <?php echo pauza_button($max, __('Открыть MAX', 'pauza-rabotaet')); ?>
                    </div>
                </div>

                <div class="pauza-panel">
                    <div class="pauza-content">
                        <h2><?php esc_html_e('Работа по шагу', 'pauza-rabotaet'); ?> <?php echo $source_work ? pauza_origin_badge('source') : pauza_origin_badge('editorial'); ?></h2>
                        <?php if ($source_work) : ?>
                            <?php pauza_render_source_list($source_work); ?>
                        <?php elseif ($tasks) : ?>
                            <?php pauza_render_source_list($tasks); ?>
                        <?php elseif ($full_text) : ?>
                            <?php pauza_render_source_list(pauza_lines($full_text), 'ul'); ?>
                        <?php else : ?>
                            <p><?php esc_html_e('Пункты работы пока не добавлены.', 'pauza-rabotaet'); ?> <?php echo pauza_origin_badge('verify'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pauza-next-box">
                    <div>
                        <h2><?php esc_html_e('После выполнения шага', 'pauza-rabotaet'); ?></h2>
                        <?php if ($source_transition) : ?>
                            <?php pauza_render_source_list($source_transition, 'ul'); ?>
                        <?php else : ?>
                            <p><?php esc_html_e('Переход для этого шага пока не указан.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </article>
<?php endwhile; ?>

<?php
get_footer();
