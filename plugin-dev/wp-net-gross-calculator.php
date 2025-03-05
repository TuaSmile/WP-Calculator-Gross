<?php
/**
 * Plugin Name: Net to gross calculator
 * Plugin URI: https://github.com/s-andrzejewski/wp-net-gross-calculator
 * Description: A plugin that calculates gross and tax amounts and saving data to CPT.
 * Version: 1.0.0
 * Author: Szymon Andrzejewski
 * Author URI: https://www.linkedin.com/in/szymon-andrzejewski-kepno/?locale=en_US
 * Text Domain: net-gross-calc
 * Domain path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

require_once __DIR__ . '/vendor/autoload.php';

use NetGrossCalc\Core;

defined('ABSPATH') || exit;

if ( !class_exists('Timber\\Timber') ) {
    echo 'Timber not found. Install it from <a href="https://packagist.org/packages/timber/timber">composer</a>.<br>';
    return;
}
if (!function_exists('get_field')) {
    echo 'Advanced Custom Fields PRO not found. Install it from <a href="https://www.advancedcustomfields.com/pro/">ACF PRO</a>.<br>';
    return;
}

define( 'NGC_BASENAME', plugin_basename(__FILE__) );
define( 'NGC_NAME', dirname(NGC_BASENAME) );
define( 'NGC_URL', untrailingslashit( plugin_dir_url(__FILE__)) );
define( 'NGC_PATH', untrailingslashit( plugin_dir_path(__FILE__)) );

//* TDOMAIN = text domain
define( 'NGC_TDOMAIN', 'net-gross-calc' );

Core::init();
