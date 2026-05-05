<?php
/**
 * Sponsors archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('Спонсоры и проводники', 'Сначала выберите список. Потом напишите человеку вручную с телефона или в мессенджере.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-next-box">
            <div>
                <p class="pauza-eyebrow"><?php esc_html_e('Перед контактом', 'pauza-rabotaet'); ?></p>
                <h2><?php esc_html_e('Выберите свой пол', 'pauza-rabotaet'); ?></h2>
                <p><?php esc_html_e('Сначала напишите, не звоните. Представьтесь и коротко расскажите о себе. Контакты показываются только после выбора списка.', 'pauza-rabotaet'); ?></p>
            </div>
        </div>

        <div class="pauza-filter" role="group" aria-label="<?php esc_attr_e('Выбор списка спонсоров', 'pauza-rabotaet'); ?>">
            <button type="button" data-sponsor-filter="female"><?php esc_html_e('Женщины', 'pauza-rabotaet'); ?></button>
            <button type="button" data-sponsor-filter="male"><?php esc_html_e('Мужчины', 'pauza-rabotaet'); ?></button>
        </div>

        <?php if (have_posts()) : ?>
            <div class="pauza-sponsor-grid is-collapsed" data-sponsor-list>
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
                    <article class="pauza-sponsor-card pauza-sponsor-card--compact is-hidden" data-sponsor-gender="<?php echo esc_attr($gender); ?>">
                        <h3><?php the_title(); ?></h3>
                        <?php if ($phone) : ?>
                            <p class="pauza-phone"><?php echo esc_html($phone); ?></p>
                        <?php endif; ?>
                        <?php if ($note) : ?>
                            <p><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                        <?php if ($telegram || $whatsapp || $max) : ?>
                            <p class="pauza-muted-line"><?php esc_html_e('Дополнительные ссылки можно заполнить в админке.', 'pauza-rabotaet'); ?></p>
                        <?php endif; ?>
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
