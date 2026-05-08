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

function pauza_calculator_url(): string
{
    return 'https://pauzarabotaet.ru/calculator/';
}

function pauza_is_external_url(string $url): bool
{
    $url = trim($url);

    if ('' === $url || 0 === strpos($url, '#') || 0 === strpos($url, '/')) {
        return false;
    }

    if (!preg_match('/^https?:\/\//i', $url)) {
        return false;
    }

    return 0 !== strpos($url, home_url('/'));
}

function pauza_external_link_attrs(string $url): string
{
    return pauza_is_external_url($url) ? ' target="_blank" rel="noopener noreferrer"' : '';
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

function pauza_today_parts(string $content): array
{
    $plain = trim(wp_strip_all_tags(strip_shortcodes($content)));
    $paragraphs = pauza_paragraphs($plain);

    if (!$paragraphs) {
        return [
            'question' => '',
            'answer'   => '',
        ];
    }

    return [
        'question' => $paragraphs[0],
        'answer'   => implode("\n\n", array_slice($paragraphs, 1)),
    ];
}

function pauza_today_question_answer_html(string $content, bool $trim_answer = false): string
{
    $parts = pauza_today_parts($content);
    $question = $parts['question'];
    $answer = $trim_answer && $parts['answer'] ? wp_trim_words($parts['answer'], 34, '...') : $parts['answer'];

    ob_start();
    ?>
    <div class="pauza-qa-list">
        <?php if ($question) : ?>
            <div class="pauza-qa pauza-qa--question">
                <span class="pauza-qa__label"><?php esc_html_e('Вопрос', 'pauza-rabotaet'); ?></span>
                <?php echo wpautop(esc_html($question)); ?>
            </div>
        <?php endif; ?>
        <?php if ($answer) : ?>
            <div class="pauza-qa pauza-qa--answer">
                <span class="pauza-qa__label"><?php esc_html_e('Ответ', 'pauza-rabotaet'); ?></span>
                <?php echo wpautop(esc_html($answer)); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php

    return (string) ob_get_clean();
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
        '<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
        esc_attr($class),
        esc_url($url),
        pauza_external_link_attrs($url),
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

function pauza_step_display_title($number): string
{
    return sprintf(__('%s шаг для ВСЕХ', 'pauza-rabotaet'), (string) $number);
}

function pauza_video_label_from_context(string $context): string
{
    $context = preg_replace('/https?:\/\/[^\s)]+/i', '', $context);
    $context = is_string($context) ? trim($context) : '';

    if ('' === $context) {
        return '';
    }

    if (preg_match('/инструкц[^\n\r.]*калькулятор/iu', $context)) {
        return __('инструкцию к калькулятору', 'pauza-rabotaet');
    }

    if (preg_match('/видео\s+(ИНТРО|ПРО БЕССИЛИЕ|\d{3}\s+ШАГ\s+\d+\s+ДЕНЬ\s+\d+|по\s+4\s+и\s+5\s+шагам)/iu', $context, $matches)) {
        return trim($matches[1]);
    }

    if (preg_match('/видео\s+([^.,;\n\r()]{1,80})/iu', $context, $matches)) {
        return trim($matches[1]);
    }

    return '';
}

function pauza_url_label(string $url, string $context = ''): string
{
    if (pauza_context_describes_url($url, $context)) {
        return __('Открыть', 'pauza-rabotaet');
    }

    $step = '';
    if (preg_match('/группа\s+(\d+)\s+шага/iu', $context, $matches)) {
        $step = $matches[1];
    }

    if (preg_match('/FourStepForAllBot/i', $url)) {
        return __('Открыть Telegram-бот', 'pauza-rabotaet');
    }

    if (preg_match('/max\.ru\/id860230186705_bot/i', $url)) {
        return __('Открыть MAX-бот', 'pauza-rabotaet');
    }

    if (preg_match('/cbr\.ru/i', $url)) {
        return __('Открыть курс ЦБ', 'pauza-rabotaet');
    }

    if (preg_match('/rutube\.ru/i', $url)) {
        if (preg_match('/\/channel\//i', $url)) {
            return __('Открыть Rutube-канал', 'pauza-rabotaet');
        }

        $video_label = pauza_video_label_from_context($context);
        return $video_label ? sprintf(__('Открыть видео %s', 'pauza-rabotaet'), $video_label) : __('Открыть видео', 'pauza-rabotaet');
    }

    if (preg_match('/disk\.yandex\.ru/i', $url)) {
        return __('Открыть Яндекс.Диск', 'pauza-rabotaet');
    }

    if (preg_match('/t\.me\/neporukovodstvu/i', $url)) {
        return __('Открыть Telegram-канал 360 видео', 'pauza-rabotaet');
    }

    if (preg_match('/t\.me/i', $url)) {
        return $step ? sprintf(__('Открыть Telegram-группу %s шага', 'pauza-rabotaet'), $step) : __('Открыть Telegram', 'pauza-rabotaet');
    }

    if (preg_match('/max\.ru/i', $url)) {
        return $step ? sprintf(__('Открыть MAX-группу %s шага', 'pauza-rabotaet'), $step) : __('Открыть MAX', 'pauza-rabotaet');
    }

    return __('Открыть ссылку', 'pauza-rabotaet');
}

function pauza_context_describes_url(string $url, string $context): bool
{
    if ('' === trim($context)) {
        return false;
    }

    $without_urls = trim((string) preg_replace('/https?:\/\/[^\s)]+/iu', ' ', $context));
    $without_urls = trim((string) preg_replace('/\s+/u', ' ', $without_urls));

    if ('' === $without_urls || !preg_match('/[а-яa-z0-9]/iu', $without_urls)) {
        return false;
    }

    if (preg_match('/rutube\.ru\/video/i', $url)) {
        return (bool) preg_match('/(смотрю|смотреть|посмотреть|видео|интро)/iu', $without_urls);
    }

    if (preg_match('/cbr\.ru/i', $url)) {
        return (bool) preg_match('/(цб|курс|доллар|валют)/iu', $without_urls);
    }

    if (preg_match('/disk\.yandex\.ru/i', $url)) {
        return (bool) preg_match('/(яндекс|диск|скачать|материал)/iu', $without_urls);
    }

    if (preg_match('/t\.me/i', $url)) {
        return (bool) preg_match('/(телеграм|telegram|группа|канал|бот)/iu', $without_urls);
    }

    if (preg_match('/max\.ru/i', $url)) {
        return (bool) preg_match('/(макс|max|группа|бот)/iu', $without_urls);
    }

    if (preg_match('/rutube\.ru/i', $url)) {
        return (bool) preg_match('/(rutube|рутуб|канал|видео)/iu', $without_urls);
    }

    return false;
}

function pauza_linkify_text(string $text, bool $replace_urls = false): string
{
    if ('' === $text || !preg_match('/https?:\/\//i', $text)) {
        return esc_html($text);
    }

    $result = '';
    $offset = 0;
    preg_match_all('/https?:\/\/[^\s)]+/i', $text, $matches, PREG_OFFSET_CAPTURE);

    foreach ($matches[0] as $match) {
        [$raw_url, $position] = $match;
        $url = rtrim($raw_url, '.,;:');
        $suffix = substr($raw_url, strlen($url));
        $result .= esc_html(substr($text, $offset, $position - $offset));
        $result .= sprintf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
            esc_url($url),
            esc_html($replace_urls ? pauza_url_label($url, $text) : $url)
        );
        $result .= esc_html($suffix);
        $offset = $position + strlen($raw_url);
    }

    $result .= esc_html(substr($text, $offset));

    return $result;
}

function pauza_render_plain_text(string $text, bool $replace_urls = false): void
{
    foreach (pauza_paragraphs($text) as $paragraph) {
        echo '<p>' . nl2br(pauza_linkify_text($paragraph, $replace_urls)) . '</p>';
    }
}

function pauza_origin_badge(string $origin, string $label = ''): string
{
    return '';
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

function pauza_summary_link_label(string $url): string
{
    if (preg_match('/rutube\.ru\/video/i', $url)) {
        return __('ссылка на видео', 'pauza-rabotaet');
    }

    if (preg_match('/rutube\.ru/i', $url)) {
        return __('ссылка на Rutube', 'pauza-rabotaet');
    }

    if (preg_match('/t\.me/i', $url)) {
        return __('ссылка на Telegram', 'pauza-rabotaet');
    }

    if (preg_match('/max\.ru/i', $url)) {
        return __('ссылка на MAX', 'pauza-rabotaet');
    }

    if (preg_match('/cbr\.ru/i', $url)) {
        return __('ссылка на ЦБ', 'pauza-rabotaet');
    }

    if (preg_match('/disk\.yandex\.ru/i', $url)) {
        return __('ссылка на Яндекс.Диск', 'pauza-rabotaet');
    }

    return __('ссылка', 'pauza-rabotaet');
}

function pauza_compact_summary_text(string $text): string
{
    $text = preg_replace_callback('/https?:\/\/[^\s)]+/i', static function ($matches) {
        $raw_url = (string) $matches[0];
        $url = rtrim($raw_url, '.,;:');
        $suffix = substr($raw_url, strlen($url));

        return pauza_summary_link_label($url) . $suffix;
    }, $text);

    $text = wp_strip_all_tags((string) $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim((string) $text);
}

function pauza_render_step_source_sections(string $full_text): void
{
    $chunks = pauza_step_source_chunks($full_text);

    foreach ($chunks as $index => $chunk) {
        $summary = wp_trim_words(pauza_compact_summary_text((string) ($chunk[0] ?? '')), 12, '...');
        if ('' === $summary) {
            $summary = sprintf(__('Фрагмент %d', 'pauza-rabotaet'), $index + 1);
        }

        echo '<details class="pauza-details">';
        echo '<summary>' . esc_html($summary) . '</summary>';
        echo '<div class="pauza-content">';
        pauza_render_plain_text(implode("\n", $chunk), true);
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
        $has_url = (bool) preg_match('/https?:\/\//i', $line);

        if ($has_url) {
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
    $class = $has_source_numbers || 'ul' === $type ? 'pauza-source-list' : 'pauza-task-list';

    printf('<%1$s class="%2$s">', esc_html($tag), esc_attr($class));
    foreach ($items as $item) {
        $item = (string) $item;

        if (preg_match('/^(\d+)\.\s+(.*)$/u', $item, $matches)) {
            $label = __('Задание', 'pauza-rabotaet');

            if (preg_match('/видео/iu', $item)) {
                $label = __('Видео', 'pauza-rabotaet');
            } elseif (preg_match('/калькулятор/iu', $item)) {
                $label = __('Калькулятор', 'pauza-rabotaet');
            } elseif (preg_match('/бот/iu', $item)) {
                $label = __('Бот', 'pauza-rabotaet');
            } elseif (preg_match('/перехожу|переходи|группа\s+\d+\s+шага/iu', $item)) {
                $label = __('Переход', 'pauza-rabotaet');
            }

            echo '<li class="pauza-source-item">';
            echo '<span class="pauza-source-item__number">' . esc_html($matches[1]) . '</span>';
            echo '<div class="pauza-source-item__body">';
            echo '<span class="pauza-source-item__type">' . esc_html($label) . '</span>';
            echo '<p>' . pauza_linkify_text($matches[2], true) . '</p>';
            echo '</div>';
            echo '</li>';
            continue;
        }

        if (preg_match('/^(.+?)\s*→\s*(.+)$/u', $item, $matches)) {
            echo '<li class="pauza-source-text pauza-source-pair">';
            echo '<span class="pauza-source-pair__from">' . esc_html($matches[1]) . '</span>';
            echo '<span class="pauza-source-pair__arrow">→</span>';
            echo '<span class="pauza-source-pair__to">' . esc_html($matches[2]) . '</span>';
            echo '</li>';
            continue;
        }

        echo '<li class="pauza-source-text"><p>' . pauza_linkify_text($item, true) . '</p></li>';
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
        echo '<summary>' . esc_html($title) . '</summary>';
        echo '<div class="pauza-content">';

        if (is_array($content)) {
            pauza_render_source_list($content);
        } elseif ('' !== trim((string) $content)) {
            pauza_render_plain_text((string) $content, true);
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
            <p class="pauza-eyebrow"><?php esc_html_e('12 шагов для ВСЕХ', 'pauza-rabotaet'); ?></p>
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
        ['Спонсоры', home_url('/sponsory/')],
        ['Материалы', home_url('/materialy/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Бот 4 шага', home_url('/bot-4-shaga/')],
        ['Калькулятор', pauza_calculator_url()],
        ['Только сегодня', home_url('/tolko-segodnya/')],
    ];

    echo '<ul class="pauza-nav__list">';
    foreach ($items as $item) {
        printf('<li><a href="%s"%s>%s</a></li>', esc_url($item[1]), pauza_external_link_attrs($item[1]), esc_html($item[0]));
    }
    echo '</ul>';
}

function pauza_footer_menu(): void
{
    $items = [
        ['Начать', home_url('/')],
        ['Спонсоры', home_url('/sponsory/')],
        ['Материалы', home_url('/materialy/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Бот 4 шага', home_url('/bot-4-shaga/')],
        ['Калькулятор', pauza_calculator_url()],
        ['Только сегодня', home_url('/tolko-segodnya/')],
    ];

    echo '<ul class="pauza-footer__links">';
    foreach ($items as $item) {
        printf('<li><a href="%s"%s>%s</a></li>', esc_url($item[1]), pauza_external_link_attrs($item[1]), esc_html($item[0]));
    }
    echo '</ul>';
}

function pauza_external_nav_link_attrs(array $atts): array
{
    $url = isset($atts['href']) ? (string) $atts['href'] : '';

    if (pauza_is_external_url($url)) {
        $atts['target'] = '_blank';
        $atts['rel'] = trim(($atts['rel'] ?? '') . ' noopener noreferrer');
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'pauza_external_nav_link_attrs');

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

    if ($query->is_post_type_archive('pauza_material')) {
        $query->set('meta_query', [
            [
                'key'     => '_pauza_material_type',
                'value'   => ['project_channel', 'video', 'download'],
                'compare' => 'IN',
            ],
        ]);
    }
}
add_action('pre_get_posts', 'pauza_order_archives');
