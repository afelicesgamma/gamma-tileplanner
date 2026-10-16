<?php
namespace GammaTileplanner\General;

use GammaTileplanner\Includes\BladeLoader;

class Routes {

	const QUERY_VAR = 'gamma_tileplanner';

	protected $plugin_name;
	protected $plugin_version;

	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;

		add_action( 'init', [$this, 'add_rewrite_rule'] );
		add_filter( 'query_vars', [$this, 'add_query_var'] );
		add_action( 'parse_request', [$this, 'match_home_route'], 9 );
		add_filter( 'template_include', [$this, 'template_include'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue'] );
	}

	public function enqueue() {

		wp_enqueue_style( 'inter', '//rsms.me/inter/inter.css' );

		wp_enqueue_style('gamma-tileplanner/app.css', GAMMATILEPLANNER_URL . 'dist/app.css', false, null);
		wp_enqueue_script('gamma-tileplanner/app.js', GAMMATILEPLANNER_URL . 'dist/app.js', [], null, true);

		wp_localize_script( 'gamma-tileplanner/app.js', 'gamma_tileplanner_ajax', array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'ajax-nonce' ),
		) );

	}


	private static function get_tileplanner_route_slug() {
		$slug = carbon_get_theme_option( 'tileplanner_route_slug' );

		if ( ! is_string( $slug ) ) {
			return '/';
		}

		$slug = trim( trim( $slug ), '/' );

		return $slug === '' ? '/' : $slug;
	}

	public function add_rewrite_rule() {
		$slug = self::get_tileplanner_route_slug();

		if ( $slug === '/' ) {
			return;
		}

		add_rewrite_rule(
			'^' . preg_quote( $slug, '#' ) . '/?$',
			'index.php?' . self::QUERY_VAR . '=1',
			'top'
		);
	}

	public function add_query_var( $query_vars ) {
		$query_vars[] = self::QUERY_VAR;

		return $query_vars;
	}

	public function match_home_route( $wp ) {
		if ( self::get_tileplanner_route_slug() !== '/' || trim( $wp->request, '/' ) !== '' ) {
			return;
		}

		$wp->query_vars[self::QUERY_VAR] = '1';
	}

	public function template_include( $template ) {
		if ( (string) get_query_var( self::QUERY_VAR ) !== '1' ) {
			return $template;
		}

		return GAMMATILEPLANNER_PATH . 'templates/tileplanner.php';
	}

	public static function render_tileplanner() {
		return BladeLoader::getInstance()->template(
			'tileplanner',
			[
				'tileplanner_route'		=> self::get_tileplanner_route_slug(),
				'is_user_logged_id'		=> is_user_logged_in(),
				'user_id'				=> get_current_user_id(),
				'group_id'				=> 'gamma-sites-' . get_current_blog_id(),
				'primary_color'			=> carbon_get_theme_option( 'tileplanner_primary_color' ) ?? '#D90708',
				'background_image'		=> carbon_get_theme_option( 'tileplanner_background_image' ) ?? '',
				'logo'					=> carbon_get_theme_option( 'tileplanner_logo' ) ?? '',
				'secret'				=> 'UdcCiRjYdGiq1xOHKWeRcoMFemO9JDGO',
			]
		);
	}

}
