<?php
namespace GammaTileplanner\Functionality;

class AccessControl {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'redirect_non_admins_from_admin' ] );
		add_filter( 'show_admin_bar', [ $this, 'hide_admin_bar_for_non_admins' ] );
	}

	public function redirect_non_admins_from_admin() {
		global $pagenow;

		if (
			! is_user_logged_in()
			|| current_user_can( 'manage_options' )
			|| wp_doing_ajax()
			|| wp_doing_cron()
			|| in_array( $pagenow, [ 'admin-post.php', 'admin-ajax.php' ], true )
		) {
			return;
		}

		wp_safe_redirect( Routes::get_tileplanner_url() );
		exit;
	}

	public function hide_admin_bar_for_non_admins( $show ) {
		if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return $show;
	}
}
