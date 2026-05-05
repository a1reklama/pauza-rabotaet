<?php
/**
 * Single sponsor.
 *
 * @package PauzaRabotaet
 */

get_header();

while (have_posts()) :
    the_post();
    $sponsor_id = get_the_ID();
    $gender = pauza_meta($sponsor_id, '_pauza_sponsor_gender', 'female');
    $phone = pauza_meta($sponsor_id, '_pauza_sponsor_phone');
    ?>
    <section class="pauza-page-hero">
        <div class="pauza-container">
            <p class="pauza-eyebrow"><?php echo 'male' === $gender ? esc_html__('Мужчины', 'pauza-rabotaet') : esc_html__('Женщины', 'pauza-rabotaet'); ?></p>
            <h1><?php the_title(); ?></h1>
            <p class="pauza-lead"><?php esc_html_e('Сначала напишите сообщение, представьтесь и коротко расскажите о себе.', 'pauza-rabotaet'); ?></p>
        </div>
    </section>
    <section class="pauza-section">
        <div class="pauza-container pauza-narrow">
            <div class="pauza-panel">
                <?php the_content(); ?>
                <?php if ($phone) : ?>
                    <p class="pauza-phone"><?php echo esc_html($phone); ?></p>
                <?php endif; ?>
                <p class="pauza-muted-line"><?php esc_html_e('Пишите вручную с телефона или из мессенджера. Звонить без предварительной переписки не нужно.', 'pauza-rabotaet'); ?></p>
            </div>
        </div>
    </section>
<?php endwhile; ?>

<?php
get_footer();
