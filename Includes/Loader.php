<?php
namespace GammaTileplanner\Includes;

use GammaTileplanner\Admin\PostActions;
use GammaTileplanner\General\CustomFields;
use GammaTileplanner\General\ThemeOptions;
use GammaTileplanner\General\Routes;

class Loader {

	protected $plugin_name;
	protected $plugin_version;

	public function __construct() {
		$this->plugin_version = defined( 'GAMMATILEPLANNER_VERSION' ) ? GAMMATILEPLANNER_VERSION : '1.0.0';
		$this->plugin_name = 'gamma-tileplanner';
		$this->load_dependencies();
	}

	private function load_dependencies() {
		$plugin_i18n = new Languages();

		$theme_options = new ThemeOptions($this->plugin_name, $this->plugin_version);

		$routes = new Routes($this->plugin_name, $this->plugin_version);

		$post_actions = new PostActions($this->plugin_name, $this->plugin_version);

		$custom_fields = new CustomFields($this->plugin_name, $this->plugin_version);
	}
}
