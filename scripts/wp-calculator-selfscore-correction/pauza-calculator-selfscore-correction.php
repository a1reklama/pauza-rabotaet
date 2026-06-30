<?php
/**
 * Plugin Name: Pauza Calculator Self Score Correction
 * Description: One-time correction for calculator self-score fixed groups.
 * Version: 2026.06.30.2
 * Author: Pauza Rabotaet Ops
 */

defined('ABSPATH') || exit;

function pauza_calculator_selfscore_correction_normalize(string $value): string
{
    return str_replace("\r\n", "\n", $value);
}

function pauza_calculator_selfscore_correction_apply(): array
{
    $calculator_dir = trailingslashit(ABSPATH) . 'calculator';
    $config_path = $calculator_dir . '/assets/js/config.js';
    $index_path = $calculator_dir . '/index.html';
    $stamp = gmdate('Ymd-His');

    foreach ([$config_path, $index_path] as $path) {
        if (!is_file($path) || !is_readable($path) || !is_writable($path)) {
            throw new RuntimeException('File is not readable/writable: ' . $path);
        }
    }

    $config_original = file_get_contents($config_path);
    if (false === $config_original) {
        throw new RuntimeException('Unable to read config.js');
    }

    $config = pauza_calculator_selfscore_correction_normalize($config_original);
    $from = "score: 0,\n          questions: [\n            \"Изменяю ли я любимому человеку?\",";
    $to = "score: 3,\n          questions: [\n            \"Изменяю ли я любимому человеку?\",";

    if (false === strpos($config, $from)) {
        throw new RuntimeException('Expected score 0 group for cheating/violence/theft was not found in config.js');
    }
    $config = str_replace($from, $to, $config);

    $index_original = file_get_contents($index_path);
    if (false === $index_original) {
        throw new RuntimeException('Unable to read index.html');
    }

    $index = preg_replace(
        '~assets/js/config\.js(?:\?v=[^"\']+)?~',
        'assets/js/config.js?v=20260630-selfscore-final',
        $index_original,
        1,
        $index_replacements
    );
    if (!is_string($index) || 0 === $index_replacements) {
        throw new RuntimeException('config.js script reference was not found in index.html');
    }

    $config_backup = $config_path . '.bak-' . $stamp;
    $index_backup = $index_path . '.bak-' . $stamp;
    if (!copy($config_path, $config_backup) || !copy($index_path, $index_backup)) {
        throw new RuntimeException('Unable to create correction backups');
    }

    if (false === file_put_contents($config_path, $config)) {
        throw new RuntimeException('Unable to write config.js');
    }
    if (false === file_put_contents($index_path, $index)) {
        throw new RuntimeException('Unable to write index.html');
    }

    return [
        'status' => 'success',
        'config_backup' => $config_backup,
        'index_backup' => $index_backup,
        'version' => '20260630-selfscore-final',
        'changed_groups' => [
            'cheating_violence_theft' => ['from' => 0, 'to' => 3],
        ],
    ];
}

function pauza_calculator_selfscore_correction_activate(): void
{
    try {
        $result = pauza_calculator_selfscore_correction_apply();
    } catch (Throwable $error) {
        $result = [
            'status' => 'error',
            'message' => $error->getMessage(),
        ];
    }

    update_option('pauza_calculator_selfscore_correction_result', $result, false);
    if ('success' !== ($result['status'] ?? '')) {
        wp_die(esc_html($result['message'] ?? 'Calculator correction failed'));
    }
}

register_activation_hook(__FILE__, 'pauza_calculator_selfscore_correction_activate');

add_action('admin_notices', static function (): void {
    $result = get_option('pauza_calculator_selfscore_correction_result');
    if (!is_array($result)) {
        return;
    }

    $class = 'success' === ($result['status'] ?? '') ? 'notice-success' : 'notice-error';
    echo '<div class="notice ' . esc_attr($class) . '"><p><strong>Pauza Calculator Correction:</strong> '
        . esc_html(wp_json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        . '</p></div>';
});
