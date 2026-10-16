<?php
namespace GammaTileplanner\Functionality;

class LoginActions {

	public function __construct() {
		add_action( 'admin_post_nopriv_gamma_tileplanner_login', [ $this, 'on_user_login' ] );
		add_action( 'admin_post_gamma_tileplanner_login', [ $this, 'on_user_login' ] );
		add_action( 'admin_post_gamma_tileplanner_logout', [ $this, 'on_user_logout' ] );
	}

	public function on_user_logout() {
		check_admin_referer( 'gamma_tileplanner_logout' );

		wp_logout();
		wp_safe_redirect( Routes::get_tileplanner_url() );
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
}
