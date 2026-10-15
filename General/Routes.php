<?php
namespace GammaTileplanner\General;

use PluboRoutes\Route\Route;
use PluboRoutes\Route\ActionRoute;
use PluboRoutes\Route\RedirectRoute;

use GammaTileplanner\Includes\BladeLoader;

class Routes {

	protected $plugin_name;
	protected $plugin_version;

	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;
		$this->blade = BladeLoader::getInstance();

		add_filter( 'plubo/routes', array($this, 'add_routes') );
		add_action( 'parse_request', [$this, 'match_home_route'], 9 );
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


	private function get_tileplanner_route_slug() {
		$slug = carbon_get_theme_option( 'tileplanner_route_slug' );

		return $slug === null || $slug === '' ? '/' : $slug;
	}

	public function match_home_route( $wp ) {
		if ( $this->get_tileplanner_route_slug() !== '/' || trim( $wp->request, '/' ) !== '' ) {
			return;
		}

		$route_variable = apply_filters( 'plubo/route_variable', 'route_name' );
		$wp->query_vars[$route_variable] = md5( '/' );
	}

	public function add_routes( $routes ) {

		$routes[] = new Route(
			$this->get_tileplanner_route_slug(),
			function($matches) {
				echo $this->blade->template(
					'tileplanner',
					[
						'tileplanner_route'		=> $this->get_tileplanner_route_slug(),
						'is_user_logged_id'		=> is_user_logged_in(),
						'user_id'				=> get_current_user_id(),
						'group_id'				=> 'gamma-sites-' . get_current_blog_id(),
						'primary_color'			=> carbon_get_theme_option( 'tileplanner_primary_color' ) ?? '#D90708',
						'background_image'		=> carbon_get_theme_option( 'tileplanner_background_image' ) ?? '',
						'logo'		=> carbon_get_theme_option( 'tileplanner_logo' ) ?? '',
						'secret'				=> 'UdcCiRjYdGiq1xOHKWeRcoMFemO9JDGO',
					]
				);
				exit();
			}
		);

		return $routes;
	}

}
