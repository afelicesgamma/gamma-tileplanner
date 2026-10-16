<?php
namespace GammaTileplanner\Functionality;

class FrontendAssets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		if ( (string) get_query_var( Routes::QUERY_VAR ) !== '1' ) {
			return;
		}

		$manifest_path = GAMMATILEPLANNER_PATH . 'dist/manifest.json';
		$manifest_contents = is_readable( $manifest_path ) ? file_get_contents( $manifest_path ) : false;
		$manifest = is_string( $manifest_contents ) ? json_decode( $manifest_contents, true ) : null;
		$entry = is_array( $manifest ) ? ( $manifest['resources/scripts/app.ts'] ?? null ) : null;

		if ( ! is_array( $entry ) || ! isset( $entry['file'] ) || ! is_string( $entry['file'] ) || $entry['file'] === '' || ( isset( $entry['css'] ) && ! is_array( $entry['css'] ) ) ) {
			wp_die( esc_html__( 'Tileplanner assets are missing or the Vite manifest is invalid. Run the production build.', 'gamma-tileplanner' ) );
		}

		foreach ( $entry['css'] ?? [] as $index => $css_file ) {
			if ( ! is_string( $css_file ) || $css_file === '' ) {
				wp_die( esc_html__( 'The Vite manifest contains an invalid stylesheet path.', 'gamma-tileplanner' ) );
			}

			wp_enqueue_style(
				'gamma-tileplanner/app-' . $index,
				GAMMATILEPLANNER_URL . 'dist/' . ltrim( $css_file, '/' ),
				[],
				null
			);
		}

		wp_enqueue_script(
			'gamma-tileplanner/app.js',
			GAMMATILEPLANNER_URL . 'dist/' . ltrim( $entry['file'], '/' ),
			[],
			null,
			true
		);

		wp_localize_script( 'gamma-tileplanner/app.js', 'gamma_tileplanner_ajax', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'ajax-nonce' ),
		] );
	}
}
