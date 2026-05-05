<?php
/**
 * Template helpers.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_get_option(string $key, string $default = ''): string
{
    $options = get_option('pauza_options', []);

    if (!is_array($options) || !array_key_exists($key, $options)) {
        return $default;
    }

    return (string) $options[$key];
}

function pauza_meta(int $post_id, string $key, string $default = ''): string
{
    $value = get_post_meta($post_id, $key, true);

    return '' === $value || null === $value ? $default : (string) $value;
}

function pauza_lines(string $value): array
{
    $lines = preg_split('/\r\n|\r|\n/', $value);
    $lines = array_map('trim', is_array($lines) ? $lines : []);

    return array_values(array_filter($lines, static fn ($line) => '' !== $line));
}

function pauza_paragraphs(string $value): array
{
    $paragraphs = preg_split('/\n\s*\n|\r\n\s*\r\n|\r\s*\r/', trim($value));
    $paragraphs = array_map('trim', is_array($paragraphs) ? $paragraphs : []);

    return array_values(array_filter($paragraphs, static fn ($paragraph) => '' !== $paragraph));
}

function pauza_button(string $url, string $label, string $class = 'pauza-button'): string
{
    if ('' === trim($url)) {
        return '';
    }

    return sprintf(
        '<a class="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer">%3$s</a>',
        esc_attr($class),
        esc_url($url),
        esc_html($label)
    );
}

function pauza_internal_button(string $url, string $label, string $class = 'pauza-button pauza-button--ghost'): string
{
    if ('' === trim($url)) {
        return '';
    }

    return sprintf(
        '<a class="%1$s" href="%2$s">%3$s</a>',
        esc_attr($class),
        esc_url($url),
        esc_html($label)
    );
}

function pauza_smart_button(string $url, string $label, string $class = 'pauza-button'): string
{
    if ('' === trim($url)) {
        return '';
    }

    if (0 === strpos($url, home_url('/')) || 0 === strpos($url, '/')) {
        return pauza_internal_button($url, $label, $class);
    }

    return pauza_button($url, $label, $class);
}

function pauza_render_plain_text(string $text): void
{
    foreach (pauza_paragraphs($text) as $paragraph) {
        echo '<p>' . nl2br(esc_html($paragraph)) . '</p>';
    }
}

function pauza_sms_link(string $phone): string
{
    $clean = preg_replace('/[^\d+]/', '', $phone);

    return $clean ? 'sms:' . $clean : '';
}

function pauza_steps_query(int $limit = -1): WP_Query
{
    return new WP_Query([
        'post_type'      => 'pauza_step',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_key'       => '_pauza_step_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);
}

function pauza_latest_today_query(int $limit = 3): WP_Query
{
    return new WP_Query([
        'post_type'      => 'pauza_today',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

function pauza_sponsors_query(): WP_Query
{
    return new WP_Query([
        'post_type'      => 'pauza_sponsor',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'order'          => 'ASC',
    ]);
}

function pauza_archive_title(string $title, string $subtitle = ''): void
{
    ?>
    <section class="pauza-page-hero">
        <div class="pauza-container">
            <p class="pauza-eyebrow"><?php esc_html_e('Пауза работает', 'pauza-rabotaet'); ?></p>
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle) : ?>
                <p class="pauza-lead"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function pauza_fallback_menu(): void
{
    $items = [
        ['Главная', home_url('/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Спонсоры', home_url('/sponsory/')],
        ['Калькулятор', home_url('/calculator/')],
        ['Только сегодня', home_url('/tolko-segodnya/')],
        ['Бот 4 шага', home_url('/bot-4-shaga/')],
    ];

    echo '<ul class="pauza-nav__list">';
    foreach ($items as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item[1]), esc_html($item[0]));
    }
    echo '</ul>';
}

function pauza_order_archives(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('pauza_step')) {
        $query->set('meta_key', '_pauza_step_number');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', 12);
    }

    if ($query->is_post_type_archive('pauza_sponsor')) {
        $query->set('orderby', ['menu_order' => 'ASC', 'title' => 'ASC']);
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'pauza_order_archives');
