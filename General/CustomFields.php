<?php
namespace GammaTileplanner\General;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CustomFields {

	protected $plugin_name;
	protected $plugin_version;

	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;

		add_action( 'after_setup_theme', array($this, 'load_cf') );
		add_action( 'carbon_fields_register_fields', array($this, 'register_fields') );
	}

	public function load_cf() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function register_fields() {

		Container::make( 'user_meta', 'Datos desde proyecto3d' )
		->add_fields( array(
			Field::make( 'text', 'cp', 'Código postal' ),
			Field::make( 'text', 'customer_type', 'Tipo de cliente' ),
			Field::make( 'text', 'profesional_type', 'Tipo de profesional' )
		) );
		return;
		
	}

}
