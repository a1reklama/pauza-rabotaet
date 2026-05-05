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
    $telegram = pauza_meta($sponsor_id, '_pauza_sponsor_telegram_url');
    $whatsapp = pauza_meta($sponsor_id, '_pauza_sponsor_whatsapp_url');
    $max = pauza_meta($sponsor_id, '_pauza_sponsor_max_url');
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
                <div class="pauza-actions">
                    <?php echo pauza_button($telegram, __('Написать в Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                    <?php echo pauza_button($whatsapp, __('Написать в WhatsApp', 'pauza-rabotaet')); ?>
                    <?php echo pauza_button($max, __('Написать в MAX', 'pauza-rabotaet')); ?>
                    <?php echo $phone ? pauza_button(pauza_sms_link($phone), __('Написать SMS', 'pauza-rabotaet')) : ''; ?>
                </div>
            </div>
        </div>
    </section>
<?php endwhile; ?>

<?php
get_footer();

