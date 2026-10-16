<?php
namespace GammaTileplanner\Functionality;

class PasswordResetActions {

	public function __construct() {
		add_action( 'admin_post_nopriv_gamma_tileplanner_request_password_reset', [ $this, 'on_password_reset_request' ] );
		add_action( 'admin_post_gamma_tileplanner_request_password_reset', [ $this, 'on_password_reset_request' ] );
		add_action( 'admin_post_nopriv_gamma_tileplanner_reset_password', [ $this, 'on_password_reset' ] );
		add_action( 'admin_post_gamma_tileplanner_reset_password', [ $this, 'on_password_reset' ] );
	}

	public function on_password_reset_request() {
		if ( ! $this->verify_post_nonce( 'gamma_tileplanner_request_password_reset', 'gamma_tileplanner_request_password_reset_nonce' ) ) {
			$this->redirect_auth_view( 'forgot', [ Routes::STATUS_QUERY_VAR => 'error' ] );
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
				Routes::AUTH_QUERY_VAR => 'reset',
				Routes::RESET_KEY_QUERY_VAR => $key,
				Routes::RESET_LOGIN_QUERY_VAR => $user_login,
			],
			Routes::get_tileplanner_url()
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
				Routes::RESET_KEY_QUERY_VAR => $reset_key,
				Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				Routes::STATUS_QUERY_VAR => 'error',
			] );
		}

		$user = check_password_reset_key( $reset_key, $user_login );
		if ( is_wp_error( $user ) ) {
			$this->redirect_auth_view( 'reset', [
				Routes::RESET_KEY_QUERY_VAR => $reset_key,
				Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				Routes::STATUS_QUERY_VAR => 'error',
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
				Routes::RESET_KEY_QUERY_VAR => $reset_key,
				Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				Routes::STATUS_QUERY_VAR => 'error',
			] );
		}

		$errors = apply_filters( 'validate_password_reset', new \WP_Error(), $user );
		if ( is_wp_error( $errors ) && $errors->has_errors() ) {
			$this->redirect_auth_view( 'reset', [
				Routes::RESET_KEY_QUERY_VAR => $reset_key,
				Routes::RESET_LOGIN_QUERY_VAR => $user_login,
				'password_reset_error' => '1',
			] );
		}

		reset_password( $user, $password );
		$this->redirect_auth_view( 'reset_complete' );
	}

	private function verify_post_nonce( $action, $field ) {
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
		$query_args[ Routes::AUTH_QUERY_VAR ] = $view;
		wp_safe_redirect( add_query_arg( $query_args, Routes::get_tileplanner_url() ) );
		exit;
	}
}
