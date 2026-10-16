<?php
namespace GammaTileplanner\Functionality;

class RegistrationActions {

	public function __construct() {
		add_action( 'admin_post_nopriv_gamma_tileplanner_new_user', [ $this, 'on_new_user' ] );
	}

	public function on_new_user() {
		if ( ! isset( $_POST['gamma_tileplanner_new_user_nonce'] ) || ! wp_verify_nonce( $_POST['gamma_tileplanner_new_user_nonce'], 'gamma_tileplanner_new_user' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		$user_name = sanitize_text_field( $_POST['user_name'] ) ?? false;
		$user_email = sanitize_email( $_POST['user_email'] ) ?? false;
		$user_password = sanitize_text_field( $_POST['user_password'] ) ?? false;
		$cp = sanitize_text_field( $_POST['cp'] ) ?? false;
		$customer_type = sanitize_text_field( $_POST['customer_type'] ) ?? false;
		$profesional_type = sanitize_text_field( $_POST['profesional_type'] ) ?? false;

		if ( ! $user_name || ! $user_email || ! $user_password ) {
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
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );

			wp_redirect( wp_get_referer() );
		} else {
			$message = reset( $user_id->errors )[0];
			wp_redirect( add_query_arg( 'e', $message, wp_get_referer() . '/#register' ) );
		}

		exit;
	}
}
