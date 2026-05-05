<?php
/**
 * Steps archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('12 шагов за 360 дней', 'Карта программы: у каждого шага есть цель, условия входа, чек-лист и кнопки внешних групп.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <?php if (have_posts()) : ?>
            <div class="pauza-step-list">
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $step_id = get_the_ID();
                    $number = pauza_meta($step_id, '_pauza_step_number');
                    $goal = pauza_meta($step_id, '_pauza_step_goal');
                    $status = pauza_meta($step_id, '_pauza_step_status');
                    ?>
                    <article class="pauza-step-row">
                        <a class="pauza-step-row__number" href="<?php the_permalink(); ?>"><?php echo esc_html($number); ?></a>
                        <div>
                            <?php if ($status) : ?>
                                <p class="pauza-tag"><?php echo esc_html($status); ?></p>
                            <?php endif; ?>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p><?php echo esc_html($goal ?: get_the_excerpt()); ?></p>
                        </div>
                        <div class="pauza-step-row__action">
                            <?php echo pauza_internal_button(get_permalink(), __('Открыть', 'pauza-rabotaet')); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('Шаги пока не добавлены.', 'pauza-rabotaet'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

