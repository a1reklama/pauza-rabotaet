<?php
/**
 * Public sponsor privacy controls.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_sponsor_robots_directives(): array
{
    return [
        'noindex'   => true,
        'nofollow'  => true,
        'noarchive' => true,
        'nosnippet' => true,
    ];
}

function pauza_is_sponsor_public_view(): bool
{
    return is_post_type_archive('pauza_sponsor') || is_singular('pauza_sponsor');
}

function pauza_sponsor_robots_meta(array $robots): array
{
    if (!pauza_is_sponsor_public_view()) {
        return $robots;
    }

    return array_merge($robots, pauza_sponsor_robots_directives());
}
add_filter('wp_robots', 'pauza_sponsor_robots_meta');

function pauza_sponsor_robots_header(): void
{
    if (!pauza_is_sponsor_public_view()) {
        return;
    }

    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true);
}
add_action('send_headers', 'pauza_sponsor_robots_header');

function pauza_remove_sponsors_from_sitemap(array $post_types): array
{
    unset($post_types['pauza_sponsor']);

    return $post_types;
}
add_filter('wp_sitemaps_post_types', 'pauza_remove_sponsors_from_sitemap');

function pauza_redirect_single_sponsor(): void
{
    if (!is_singular('pauza_sponsor')) {
        return;
    }

    wp_safe_redirect(home_url('/#sponsors'), 302);
    exit;
}
add_action('template_redirect', 'pauza_redirect_single_sponsor');

function pauza_public_sponsor_payload(): array
{
    $query = pauza_sponsors_query();
    $items = [];

    if (!$query->have_posts()) {
        return $items;
    }

    while ($query->have_posts()) {
        $query->the_post();

        $post_id = get_the_ID();
        $gender = pauza_meta($post_id, '_pauza_sponsor_gender', 'female');

        if (!in_array($gender, ['female', 'male'], true)) {
            $gender = 'female';
        }

        $items[] = [
            'name'   => get_the_title(),
            'gender' => $gender,
            'phone'  => pauza_meta($post_id, '_pauza_sponsor_phone'),
            'note'   => pauza_meta($post_id, '_pauza_sponsor_note'),
        ];
    }

    wp_reset_postdata();

    return $items;
}

function pauza_sponsor_ajax_response(): void
{
    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true);
    nocache_headers();
    check_ajax_referer('pauza_sponsors', 'nonce');

    wp_send_json_success([
        'sponsors' => pauza_public_sponsor_payload(),
    ]);
}
add_action('wp_ajax_pauza_sponsors', 'pauza_sponsor_ajax_response');
add_action('wp_ajax_nopriv_pauza_sponsors', 'pauza_sponsor_ajax_response');
