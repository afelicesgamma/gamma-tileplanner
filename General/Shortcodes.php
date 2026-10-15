<?php
namespace GammaTileplanner\General;

use GammaTileplanner\Includes\BladeLoader;

class Shortcodes {

	protected $plugin_name;
	protected $plugin_version;

	private $blade;

	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;
		$this->blade = BladeLoader::getInstance();

		add_action( 'init', array($this, 'add_shortcodes') );
	}

	public function add_shortcodes() {
		add_shortcode( 'tileplanner', array($this, 'do_tileplanner') );
		return;
	}

	public function do_tileplanner($atts, $content = "") {
		return $this->blade->template(
			'tileplanner',
			[
				'is_user_logged_id'		=> is_user_logged_in(),
			]
		);
	}

}
