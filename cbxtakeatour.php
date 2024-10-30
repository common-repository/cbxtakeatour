<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeboxr.com
 * @since             1.0.0
 * @package           CBXTakeaTour
 *
 * @wordpress-plugin
 * Plugin Name:       CBX Tour - User Walkthroughs & Guided Tours
 * Plugin URI:        https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress//
 * Description:       Interactive tour creator for product/service feature for wordpress
 * Version:           1.1.2
 * Author:            Codeboxr Team
 * Author URI:        https://codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxtakeatour
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

defined('CBXTAKEATOUR_PLUGIN_NAME') or define('CBXTAKEATOUR_PLUGIN_NAME', 'cbxtakeatour');
defined('CBXTAKEATOUR_PLUGIN_VERSION') or define('CBXTAKEATOUR_PLUGIN_VERSION', '1.1.2');
defined('CBXTAKEATOUR_BASE_NAME') or define('CBXTAKEATOUR_BASE_NAME', plugin_basename(__FILE__));
defined('CBXTAKEATOUR_ROOT_PATH') or define('CBXTAKEATOUR_ROOT_PATH', plugin_dir_path(__FILE__));
defined('CBXTAKEATOUR_ROOT_URL') or define('CBXTAKEATOUR_ROOT_URL', plugin_dir_url(__FILE__));


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cbxtakeatour-activator.php
 */
function activate_cbxtakeatour()
{
    require_once plugin_dir_path(__FILE__).'includes/class-cbxtakeatour-activator.php';
    CBXTakeaTour_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cbxtakeatour-deactivator.php
 */
function deactivate_cbxtakeatour()
{
    require_once plugin_dir_path(__FILE__).'includes/class-cbxtakeatour-deactivator.php';
    CBXTakeaTour_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cbxtakeatour');
register_deactivation_hook(__FILE__, 'deactivate_cbxtakeatour');

/**
 * The core plugin class that is used to define internationalization,
 * common hooks, admin-specific hooks and public-facing site hooks.
 */
require plugin_dir_path(__FILE__).'includes/class-cbxtakeatour.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.1
 */
function run_cbxtakeatour()
{
    return CBXTakeaTour::instance();
}

$GLOBALS['cbxtakeatour'] = run_cbxtakeatour();