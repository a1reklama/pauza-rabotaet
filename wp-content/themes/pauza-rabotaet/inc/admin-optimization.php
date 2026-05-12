<?php
/**
 * Admin performance and owner-focused editing.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_admin_program_post_types(): array
{
    return ['pauza_step', 'pauza_sponsor', 'pauza_material', 'pauza_today', 'pauza_news'];
}

function pauza_disable_rest_editor_for_program_types(array $args, string $post_type): array
{
    if (in_array($post_type, pauza_admin_program_post_types(), true)) {
        $args['show_in_rest'] = false;
    }

    return $args;
}
add_filter('register_post_type_args', 'pauza_disable_rest_editor_for_program_types', 10, 2);

function pauza_disable_block_editor_for_program_types(bool $use_block_editor, string $post_type): bool
{
    if (in_array($post_type, pauza_admin_program_post_types(), true)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'pauza_disable_block_editor_for_program_types', 10, 2);

function pauza_disable_block_editor_for_program_posts(bool $use_block_editor, WP_Post $post): bool
{
    if (in_array($post->post_type, pauza_admin_program_post_types(), true)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'pauza_disable_block_editor_for_program_posts', 10, 2);

function pauza_trim_heavy_admin_supports(): void
{
    remove_post_type_support('pauza_step', 'editor');
    remove_post_type_support('pauza_step', 'thumbnail');
    remove_post_type_support('pauza_step', 'comments');
    remove_post_type_support('pauza_step', 'trackbacks');
    remove_post_type_support('pauza_sponsor', 'editor');
    remove_post_type_support('pauza_sponsor', 'excerpt');
    remove_post_type_support('pauza_sponsor', 'thumbnail');
    remove_post_type_support('pauza_sponsor', 'comments');
    remove_post_type_support('pauza_sponsor', 'trackbacks');
    remove_post_type_support('pauza_today', 'editor');
    remove_post_type_support('pauza_today', 'thumbnail');
    remove_post_type_support('pauza_today', 'comments');
    remove_post_type_support('pauza_today', 'trackbacks');
    remove_post_type_support('pauza_news', 'editor');

    // The old News type is not part of the public owner workflow anymore.
    remove_menu_page('edit.php?post_type=pauza_news');
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'pauza_trim_heavy_admin_supports', 99);

function pauza_add_owner_content_boxes(): void
{
    add_meta_box(
        'pauza_today_content',
        __('Вопрос и ответ', 'pauza-rabotaet'),
        'pauza_render_today_content_box',
        'pauza_today',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'pauza_add_owner_content_boxes');

function pauza_render_today_content_box(WP_Post $post): void
{
    wp_nonce_field('pauza_save_today_content', 'pauza_today_content_nonce');
    ?>
    <p class="description"><?php esc_html_e('Первый абзац будет вопросом. Остальной текст будет ответом. Пустая строка разделяет абзацы.', 'pauza-rabotaet'); ?></p>
    <textarea class="widefat" rows="18" name="pauza_today_content" id="pauza_today_content"><?php echo esc_textarea((string) $post->post_content); ?></textarea>
    <?php
}

function pauza_save_today_content_box(int $post_id, WP_Post $post): void
{
    if ('pauza_today' !== $post->post_type) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['pauza_today_content_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pauza_today_content_nonce'])), 'pauza_save_today_content')) {
        return;
    }

    $content = isset($_POST['pauza_today_content']) ? sanitize_textarea_field((string) wp_unslash($_POST['pauza_today_content'])) : '';

    if ($content === (string) $post->post_content) {
        return;
    }

    remove_action('save_post', 'pauza_save_today_content_box', 20);
    wp_update_post([
        'ID'           => $post_id,
        'post_content' => $content,
        'post_excerpt' => wp_trim_words(wp_strip_all_tags($content), 26),
    ]);
    add_action('save_post', 'pauza_save_today_content_box', 20, 2);
}
add_action('save_post', 'pauza_save_today_content_box', 20, 2);

function pauza_owner_dashboard_widget(): void
{
    if (!current_user_can('edit_posts')) {
        return;
    }

    wp_add_dashboard_widget(
        'pauza_owner_shortcuts',
        __('Быстрое редактирование сайта', 'pauza-rabotaet'),
        'pauza_render_owner_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'pauza_owner_dashboard_widget');

function pauza_render_owner_dashboard_widget(): void
{
    $links = [
        [
            'label' => __('Добавить текст в "Только сегодня"', 'pauza-rabotaet'),
            'url'   => admin_url('post-new.php?post_type=pauza_today'),
        ],
        [
            'label' => __('Редактировать тексты "Только сегодня"', 'pauza-rabotaet'),
            'url'   => admin_url('edit.php?post_type=pauza_today'),
        ],
        [
            'label' => __('Редактировать список спонсоров', 'pauza-rabotaet'),
            'url'   => admin_url('edit.php?post_type=pauza_sponsor'),
        ],
        [
            'label' => __('Добавить спонсора', 'pauza-rabotaet'),
            'url'   => admin_url('post-new.php?post_type=pauza_sponsor'),
        ],
    ];
    ?>
    <p><?php esc_html_e('Основные рабочие разделы вынесены сюда. Тяжелые экраны шагов лучше открывать только когда нужно править программу.', 'pauza-rabotaet'); ?></p>
    <div class="pauza-admin-shortcuts">
        <?php foreach ($links as $link) : ?>
            <a class="button button-primary" href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a>
        <?php endforeach; ?>
    </div>
    <?php
}

function pauza_simplify_dashboard_widgets(): void
{
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'pauza_simplify_dashboard_widgets', 100);

function pauza_remove_unused_admin_bar_nodes(WP_Admin_Bar $admin_bar): void
{
    $admin_bar->remove_node('comments');
    $admin_bar->remove_node('new-pauza_news');
}
add_action('admin_bar_menu', 'pauza_remove_unused_admin_bar_nodes', 999);

function pauza_filter_sponsors_by_gender(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || 'edit-pauza_sponsor' !== $screen->id) {
        return;
    }

    $current = isset($_GET['pauza_sponsor_gender']) ? sanitize_text_field(wp_unslash($_GET['pauza_sponsor_gender'])) : '';
    ?>
    <select name="pauza_sponsor_gender" id="pauza_sponsor_gender">
        <option value=""><?php esc_html_e('Все спонсоры', 'pauza-rabotaet'); ?></option>
        <option value="female" <?php selected($current, 'female'); ?>><?php esc_html_e('Женщины', 'pauza-rabotaet'); ?></option>
        <option value="male" <?php selected($current, 'male'); ?>><?php esc_html_e('Мужчины', 'pauza-rabotaet'); ?></option>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'pauza_filter_sponsors_by_gender');

function pauza_apply_sponsor_gender_filter(WP_Query $query): void
{
    global $pagenow;

    if (!is_admin() || 'edit.php' !== $pagenow || !$query->is_main_query()) {
        return;
    }

    $post_type = $query->get('post_type');
    if ('pauza_sponsor' !== $post_type) {
        return;
    }

    $gender = isset($_GET['pauza_sponsor_gender']) ? sanitize_text_field(wp_unslash($_GET['pauza_sponsor_gender'])) : '';
    if (!in_array($gender, ['female', 'male'], true)) {
        return;
    }

    $query->set('meta_query', [
        [
            'key'   => '_pauza_sponsor_gender',
            'value' => $gender,
        ],
    ]);
}
add_action('pre_get_posts', 'pauza_apply_sponsor_gender_filter');

function pauza_sponsor_admin_columns(array $columns): array
{
    return [
        'cb'            => $columns['cb'] ?? '<input type="checkbox" />',
        'title'         => __('Имя', 'pauza-rabotaet'),
        'pauza_gender'  => __('Пол', 'pauza-rabotaet'),
        'pauza_phone'   => __('Телефон', 'pauza-rabotaet'),
        'pauza_status'  => __('Статус', 'pauza-rabotaet'),
        'date'          => $columns['date'] ?? __('Дата', 'pauza-rabotaet'),
    ];
}
add_filter('manage_pauza_sponsor_posts_columns', 'pauza_sponsor_admin_columns');

function pauza_sponsor_admin_column_content(string $column, int $post_id): void
{
    if ('pauza_gender' === $column) {
        $gender = pauza_meta($post_id, '_pauza_sponsor_gender', 'female');
        echo esc_html('male' === $gender ? __('Мужчины', 'pauza-rabotaet') : __('Женщины', 'pauza-rabotaet'));
    }

    if ('pauza_phone' === $column) {
        echo esc_html(pauza_meta($post_id, '_pauza_sponsor_phone'));
    }

    if ('pauza_status' === $column) {
        $post = get_post($post_id);
        echo esc_html($post && 'publish' === $post->post_status ? __('Опубликован', 'pauza-rabotaet') : __('Черновик', 'pauza-rabotaet'));
    }
}
add_action('manage_pauza_sponsor_posts_custom_column', 'pauza_sponsor_admin_column_content', 10, 2);

function pauza_today_admin_columns(array $columns): array
{
    return [
        'cb'          => $columns['cb'] ?? '<input type="checkbox" />',
        'title'       => __('Заголовок', 'pauza-rabotaet'),
        'pauza_tease' => __('Начало текста', 'pauza-rabotaet'),
        'date'        => $columns['date'] ?? __('Дата', 'pauza-rabotaet'),
    ];
}
add_filter('manage_pauza_today_posts_columns', 'pauza_today_admin_columns');

function pauza_today_admin_column_content(string $column, int $post_id): void
{
    if ('pauza_tease' === $column) {
        echo esc_html(wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $post_id)), 18, '...'));
    }
}
add_action('manage_pauza_today_posts_custom_column', 'pauza_today_admin_column_content', 10, 2);

function pauza_admin_head_cleanup(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || ('dashboard' !== $screen->id && !in_array($screen->post_type, ['pauza_step', 'pauza_sponsor', 'pauza_material', 'pauza_today'], true))) {
        return;
    }
    ?>
    <style>
        .pauza-admin-shortcuts {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .pauza-admin-shortcuts .button {
            height: auto;
            line-height: 1.35;
            min-height: 36px;
            padding: 8px 12px;
            text-align: center;
            white-space: normal;
        }
        .post-type-pauza_step #postdivrich,
        .post-type-pauza_sponsor #postdivrich,
        .post-type-pauza_today #postdivrich {
            display: none !important;
        }
        .post-type-pauza_step .pauza-admin-field textarea,
        .post-type-pauza_sponsor .pauza-admin-field textarea,
        .post-type-pauza_material .pauza-admin-field textarea,
        .post-type-pauza_today #pauza_today_content {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 15px;
            line-height: 1.55;
        }
        .post-type-pauza_today #pauza_today_content {
            min-height: 420px;
        }
    </style>
    <?php
}
add_action('admin_head', 'pauza_admin_head_cleanup');
