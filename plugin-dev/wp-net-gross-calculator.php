<?php
/**
 * Plugin Name: Net to Gross Calculator
 * Plugin URI: https://github.com/s-andrzejewski/wp-net-gross-calculator
 * Description: A plugin that calculates gross and tax amounts and saves data to CPT.
 * Version: 1.0.0
 * Domain Path: /languages
 * Requires at Least: 5.8
 * Requires PHP: 7.4
 */

require_once __DIR__ . '/vendor/autoload.php';

use NetGrossCalc\Core;

// Exit if accessed directly
defined('ABSPATH') || exit;

// Helper function for displaying admin notices
function ngc_admin_notice($message, $link = '') {
    echo '<div class="error"><p><strong>' . esc_html($message) . '</strong>';
    if ($link) {
        echo ' <a href="' . esc_url($link) . '" target="_blank">' . esc_html($link) . '</a>';
    }
    echo '</p></div>';
}

// Check if the required dependencies are installed
function ngc_check_dependencies() {
    // Check if Timber is installed
    if (!class_exists('Timber\\Timber')) {
        ngc_admin_notice('Timber not found. Install it from <a href="https://packagist.org/packages/timber/timber">composer</a>.');
        return false;
    }

    // Check if ACF Pro is installed
    if (!function_exists('get_field')) {
        ngc_admin_notice('Advanced Custom Fields PRO not found. Install it from <a href="https://www.advancedcustomfields.com/pro/">ACF PRO</a>.');
        return false;
    }

    return true;
}

// Proceed only if dependencies are satisfied
if (ngc_check_dependencies()) {
    // Plugin constants
    define('NGC_BASENAME', plugin_basename(__FILE__));
    define('NGC_NAME', dirname(NGC_BASENAME));
    define('NGC_URL', untrailingslashit(plugin_dir_url(__FILE__)));
    define('NGC_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
    define('NGC_TDOMAIN', 'net-gross-calc');

    // Initialize the Core
    Core::init();
}
