<?php
/**
 * Sponsors archive.
 *
 * @package PauzaRabotaet
 */

get_header();
pauza_archive_title('Спонсоры', 'Сначала выбери спонсора своего пола. Потом напиши человеку вручную с телефона или в мессенджере.');
?>

<section class="pauza-section">
    <div class="pauza-container">
        <div class="pauza-next-box pauza-sponsor-consent">
            <div>
                <h2><?php esc_html_e('Сначала напиши, не звони', 'pauza-rabotaet'); ?></h2>
                <p><?php esc_html_e('Напиши сообщение в доступный мессенджер или SMS, представься и коротко расскажи о себе.', 'pauza-rabotaet'); ?></p>
            </div>
            <button class="pauza-button pauza-button--primary" type="button" data-sponsor-consent aria-expanded="false"><?php esc_html_e('Я прочитал и понимаю', 'pauza-rabotaet'); ?></button>
        </div>

        <div class="pauza-filter" role="group" aria-label="<?php esc_attr_e('Выбор списка спонсоров', 'pauza-rabotaet'); ?>" data-sponsor-controls hidden>
            <button type="button" data-sponsor-filter="female"><?php esc_html_e('Женщины', 'pauza-rabotaet'); ?></button>
            <button type="button" data-sponsor-filter="male"><?php esc_html_e('Мужчины', 'pauza-rabotaet'); ?></button>
        </div>

        <div class="pauza-sponsor-grid is-collapsed" data-sponsor-list data-nosnippet aria-live="polite" hidden></div>
    </div>
</section>

<?php
get_footer();
