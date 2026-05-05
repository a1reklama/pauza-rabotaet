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

function pauza_origin_badge(string $origin, string $label = ''): string
{
    $labels = [
        'source'        => __('Из DOCX', 'pauza-rabotaet'),
        'editorial'     => __('Редакционный слой', 'pauza-rabotaet'),
        'external_test' => __('Внешняя новость для теста', 'pauza-rabotaet'),
        'verify'        => __('Нужно подтвердить', 'pauza-rabotaet'),
    ];

    $label = $label ?: ($labels[$origin] ?? $origin);

    return sprintf(
        '<span class="pauza-origin pauza-origin--%1$s">%2$s</span>',
        esc_attr($origin),
        esc_html($label)
    );
}

function pauza_step_icon_html(string $number): string
{
    $step = max(1, min(12, (int) $number));
    $relative = sprintf('/assets/step-icons/step-%02d.png', $step);
    $path = PAUZA_THEME_DIR . $relative;

    if (file_exists($path)) {
        return sprintf(
            '<img class="pauza-step-icon" src="%1$s" alt="%2$s">',
            esc_url(PAUZA_THEME_URI . $relative),
            esc_attr(sprintf(__('Шаг %d', 'pauza-rabotaet'), $step))
        );
    }

    return sprintf(
        '<span class="pauza-step-icon pauza-step-icon--fallback">%1$d</span>',
        $step
    );
}

function pauza_step_source_chunks(string $text): array
{
    $lines = pauza_lines($text);
    $chunks = [];
    $current = [];

    foreach ($lines as $line) {
        if (preg_match('/^\d+\.\s+/u', $line) && $current) {
            $chunks[] = $current;
            $current = [];
        }

        $current[] = $line;
    }

    if ($current) {
        $chunks[] = $current;
    }

    $result = [];
    foreach ($chunks as $chunk) {
        $chunk_text = implode("\n", $chunk);
        $text_length = function_exists('mb_strlen') ? mb_strlen($chunk_text) : strlen($chunk_text);
        if ($text_length < 2200 || count($chunk) < 10) {
            $result[] = $chunk;
            continue;
        }

        foreach (array_chunk($chunk, 8) as $part) {
            $result[] = $part;
        }
    }

    return $result;
}

function pauza_render_step_source_sections(string $full_text): void
{
    $chunks = pauza_step_source_chunks($full_text);

    foreach ($chunks as $index => $chunk) {
        $summary = wp_trim_words(wp_strip_all_tags($chunk[0] ?? ''), 12, '...');
        if ('' === $summary) {
            $summary = sprintf(__('Фрагмент %d', 'pauza-rabotaet'), $index + 1);
        }

        echo '<details class="pauza-details">';
        echo '<summary>' . esc_html($summary) . ' ' . pauza_origin_badge('source') . '</summary>';
        echo '<div class="pauza-content">';
        pauza_render_plain_text(implode("\n", $chunk));
        echo '</div>';
        echo '</details>';
    }
}

function pauza_step_numbered_lines(string $text): array
{
    return array_values(array_filter(pauza_lines($text), static function ($line) {
        return (bool) preg_match('/^\d+\.\s+/u', $line);
    }));
}

function pauza_step_material_lines(string $text): array
{
    $items = [];

    foreach (pauza_lines($text) as $line) {
        if (preg_match('/https?:\/\//i', $line) || preg_match('/(группа\s+\d+\s+шага|телеграм|telegram|макс|max|видео|бот|калькулятор|rutube|яндекс|диск|cbr\.ru)/iu', $line)) {
            $items[] = $line;
        }
    }

    return array_values(array_unique($items));
}

function pauza_render_source_list(array $items, string $type = 'ol'): void
{
    if (!$items) {
        return;
    }

    $has_source_numbers = (bool) array_filter($items, static function ($item) {
        return (bool) preg_match('/^\d+\.\s+/u', (string) $item);
    });
    $tag = $has_source_numbers || 'ul' === $type ? 'ul' : 'ol';
    $class = $has_source_numbers ? 'pauza-source-list' : ('ul' === $type ? 'pauza-check-list' : 'pauza-task-list');

    printf('<%1$s class="%2$s">', esc_html($tag), esc_attr($class));
    foreach ($items as $item) {
        echo '<li>' . esc_html((string) $item) . '</li>';
    }
    printf('</%s>', esc_html($tag));
}

function pauza_text_between(string $text, string $start, string $end = ''): string
{
    $start_pos = strpos($text, $start);
    if (false === $start_pos) {
        return '';
    }

    $end_pos = '' !== $end ? strpos($text, $end, $start_pos) : false;
    if (false === $end_pos) {
        return trim(substr($text, $start_pos));
    }

    return trim(substr($text, $start_pos, $end_pos - $start_pos + strlen($end)));
}

function pauza_render_step8_source_sections(string $full_text): void
{
    $numbered = pauza_step_numbered_lines($full_text);
    $harm = [];
    $money = [];

    foreach ($numbered as $line) {
        if (preg_match('/^(2|3|4|5|6|7|8|9)\.\s/u', $line)) {
            $harm[] = $line;
        }

        if (preg_match('/^(10|11|12|13)\.\s/u', $line)) {
            $money[] = $line;
        }
    }

    $vda = pauza_text_between($full_text, 'УПРАЖНЕНИЕ ВДА', 'Конец записи');
    $transition = pauza_text_between($full_text, '15. Переходи в группу 9 шага.');
    $sections = [
        __('Списки вреда', 'pauza-rabotaet')          => $harm,
        __('Материальный ущерб', 'pauza-rabotaet')    => $money,
        __('Упражнение ВДА', 'pauza-rabotaet')        => $vda,
        __('Переход в 9 шаг', 'pauza-rabotaet')       => $transition,
    ];

    foreach ($sections as $title => $content) {
        echo '<details class="pauza-details">';
        echo '<summary>' . esc_html($title) . ' ' . pauza_origin_badge('source') . '</summary>';
        echo '<div class="pauza-content">';

        if (is_array($content)) {
            pauza_render_source_list($content);
        } elseif ('' !== trim((string) $content)) {
            pauza_render_plain_text((string) $content);
        } else {
            echo '<p>' . esc_html__('Этот фрагмент не найден в сохраненном тексте шага.', 'pauza-rabotaet') . '</p>';
        }

        echo '</div>';
        echo '</details>';
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

function pauza_latest_news_query(int $limit = 3): WP_Query
{
    return new WP_Query([
        'post_type'      => 'pauza_news',
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
        ['Начать', home_url('/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Спонсоры', home_url('/sponsory/')],
        ['Новости', home_url('/novosti/')],
    ];

    echo '<ul class="pauza-nav__list">';
    foreach ($items as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item[1]), esc_html($item[0]));
    }
    echo '<li class="menu-item-has-children"><a href="#">Еще</a><ul class="sub-menu">';
    foreach ([['Материалы', home_url('/materialy/')], ['Только сегодня', home_url('/tolko-segodnya/')], ['Калькуляторы', home_url('/calculator/')]] as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item[1]), esc_html($item[0]));
    }
    echo '</ul></li>';
    echo '</ul>';
}

function pauza_footer_menu(): void
{
    $items = [
        ['Начать', home_url('/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Спонсоры', home_url('/sponsory/')],
        ['Новости', home_url('/novosti/')],
    ];

    echo '<ul class="pauza-footer__links">';
    foreach ($items as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item[1]), esc_html($item[0]));
    }
    echo '</ul>';
}

function pauza_material_type_label(string $type): string
{
    $labels = [
        'project_channel' => __('Канал проекта', 'pauza-rabotaet'),
        'step_group'      => __('Группа шага', 'pauza-rabotaet'),
        'bot'             => __('Бот', 'pauza-rabotaet'),
        'video'           => __('Видео', 'pauza-rabotaet'),
        'calculator'      => __('Калькулятор', 'pauza-rabotaet'),
        'download'        => __('Скачать', 'pauza-rabotaet'),
        'instruction'     => __('Инструкция', 'pauza-rabotaet'),
    ];

    return $labels[$type] ?? $type;
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
