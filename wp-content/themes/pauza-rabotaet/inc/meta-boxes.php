<?php
/**
 * Admin meta boxes for editable content.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_add_meta_boxes(): void
{
    add_meta_box(
        'pauza_step_details',
        __('Параметры шага', 'pauza-rabotaet'),
        'pauza_render_step_meta_box',
        'pauza_step',
        'normal',
        'high'
    );

    add_meta_box(
        'pauza_sponsor_details',
        __('Контакт и публикация', 'pauza-rabotaet'),
        'pauza_render_sponsor_meta_box',
        'pauza_sponsor',
        'normal',
        'high'
    );

    add_meta_box(
        'pauza_today_details',
        __('Параметры текста', 'pauza-rabotaet'),
        'pauza_render_today_meta_box',
        'pauza_today',
        'side',
        'default'
    );

    add_meta_box(
        'pauza_news_details',
        __('Параметры новости', 'pauza-rabotaet'),
        'pauza_render_news_meta_box',
        'pauza_news',
        'side',
        'default'
    );

    add_meta_box(
        'pauza_material_details',
        __('Параметры материала', 'pauza-rabotaet'),
        'pauza_render_material_meta_box',
        'pauza_material',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'pauza_add_meta_boxes');

function pauza_admin_text_input(string $id, string $label, string $value, string $type = 'text', string $description = ''): void
{
    ?>
    <p class="pauza-admin-field">
        <label for="<?php echo esc_attr($id); ?>"><strong><?php echo esc_html($label); ?></strong></label>
        <input class="widefat" type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>">
        <?php if ($description) : ?>
            <span class="description"><?php echo esc_html($description); ?></span>
        <?php endif; ?>
    </p>
    <?php
}

function pauza_admin_textarea(string $id, string $label, string $value, int $rows = 5, string $description = ''): void
{
    ?>
    <p class="pauza-admin-field">
        <label for="<?php echo esc_attr($id); ?>"><strong><?php echo esc_html($label); ?></strong></label>
        <textarea class="widefat" rows="<?php echo esc_attr((string) $rows); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"><?php echo esc_textarea($value); ?></textarea>
        <?php if ($description) : ?>
            <span class="description"><?php echo esc_html($description); ?></span>
        <?php endif; ?>
    </p>
    <?php
}

function pauza_admin_select(string $id, string $label, string $value, array $options): void
{
    ?>
    <p class="pauza-admin-field">
        <label for="<?php echo esc_attr($id); ?>"><strong><?php echo esc_html($label); ?></strong></label>
        <select class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>">
            <?php foreach ($options as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr((string) $option_value); ?>" <?php selected($value, (string) $option_value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function pauza_render_step_meta_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_step_meta', 'pauza_step_nonce');

    pauza_admin_text_input('_pauza_step_number', __('Номер шага', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_number'), 'number');
    pauza_admin_text_input('_pauza_step_status', __('Метка статуса', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_status'), 'text', __('Например: Старт, В работе, Пожизненно.', 'pauza-rabotaet'));
    pauza_admin_textarea('_pauza_step_goal', __('Короткая цель шага', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_goal'), 3);
    pauza_admin_textarea('_pauza_step_requirements', __('Условия входа', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_requirements'), 4, __('Каждое условие с новой строки.', 'pauza-rabotaet'));
    pauza_admin_textarea('_pauza_step_tasks', __('Что делать', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_tasks'), 8, __('Каждое действие с новой строки. На сайте это станет чек-листом.', 'pauza-rabotaet'));
    pauza_admin_textarea('_pauza_step_materials', __('Материалы и ссылки шага', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_materials'), 5, __('Каждый материал с новой строки: видео, канал, диск, бот, внешняя инструкция.', 'pauza-rabotaet'));
    pauza_admin_textarea('_pauza_step_exercises', __('Упражнения и важные блоки', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_exercises'), 8, __('Глоссарий, молитвы, подсказки, списки, ВДА и другие блоки, которые должны жить внутри шага.', 'pauza-rabotaet'));
    pauza_admin_textarea('_pauza_step_full_text', __('Текст руководителя', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_full_text'), 12, __('Длинный текст показывается только во вкладке "Текст руководителя", чтобы не перегружать страницу.', 'pauza-rabotaet'));
    pauza_admin_text_input('_pauza_step_telegram_url', __('Ссылка Telegram', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_telegram_url'), 'url');
    pauza_admin_text_input('_pauza_step_max_url', __('Ссылка MAX', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_max_url'), 'url');
    pauza_admin_text_input('_pauza_step_video_url', __('Основное видео/плейлист', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_video_url'), 'url');
    pauza_admin_text_input('_pauza_step_next_label', __('Текст кнопки следующего действия', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_next_label'), 'text');
    pauza_admin_text_input('_pauza_step_next_url', __('Ссылка следующего действия', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_step_next_url'), 'url');
}

function pauza_render_sponsor_meta_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_sponsor_meta', 'pauza_sponsor_nonce');

    pauza_admin_select('_pauza_sponsor_gender', __('Группа', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_gender', 'female'), [
        'female' => __('Женщины', 'pauza-rabotaet'),
        'male'   => __('Мужчины', 'pauza-rabotaet'),
    ]);
    pauza_admin_text_input('_pauza_sponsor_phone', __('Телефон для ручного сообщения', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_phone'), 'text', __('На сайте телефон показывается текстом. Пользователь сам пишет с телефона или из мессенджера.', 'pauza-rabotaet'));
    pauza_admin_text_input('_pauza_sponsor_telegram_url', __('Telegram-ссылка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_telegram_url'), 'url');
    pauza_admin_text_input('_pauza_sponsor_whatsapp_url', __('WhatsApp-ссылка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_whatsapp_url'), 'url');
    pauza_admin_text_input('_pauza_sponsor_max_url', __('MAX-ссылка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_max_url'), 'url');
    pauza_admin_textarea('_pauza_sponsor_note', __('Примечание', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_sponsor_note'), 3);
}

function pauza_render_today_meta_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_today_meta', 'pauza_today_nonce');
    pauza_admin_text_input('_pauza_today_date', __('Дата/метка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_today_date'), 'text', __('Например: 5 мая или Неделя 1.', 'pauza-rabotaet'));
}

function pauza_render_news_meta_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_news_meta', 'pauza_news_nonce');
    pauza_admin_text_input('_pauza_news_type', __('Тип новости', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_news_type'), 'text', __('Например: Видео, Группы, Важно.', 'pauza-rabotaet'));
    pauza_admin_select('_pauza_news_origin', __('Происхождение', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_news_origin', 'project'), [
        'project'       => __('Новость проекта', 'pauza-rabotaet'),
        'external_test' => __('Внешняя новость для теста', 'pauza-rabotaet'),
    ]);
    pauza_admin_text_input('_pauza_news_source', __('Источник', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_news_source'), 'text', __('Например: МНПЦ наркологии или КонсультантПлюс.', 'pauza-rabotaet'));
    pauza_admin_text_input('_pauza_news_url', __('Ссылка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_news_url'), 'url');
    pauza_admin_text_input('_pauza_news_button_label', __('Текст кнопки', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_news_button_label', __('Открыть', 'pauza-rabotaet')), 'text');
}

function pauza_render_material_meta_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_material_meta', 'pauza_material_nonce');

    pauza_admin_select('_pauza_material_type', __('Тип', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_material_type', 'video'), [
        'project_channel' => __('Канал проекта', 'pauza-rabotaet'),
        'step_group'      => __('Группа шага', 'pauza-rabotaet'),
        'bot'             => __('Бот', 'pauza-rabotaet'),
        'video'           => __('Видео', 'pauza-rabotaet'),
        'calculator'      => __('Калькулятор', 'pauza-rabotaet'),
        'download'        => __('Скачать', 'pauza-rabotaet'),
        'instruction'     => __('Инструкция', 'pauza-rabotaet'),
    ]);
    pauza_admin_text_input('_pauza_material_url', __('Внешняя ссылка', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_material_url'), 'url');
    pauza_admin_text_input('_pauza_material_button_label', __('Текст кнопки', 'pauza-rabotaet'), pauza_meta($post->ID, '_pauza_material_button_label', __('Открыть', 'pauza-rabotaet')), 'text');
}

function pauza_save_meta_boxes(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $post_type = get_post_type($post_id);

    if ('pauza_step' === $post_type && isset($_POST['pauza_step_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_step_nonce'])), 'pauza_save_step_meta')) {
        $fields = [
            '_pauza_step_number'       => 'int',
            '_pauza_step_status'       => 'text',
            '_pauza_step_goal'         => 'textarea',
            '_pauza_step_requirements' => 'textarea',
            '_pauza_step_tasks'        => 'textarea',
            '_pauza_step_materials'    => 'textarea',
            '_pauza_step_exercises'    => 'textarea',
            '_pauza_step_full_text'    => 'textarea',
            '_pauza_step_telegram_url' => 'url',
            '_pauza_step_max_url'      => 'url',
            '_pauza_step_video_url'    => 'url',
            '_pauza_step_next_label'   => 'text',
            '_pauza_step_next_url'     => 'url',
        ];
        pauza_save_fields($post_id, $fields);
    }

    if ('pauza_sponsor' === $post_type && isset($_POST['pauza_sponsor_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_sponsor_nonce'])), 'pauza_save_sponsor_meta')) {
        $fields = [
            '_pauza_sponsor_gender'       => 'text',
            '_pauza_sponsor_phone'        => 'text',
            '_pauza_sponsor_telegram_url' => 'url',
            '_pauza_sponsor_whatsapp_url' => 'url',
            '_pauza_sponsor_max_url'      => 'url',
            '_pauza_sponsor_note'         => 'textarea',
        ];
        pauza_save_fields($post_id, $fields);
    }

    if ('pauza_today' === $post_type && isset($_POST['pauza_today_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_today_nonce'])), 'pauza_save_today_meta')) {
        pauza_save_fields($post_id, ['_pauza_today_date' => 'text']);
    }

    if ('pauza_news' === $post_type && isset($_POST['pauza_news_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_news_nonce'])), 'pauza_save_news_meta')) {
        $fields = [
            '_pauza_news_type'         => 'text',
            '_pauza_news_origin'       => 'text',
            '_pauza_news_source'       => 'text',
            '_pauza_news_url'          => 'url',
            '_pauza_news_button_label' => 'text',
        ];
        pauza_save_fields($post_id, $fields);
    }

    if ('pauza_material' === $post_type && isset($_POST['pauza_material_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_material_nonce'])), 'pauza_save_material_meta')) {
        $fields = [
            '_pauza_material_type'         => 'text',
            '_pauza_material_url'          => 'url',
            '_pauza_material_button_label' => 'text',
        ];
        pauza_save_fields($post_id, $fields);
    }
}
add_action('save_post', 'pauza_save_meta_boxes');

function pauza_save_fields(int $post_id, array $fields): void
{
    foreach ($fields as $key => $type) {
        if (!array_key_exists($key, $_POST)) {
            delete_post_meta($post_id, $key);
            continue;
        }

        $raw = wp_unslash($_POST[$key]);

        if ('url' === $type) {
            $value = esc_url_raw((string) $raw);
        } elseif ('int' === $type) {
            $value = (string) absint($raw);
        } elseif ('textarea' === $type) {
            $value = sanitize_textarea_field((string) $raw);
        } else {
            $value = sanitize_text_field((string) $raw);
        }

        update_post_meta($post_id, $key, $value);
    }
}
