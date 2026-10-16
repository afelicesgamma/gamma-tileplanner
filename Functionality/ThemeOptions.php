<?php
namespace GammaTileplanner\Functionality;

use Carbon_Fields\Container;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Field;

class ThemeOptions {

	public function __construct() {
		add_action( 'after_setup_theme', array($this, 'load_cf') );
		add_action( 'carbon_fields_register_fields', array($this, 'register_fields') );
	}

	public function load_cf() {
		Carbon_Fields::boot();
	}

	public function register_fields() {

		Container::make( 'theme_options', 'Tileplanner' )
		->add_fields( array(
			Field::make( 'text', 'tileplanner_route_slug', 'Slug de la ruta para Tileplanner (vacío para usar la página de inicio)' ),
			Field::make( 'image', 'tileplanner_logo', 'Logo del Header')
			->set_value_type( 'url' ),
			Field::make( 'color', 'tileplanner_primary_color', 'Color primario'),
			Field::make( 'image', 'tileplanner_background_image', 'Imagen de fondo')
			->set_value_type( 'url' ),
		) );

		return;
	}

}
