<?php
namespace GammaTileplanner\Includes;

use GammaTileplanner\Functionality\CustomFields;
use GammaTileplanner\Functionality\AccessControl;
use GammaTileplanner\Functionality\LoginActions;
use GammaTileplanner\Functionality\PasswordResetActions;
use GammaTileplanner\Functionality\RegistrationActions;
use GammaTileplanner\Functionality\Routes;
use GammaTileplanner\Functionality\ThemeOptions;

class Loader {

	public function __construct() {
		$this->load_dependencies();
		add_action( 'plugins_loaded', [$this, 'load_plugin_textdomain'] );
	}

	private function load_dependencies() {
		new ThemeOptions();
		new Routes();
		new AccessControl();
		new LoginActions();
		new PasswordResetActions();
		new RegistrationActions();
		new CustomFields();
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain( GAMMATILEPLANNER_NAME, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

}
