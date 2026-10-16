<?php
namespace GammaTileplanner\Functionality;

class BackendMenu {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'hide_menus' ] );
		add_action( 'admin_bar_menu', [ $this, 'remove_toolbar_nodes' ], 999 );
	}

	public function hide_menus() {
		remove_menu_page( 'index.php' );
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'upload.php' );
		remove_menu_page( 'edit.php?post_type=page' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'tools.php' );
		//remove_menu_page( 'options-general.php' );
	}

	public function remove_toolbar_nodes( $admin_bar ) {
		foreach ( [ 'wp-logo', 'comments', 'new-content', 'updates' ] as $node_id ) {
			if ( $admin_bar->get_node( $node_id ) ) {
				$admin_bar->remove_node( $node_id );
			}
		}
	}
}
