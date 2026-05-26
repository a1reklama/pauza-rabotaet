<?php
/**
 * Footer.
 *
 * @package PauzaRabotaet
 */

$footer_note = pauza_get_option('footer_note', 'Сайт помогает ориентироваться в программе и ведет во внешние группы, боты и видеоматериалы.');
?>
</main>

<footer class="pauza-footer">
    <div class="pauza-container pauza-footer__grid">
        <div class="pauza-footer__brand">
            <h2><?php esc_html_e('12 шагов для ВСЕХ', 'pauza-rabotaet'); ?></h2>
            <p><?php echo esc_html($footer_note); ?></p>
        </div>
        <nav class="pauza-footer__nav" aria-label="<?php esc_attr_e('Меню в подвале', 'pauza-rabotaet'); ?>">
            <h3><?php esc_html_e('Быстрый переход', 'pauza-rabotaet'); ?></h3>
            <?php
            pauza_footer_menu();
            ?>
        </nav>
        <div class="pauza-footer__meetings">
            <h3><?php esc_html_e('ГРУППА', 'pauza-rabotaet'); ?></h3>
            <p><?php esc_html_e('09:00 и 21:00 ЕЖЕДНЕВНО', 'pauza-rabotaet'); ?></p>
            <a class="pauza-footer__button" href="<?php echo esc_url(pauza_meetings_zoom_url()); ?>" target="_blank" rel="noopener noreferrer nofollow">
                <?php esc_html_e('ПОДКЛЮЧАЙСЯ ПО ССЫЛКЕ', 'pauza-rabotaet'); ?>
            </a>
        </div>
    </div>
</footer>

<div class="pauza-meetings-float" data-meetings-float>
    <button class="pauza-meetings-float__trigger" type="button" aria-expanded="false" aria-controls="pauza-meetings-popup" data-meetings-toggle>
        <?php esc_html_e('Группа', 'pauza-rabotaet'); ?>
    </button>
    <div class="pauza-meetings-float__popup" id="pauza-meetings-popup" role="dialog" aria-label="<?php esc_attr_e('Группа', 'pauza-rabotaet'); ?>" data-meetings-popup>
        <p><?php esc_html_e('09:00 и 21:00 ЕЖЕДНЕВНО', 'pauza-rabotaet'); ?></p>
        <a href="<?php echo esc_url(pauza_meetings_zoom_url()); ?>" target="_blank" rel="noopener noreferrer nofollow" data-meetings-zoom>
            <?php esc_html_e('ПОДКЛЮЧАЙСЯ ПО ССЫЛКЕ', 'pauza-rabotaet'); ?>
        </a>
    </div>
</div>

<div class="pauza-help-modal" id="pauza-help-modal" data-help-modal hidden>
    <div class="pauza-help-modal__backdrop" data-help-close></div>
    <section class="pauza-help-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="pauza-help-title" tabindex="-1">
        <div class="pauza-help-modal__header">
            <h2 id="pauza-help-title"><?php esc_html_e('Помощь', 'pauza-rabotaet'); ?></h2>
            <button class="pauza-help-modal__close" type="button" data-help-close aria-label="<?php esc_attr_e('Закрыть помощь', 'pauza-rabotaet'); ?>">
                <?php esc_html_e('Закрыть', 'pauza-rabotaet'); ?>
            </button>
        </div>
        <nav class="pauza-help-modal__actions" aria-label="<?php esc_attr_e('Быстрые действия помощи', 'pauza-rabotaet'); ?>">
            <a class="pauza-help-modal__action" href="<?php echo esc_url(home_url('/#sponsors')); ?>"><?php esc_html_e('Выбрать спонсора', 'pauza-rabotaet'); ?></a>
            <a class="pauza-help-modal__action" href="<?php echo esc_url(pauza_meetings_zoom_url()); ?>" target="_blank" rel="noopener noreferrer nofollow"><?php esc_html_e('Подключиться к группе', 'pauza-rabotaet'); ?></a>
            <a class="pauza-help-modal__action" href="<?php echo esc_url(home_url('/#step-1')); ?>"><?php esc_html_e('Открыть 1 шаг', 'pauza-rabotaet'); ?></a>
            <a class="pauza-help-modal__action" href="<?php echo esc_url(home_url('/#bot-4')); ?>"><?php esc_html_e('Открыть бот 4 шага', 'pauza-rabotaet'); ?></a>
            <a class="pauza-help-modal__action" href="<?php echo esc_url(pauza_calculator_url()); ?>" target="_blank" rel="noopener noreferrer nofollow"><?php esc_html_e('Открыть калькулятор', 'pauza-rabotaet'); ?></a>
            <a class="pauza-help-modal__action" href="<?php echo esc_url(home_url('/#today')); ?>"><?php esc_html_e('Только сегодня', 'pauza-rabotaet'); ?></a>
        </nav>
    </section>
</div>

<?php wp_footer(); ?>
</body>
</html>
