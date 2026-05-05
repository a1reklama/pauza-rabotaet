<?php
/**
 * Sponsors archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('Спонсоры и проводники', 'Выберите человека своего пола и сначала напишите сообщение. Публично видны только опубликованные контакты.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-notice">
            <?php echo esc_html(pauza_get_option('privacy_notice')); ?>
        </div>

        <div class="pauza-filter" role="group" aria-label="<?php esc_attr_e('Фильтр спонсоров', 'pauza-rabotaet'); ?>">
            <button class="is-active" type="button" data-sponsor-filter="all"><?php esc_html_e('Все', 'pauza-rabotaet'); ?></button>
            <button type="button" data-sponsor-filter="female"><?php esc_html_e('Женщины', 'pauza-rabotaet'); ?></button>
            <button type="button" data-sponsor-filter="male"><?php esc_html_e('Мужчины', 'pauza-rabotaet'); ?></button>
        </div>

        <?php if (have_posts()) : ?>
            <div class="pauza-sponsor-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $sponsor_id = get_the_ID();
                    $gender = pauza_meta($sponsor_id, '_pauza_sponsor_gender', 'female');
                    $phone = pauza_meta($sponsor_id, '_pauza_sponsor_phone');
                    $telegram = pauza_meta($sponsor_id, '_pauza_sponsor_telegram_url');
                    $whatsapp = pauza_meta($sponsor_id, '_pauza_sponsor_whatsapp_url');
                    $max = pauza_meta($sponsor_id, '_pauza_sponsor_max_url');
                    $note = pauza_meta($sponsor_id, '_pauza_sponsor_note');
                    ?>
                    <article class="pauza-sponsor-card" data-sponsor-gender="<?php echo esc_attr($gender); ?>">
                        <p class="pauza-tag"><?php echo 'male' === $gender ? esc_html__('Мужчины', 'pauza-rabotaet') : esc_html__('Женщины', 'pauza-rabotaet'); ?></p>
                        <h2><?php the_title(); ?></h2>
                        <?php if ($note) : ?>
                            <p><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                        <div class="pauza-actions pauza-actions--stacked">
                            <?php echo pauza_button($telegram, __('Написать в Telegram', 'pauza-rabotaet'), 'pauza-button pauza-button--primary'); ?>
                            <?php echo pauza_button($whatsapp, __('Написать в WhatsApp', 'pauza-rabotaet')); ?>
                            <?php echo pauza_button($max, __('Написать в MAX', 'pauza-rabotaet')); ?>
                            <?php echo $phone ? pauza_button(pauza_sms_link($phone), __('Написать SMS', 'pauza-rabotaet')) : ''; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="pauza-empty">
                <h2><?php esc_html_e('Контакты пока не опубликованы', 'pauza-rabotaet'); ?></h2>
                <p><?php esc_html_e('В админке уже можно добавить или опубликовать подтвержденных спонсоров.', 'pauza-rabotaet'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

