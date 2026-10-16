<?php
namespace GammaTileplanner\General;

use GammaTileplanner\Includes\BladeLoader;

class Routes {

	const QUERY_VAR = 'gamma_tileplanner';
	const AUTH_QUERY_VAR = 'gamma_tileplanner_auth';
	const RESET_KEY_QUERY_VAR = 'gamma_tileplanner_key';
	const RESET_LOGIN_QUERY_VAR = 'gamma_tileplanner_login';
	const STATUS_QUERY_VAR = 'gamma_tileplanner_status';

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
		$query_vars[] = self::AUTH_QUERY_VAR;
		$query_vars[] = self::RESET_KEY_QUERY_VAR;
		$query_vars[] = self::RESET_LOGIN_QUERY_VAR;
		$query_vars[] = self::STATUS_QUERY_VAR;

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
		$auth_view = get_query_var( self::AUTH_QUERY_VAR );
		$reset_key = get_query_var( self::RESET_KEY_QUERY_VAR );
		$reset_login = get_query_var( self::RESET_LOGIN_QUERY_VAR );
		$status = get_query_var( self::STATUS_QUERY_VAR );
		$password_reset_valid = false;

		if ( $auth_view === 'reset' && is_string( $reset_key ) && is_string( $reset_login ) ) {
			$password_reset_valid = ! is_wp_error( check_password_reset_key( $reset_key, $reset_login ) );
		}

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
				'auth_view'				=> is_string( $auth_view ) ? $auth_view : '',
				'password_reset_valid'	=> $password_reset_valid,
				'password_reset_key'	=> is_string( $reset_key ) ? $reset_key : '',
				'password_reset_login'	=> is_string( $reset_login ) ? $reset_login : '',
				'password_reset_error'	=> $status === 'error',
				'user_display_name'		=> wp_get_current_user()->display_name,
				'logout_url'			=> add_query_arg(
					[
						'action'   => 'gamma_tileplanner_logout',
						'_wpnonce' => wp_create_nonce( 'gamma_tileplanner_logout' ),
					],
					admin_url( 'admin-post.php' )
				),
			]
		);
	}

	public static function get_tileplanner_url() {
		$slug = self::get_tileplanner_route_slug();

		return home_url( $slug === '/' ? '/' : '/' . $slug . '/' );
	}

}
