<?php
/**
 * Custom content types for the program website.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_register_post_types(): void
{
    register_post_type('pauza_step', [
        'labels' => [
            'name'               => __('12 шагов', 'pauza-rabotaet'),
            'singular_name'      => __('Шаг', 'pauza-rabotaet'),
            'add_new_item'       => __('Добавить шаг', 'pauza-rabotaet'),
            'edit_item'          => __('Редактировать шаг', 'pauza-rabotaet'),
            'new_item'           => __('Новый шаг', 'pauza-rabotaet'),
            'view_item'          => __('Смотреть шаг', 'pauza-rabotaet'),
            'search_items'       => __('Искать шаги', 'pauza-rabotaet'),
            'not_found'          => __('Шаги не найдены', 'pauza-rabotaet'),
            'menu_name'          => __('12 шагов', 'pauza-rabotaet'),
        ],
        'public'       => true,
        'has_archive'  => '12-shagov',
        'rewrite'      => ['slug' => '12-shagov'],
        'menu_icon'    => 'dashicons-list-view',
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);

    register_post_type('pauza_sponsor', [
        'labels' => [
            'name'               => __('Спонсоры', 'pauza-rabotaet'),
            'singular_name'      => __('Спонсор', 'pauza-rabotaet'),
            'add_new_item'       => __('Добавить спонсора', 'pauza-rabotaet'),
            'edit_item'          => __('Редактировать спонсора', 'pauza-rabotaet'),
            'new_item'           => __('Новый спонсор', 'pauza-rabotaet'),
            'view_item'          => __('Смотреть спонсора', 'pauza-rabotaet'),
            'search_items'       => __('Искать спонсоров', 'pauza-rabotaet'),
            'not_found'          => __('Спонсоры не найдены', 'pauza-rabotaet'),
            'menu_name'          => __('Спонсоры', 'pauza-rabotaet'),
        ],
        'public'              => true,
        'has_archive'         => 'sponsory',
        'rewrite'             => ['slug' => 'sponsory'],
        'menu_icon'           => 'dashicons-groups',
        'show_in_rest'        => true,
        'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'exclude_from_search' => true,
    ]);

    register_post_type('pauza_today', [
        'labels' => [
            'name'               => __('Только сегодня', 'pauza-rabotaet'),
            'singular_name'      => __('Текст дня', 'pauza-rabotaet'),
            'add_new_item'       => __('Добавить текст', 'pauza-rabotaet'),
            'edit_item'          => __('Редактировать текст', 'pauza-rabotaet'),
            'new_item'           => __('Новый текст', 'pauza-rabotaet'),
            'view_item'          => __('Смотреть текст', 'pauza-rabotaet'),
            'search_items'       => __('Искать тексты', 'pauza-rabotaet'),
            'not_found'          => __('Тексты не найдены', 'pauza-rabotaet'),
            'menu_name'          => __('Только сегодня', 'pauza-rabotaet'),
        ],
        'public'       => true,
        'has_archive'  => 'tolko-segodnya',
        'rewrite'      => ['slug' => 'tolko-segodnya'],
        'menu_icon'    => 'dashicons-calendar-alt',
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);

    register_post_type('pauza_material', [
        'labels' => [
            'name'               => __('Материалы', 'pauza-rabotaet'),
            'singular_name'      => __('Материал', 'pauza-rabotaet'),
            'add_new_item'       => __('Добавить материал', 'pauza-rabotaet'),
            'edit_item'          => __('Редактировать материал', 'pauza-rabotaet'),
            'new_item'           => __('Новый материал', 'pauza-rabotaet'),
            'view_item'          => __('Смотреть материал', 'pauza-rabotaet'),
            'search_items'       => __('Искать материалы', 'pauza-rabotaet'),
            'not_found'          => __('Материалы не найдены', 'pauza-rabotaet'),
            'menu_name'          => __('Материалы', 'pauza-rabotaet'),
        ],
        'public'       => true,
        'has_archive'  => 'materialy',
        'rewrite'      => ['slug' => 'materialy'],
        'menu_icon'    => 'dashicons-media-document',
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);
}
add_action('init', 'pauza_register_post_types');

function pauza_flush_rewrite_rules_on_switch(): void
{
    pauza_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'pauza_flush_rewrite_rules_on_switch');

