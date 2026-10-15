<?php
namespace GammaTileplanner\Includes;

class Languages {

  public function __construct() {
    add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'gamma-tileplanner', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

}
