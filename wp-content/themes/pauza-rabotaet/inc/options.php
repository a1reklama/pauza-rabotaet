<?php
/**
 * Theme settings page.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_register_options(): void
{
    register_setting('pauza_options_group', 'pauza_options', [
        'type'              => 'array',
        'sanitize_callback' => 'pauza_sanitize_options',
        'default'           => [],
    ]);
}
add_action('admin_init', 'pauza_register_options');

function pauza_add_options_page(): void
{
    add_menu_page(
        __('Пауза работает', 'pauza-rabotaet'),
        __('Пауза работает', 'pauza-rabotaet'),
        'manage_options',
        'pauza-settings',
        'pauza_render_options_page',
        'dashicons-heart',
        58
    );
}
add_action('admin_menu', 'pauza_add_options_page');

function pauza_sanitize_options(array $input): array
{
    $url_fields = [
        'telegram_channel_url',
        'rutube_channel_url',
        'yandex_disk_url',
        'calculator_instruction_url',
        'calculator_telegram_url',
        'calculator_max_url',
        'four_step_bot_url',
        'four_step_max_bot_url',
    ];

    $text_fields = [
        'calculator_intro',
        'privacy_notice',
        'footer_note',
    ];

    $output = [];

    foreach ($url_fields as $field) {
        $output[$field] = isset($input[$field]) ? esc_url_raw((string) $input[$field]) : '';
    }

    foreach ($text_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_textarea_field((string) $input[$field]) : '';
    }

    return $output;
}

function pauza_sanitize_embed(string $html): string
{
    $allowed = wp_kses_allowed_html('post');
    $allowed['iframe'] = [
        'src'             => true,
        'width'           => true,
        'height'          => true,
        'frameborder'     => true,
        'allow'           => true,
        'allowfullscreen' => true,
        'loading'         => true,
        'title'           => true,
    ];

    return wp_kses($html, $allowed);
}

function pauza_render_options_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('pauza_options', []);
    $options = is_array($options) ? $options : [];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Настройки сайта "Пауза работает"', 'pauza-rabotaet'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('pauza_options_group'); ?>

            <h2><?php esc_html_e('Глобальные ссылки', 'pauza-rabotaet'); ?></h2>
            <?php pauza_options_text('telegram_channel_url', __('Telegram-канал с видео', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('rutube_channel_url', __('Rutube-канал', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('yandex_disk_url', __('Яндекс.Диск', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('four_step_bot_url', __('Telegram-бот 4 шага', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('four_step_max_bot_url', __('MAX-бот 4 шага', 'pauza-rabotaet'), $options, 'url'); ?>

            <h2><?php esc_html_e('Калькулятор', 'pauza-rabotaet'); ?></h2>
            <?php pauza_options_textarea('calculator_intro', __('Пояснение на странице калькулятора', 'pauza-rabotaet'), $options, 4); ?>
            <?php pauza_options_text('calculator_instruction_url', __('Ссылка на инструкцию', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('calculator_telegram_url', __('Ссылка на Telegram-бот калькулятора', 'pauza-rabotaet'), $options, 'url'); ?>
            <?php pauza_options_text('calculator_max_url', __('Ссылка на MAX-бот калькулятора', 'pauza-rabotaet'), $options, 'url'); ?>

            <h2><?php esc_html_e('Юридические и служебные тексты', 'pauza-rabotaet'); ?></h2>
            <?php pauza_options_textarea('privacy_notice', __('Заметка о контактах спонсоров', 'pauza-rabotaet'), $options, 4); ?>
            <?php pauza_options_textarea('footer_note', __('Текст в подвале', 'pauza-rabotaet'), $options, 3); ?>

            <?php submit_button(__('Сохранить настройки', 'pauza-rabotaet')); ?>
        </form>
    </div>
    <?php
}

function pauza_options_text(string $key, string $label, array $options, string $type = 'text'): void
{
    ?>
    <p>
        <label for="pauza_options_<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label>
        <input class="regular-text" type="<?php echo esc_attr($type); ?>" id="pauza_options_<?php echo esc_attr($key); ?>" name="pauza_options[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) ($options[$key] ?? '')); ?>">
    </p>
    <?php
}

function pauza_options_textarea(string $key, string $label, array $options, int $rows = 5): void
{
    ?>
    <p>
        <label for="pauza_options_<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label>
        <textarea class="large-text" rows="<?php echo esc_attr((string) $rows); ?>" id="pauza_options_<?php echo esc_attr($key); ?>" name="pauza_options[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea((string) ($options[$key] ?? '')); ?></textarea>
    </p>
    <?php
}
