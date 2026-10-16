<?php
/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       Gamma Tileplanner
 * Plugin URI:        https://sirvelia.com/
 * Description:       Add the Tileplanner functionality.
 * Version:           1.0.0
 * Author:            Sirvelia - Studio Moare
 * Author URI:        https://sirvelia.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       gamma-tileplanner
 * Domain Path:       /languages
 */

use GammaTileplanner\Includes\Loader;

if ( ! defined( 'WPINC' ) ) {
	die('YOU SHALL NOT PASS!');
}

define( 'GAMMATILEPLANNER_NAME', 'gamma-tileplanner' );
define( 'GAMMATILEPLANNER_VERSION', '1.0.0' );
define( 'GAMMATILEPLANNER_PATH', plugin_dir_path( __FILE__ ) );
define( 'GAMMATILEPLANNER_URL', plugin_dir_url( __FILE__ ) );

if (file_exists(GAMMATILEPLANNER_PATH . 'vendor/autoload.php')) {
	require_once GAMMATILEPLANNER_PATH . 'vendor/autoload.php';
}

$loader = new Loader();
