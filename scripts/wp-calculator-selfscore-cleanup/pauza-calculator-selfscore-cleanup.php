<?php
/**
 * Plugin Name: Pauza Calculator Self Score Cleanup
 * Description: Removes malformed temporary calculator hotfix plugin folders.
 * Version: 2026.06.30
 * Author: Pauza Rabotaet Ops
 */

defined('ABSPATH') || exit;

function pauza_calculator_selfscore_cleanup_rm(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    if (is_file($path) || is_link($path)) {
        @unlink($path);
        return;
    }

    $items = scandir($path);
    if (!is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        if ('.' === $item || '..' === $item) {
            continue;
        }
        pauza_calculator_selfscore_cleanup_rm($path . DIRECTORY_SEPARATOR . $item);
    }

    @rmdir($path);
}

function pauza_calculator_selfscore_cleanup_activate(): void
{
    $removed = [];
    $plugin_dir = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : trailingslashit(WP_CONTENT_DIR) . 'plugins';

    foreach (scandir($plugin_dir) ?: [] as $item) {
        if ('.' === $item || '..' === $item) {
            continue;
        }

        if (0 === strpos($item, 'wp-calculator-selfscore-hotfix')) {
            $path = $plugin_dir . DIRECTORY_SEPARATOR . $item;
            pauza_calculator_selfscore_cleanup_rm($path);
            $removed[] = $path;
        }
    }

    update_option('pauza_calculator_selfscore_cleanup_result', [
        'status' => 'success',
        'removed' => $removed,
    ], false);
}

register_activation_hook(__FILE__, 'pauza_calculator_selfscore_cleanup_activate');

add_action('admin_notices', static function (): void {
    $result = get_option('pauza_calculator_selfscore_cleanup_result');
    if (!is_array($result)) {
        return;
    }

    echo '<div class="notice notice-success"><p><strong>Pauza Calculator Cleanup:</strong> '
        . esc_html(wp_json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        . '</p></div>';
});
