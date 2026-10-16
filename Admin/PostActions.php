<?php
namespace GammaTileplanner\Admin;

class PostActions {

	protected $plugin_name;
	protected $plugin_version;

	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->plugin_version = $plugin_version;
		$this->add_post_actions();
		add_action( 'admin_init', [ $this, 'redirect_subscribers_from_admin' ] );
		add_filter( 'show_admin_bar', [ $this, 'hide_admin_bar_for_subscribers' ] );
	}

	private function add_post_non_logged_in_action($action, $function) {
		add_action( "admin_post_nopriv_{$action}", array($this, $function) );
	}

	private function add_post_logged_in_action($action, $function) {
		add_action( "admin_post_{$action}", array($this, $function) );
	}

	private function add_post_action($action, $function) {
		$this->add_post_non_logged_in_action($action, $function);
		$this->add_post_logged_in_action($action, $function);
	}

	private function add_post_actions() {
		$this->add_post_non_logged_in_action( 'gamma_tileplanner_new_user', 'on_new_user' );
		$this->add_post_action( 'gamma_tileplanner_login', 'on_user_login' );
		$this->add_post_action( 'gamma_tileplanner_request_password_reset', 'on_password_reset_request' );
		$this->add_post_action( 'gamma_tileplanner_reset_password', 'on_password_reset' );
		$this->add_post_logged_in_action( 'gamma_tileplanner_logout', 'on_user_logout' );
	}

	public function redirect_subscribers_from_admin() {
		global $pagenow;

		if (
			! is_user_logged_in()
			|| current_user_can( 'edit_posts' )
			|| wp_doing_ajax()
			|| wp_doing_cron()
			|| in_array( $pagenow, [ 'admin-post.php', 'admin-ajax.php' ], true )
		) {
			return;
		}

		wp_safe_redirect( \GammaTileplanner\General\Routes::get_tileplanner_url() );
		exit;
	}

	public function hide_admin_bar_for_subscribers( $show ) {
		if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		return $show;
	}

	public function on_user_logout() {
		check_admin_referer( 'gamma_tileplanner_logout' );

		wp_logout();
		wp_safe_redirect( \GammaTileplanner\General\Routes::get_tileplanner_url() );
		exit;
	}

	public function on_user_login() {
		$redirect_to = isset( $_POST['redirect_to'] ) && is_string( $_POST['redirect_to'] )
			? wp_unslash( $_POST['redirect_to'] )
			: home_url( '/' );
		$redirect_to = wp_validate_redirect( $redirect_to, home_url( '/' ) );

		if (
			! isset( $_POST['gamma_tileplanner_login_nonce'] )
			|| ! is_string( $_POST['gamma_tileplanner_login_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_POST['gamma_tileplanner_login_nonce'] ), 'gamma_tileplanner_login' )
		) {
			$this->redirect_login( $redirect_to, true );
		}

		$user_login = isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] )
			? sanitize_user( wp_unslash( $_POST['user_login'] ) )
			: '';
		$user_password = isset( $_POST['user_password'] ) && is_string( $_POST['user_password'] )
			? wp_unslash( $_POST['user_password'] )
			: '';

		if ( $user_login === '' || $user_password === '' ) {
			$this->redirect_login( $redirect_to, true );
		}

		$user = wp_signon(
			[
				'user_login'    => $user_login,
				'user_password' => $user_password,
				'remember'      => isset( $_POST['rememberme'] ),
			],
			is_ssl()
		);

		if ( is_wp_error( $user ) ) {
			$this->redirect_login( $redirect_to, true );
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}

	private function redirect_login( $redirect_to, $has_error = false ) {
		if ( $has_error ) {
			$redirect_to = add_query_arg( 'login_error', '1', $redirect_to );
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}

	public function on_password_reset_request() {
		if ( ! $this->verify_post_nonce( 'gamma_tileplanner_request_password_reset', 'gamma_tileplanner_request_password_reset_nonce' ) ) {
			$this->redirect_auth_view( 'forgot', [ \GammaTileplanner\General\Routes::STATUS_QUERY_VAR => 'error' ] );
		}

		$user_login = isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] )
			? sanitize_text_field( wp_unslash( $_POST['user_login'] ) )
			: '';

		if ( $user_login !== '' && $this->allow_password_reset_request() ) {
			add_filter( 'retrieve_password_message', [ $this, 'filter_password_reset_message' ], 10, 4 );
			$result = retrieve_password( $user_login );
			remove_filter( 'retrieve_password_message', [ $this, 'filter_password_reset_message' ], 10 );

			if ( is_wp_error( $result ) && in_array( 'retrieve_password_email_failure', $result->get_error_codes(), true ) ) {
				error_log( 'Tileplanner could not send a password reset email.' );
			}
		}

		$this->redirect_auth_view( 'check_email' );
	}

	public function filter_password_reset_message( $message, $key, $user_login, $user_data ) {
		$reset_url = add_query_arg(
			[
				\GammaTileplanner\General\Routes::AUTH_QUERY_VAR => 'reset',
				\GammaTileplanner\General\Routes::RESET_KEY_QUERY_VAR => $key,
				\GammaTileplanner\General\Routes::RESET_LOGIN_QUERY_VAR => $user_login,
			],
			\GammaTileplanner\General\Routes::get_tileplanner_url()
		);
		$default_reset_url = network_site_url(
			'wp-login.php?action=rp&key=' . rawurlencode( $key ) . '&login=' . rawurlencode( $user_login ),
			'login'
		);

		if ( strpos( $message, $default_reset_url ) !== false ) {
			return str_replace( $default_reset_url, $reset_url, $message );
		}

		$reset_link_pattern = '~https?://[^\s<>]*wp-login\.php\?[^\s<>]*~i';
		$updated_message = preg_replace( $reset_link_pattern, $reset_url, $message, 1, $replacements );

		if ( $replacements > 0 ) {
			return $updated_message;
		}

		return sprintf(
			"A password reset has been requested for %1\$s on %2\$s.\n\nTo reset your password, visit this link:\n\n%3\$s\n",
			$user_login,
			wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
			$reset_url
		);
	}

	public function on_password_reset() {
		$user_login = isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] )
			? sanitize_text_field( wp_unslash( $_POST['user_login'] ) )
			: '';
		$reset_key = isset( $_POST['key'] ) && is_string( $_POST['key'] )
			? wp_unslash( $_POST['key'] )
			: '';

		if (
			$user_login === ''
			|| $reset_key === ''
			|| ! $this->verify_post_nonce( 'gamma_tileplanner_reset_password_' . $user_login, 'gamma_tileplanner_reset_password_nonce' )
		) {
			$this->redirect_auth_view( 'reset', [
				\GammaTileplanner\General\Routes::RESET_KEY_QUERY_VAR => $reset_key,
				\GammaTileplanner\General\Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				\GammaTileplanner\General\Routes::STATUS_QUERY_VAR => 'error',
			] );
		}

		$user = check_password_reset_key( $reset_key, $user_login );
		if ( is_wp_error( $user ) ) {
			$this->redirect_auth_view( 'reset', [
				\GammaTileplanner\General\Routes::RESET_KEY_QUERY_VAR => $reset_key,
				\GammaTileplanner\General\Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				\GammaTileplanner\General\Routes::STATUS_QUERY_VAR => 'error',
			] );
		}

		$password = isset( $_POST['password'] ) && is_string( $_POST['password'] )
			? wp_unslash( $_POST['password'] )
			: '';
		$password_confirmation = isset( $_POST['password_confirmation'] ) && is_string( $_POST['password_confirmation'] )
			? wp_unslash( $_POST['password_confirmation'] )
			: '';

		if ( $password === '' || $password !== $password_confirmation ) {
			$this->redirect_auth_view( 'reset', [
				\GammaTileplanner\General\Routes::RESET_KEY_QUERY_VAR => $reset_key,
				\GammaTileplanner\General\Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				\GammaTileplanner\General\Routes::STATUS_QUERY_VAR => 'error',
			] );
		}

		$errors = apply_filters( 'validate_password_reset', new \WP_Error(), $user );
		if ( is_wp_error( $errors ) && $errors->has_errors() ) {
			$this->redirect_auth_view( 'reset', [
				\GammaTileplanner\General\Routes::RESET_KEY_QUERY_VAR => $reset_key,
				\GammaTileplanner\General\Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				'password_reset_error' => '1',
			] );
		}

		reset_password( $user, $password );
		$this->redirect_auth_view( 'reset_complete' );
	}

	public function verify_post_nonce( $action, $field ) {
		return isset( $_POST[ $field ] )
			&& is_string( $_POST[ $field ] )
			&& wp_verify_nonce( wp_unslash( $_POST[ $field ] ), $action );
	}

	private function allow_password_reset_request() {
		if ( empty( $_SERVER['REMOTE_ADDR'] ) || ! is_string( $_SERVER['REMOTE_ADDR'] ) ) {
			return false;
		}

		$transient_key = 'gamma_tileplanner_password_reset_' . substr( wp_hash( $_SERVER['REMOTE_ADDR'] ), 0, 32 );
		$attempts = (int) get_transient( $transient_key );

		if ( $attempts >= 5 ) {
			return false;
		}

		set_transient( $transient_key, $attempts + 1, HOUR_IN_SECONDS );

		return true;
	}

	private function redirect_auth_view( $view, $query_args = [] ) {
		$query_args[ \GammaTileplanner\General\Routes::AUTH_QUERY_VAR ] = $view;
		wp_safe_redirect( add_query_arg( $query_args, \GammaTileplanner\General\Routes::get_tileplanner_url() ) );
		exit;
	}

	public function on_new_user() {

		if ( ! isset( $_POST['gamma_tileplanner_new_user_nonce'] ) || ! wp_verify_nonce( $_POST['gamma_tileplanner_new_user_nonce'], 'gamma_tileplanner_new_user' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {

			$user_name = sanitize_text_field( $_POST['user_name'] ) ?? false;
			$user_email = sanitize_email( $_POST['user_email'] ) ?? false;
			$user_password = sanitize_text_field( $_POST['user_password'] ) ?? false;
			$cp = sanitize_text_field( $_POST['cp'] ) ?? false;
			$customer_type = sanitize_text_field( $_POST['customer_type'] ) ?? false;
			$profesional_type = sanitize_text_field( $_POST['profesional_type'] ) ?? false;

			if ( !$user_name || !$user_email || !$user_password ) {
				die();
			}

			$userdata = [
				'user_login' => $user_name,
				'user_pass' => $user_password,
				'user_email' => $user_email,
				'role' => 'subscriber',
			];

			$user_id = wp_insert_user( wp_slash( $userdata ) );

			if ( ! is_wp_error( $user_id ) ) {
				carbon_set_user_meta( $user_id, 'cp', $cp );
				carbon_set_user_meta( $user_id, 'customer_type', $customer_type );
				carbon_set_user_meta( $user_id, 'profesional_type', $profesional_type );

				wp_clear_auth_cookie();
		        wp_set_current_user ( $user_id );
		        wp_set_auth_cookie  ( $user_id );

				wp_redirect( wp_get_referer() );

			} else {
				$message = reset($user_id->errors)[0];
				wp_redirect( add_query_arg( 'e', $message, wp_get_referer() . '/#register' ) );
			}

			exit();

		}
	}

}
