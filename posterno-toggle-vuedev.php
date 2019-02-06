<?php
/**
 * Plugin Name:     Posterno Toggle Vuedev
 * Plugin URI:      https://posterno.com
 * Description:     Toggle Vuejs development mode for Posterno directly from the WordPress admin bar.
 * Author:          Posterno, Alessandro Tesoro
 * Author URI:      https://posterno.com
 * Text Domain:     posterno-toggle-vuedev
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Posterno_Toggle_Vuedev
 */

/**
 * Handle the activation and deactivation of vuejs debug mode within the Posterno plugin.
 */
class PNO_Toggle_Vue {

	/**
	 * Flat to determine if debug is enabled or not.
	 *
	 * @var boolean
	 */
	public $debug_enabled = false;

	/**
	 * Initialize the properties.
	 */
	public function __construct() {

		$this->debug_enabled = $this->is_debug_mode_enabled();

	}

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'store' ] );
		add_action( 'admin_bar_menu', [ $this, 'admin_bar' ], 99 );
		add_action( 'after_setup_theme', [ $this, 'trigger' ], 20 );
	}

	/**
	 * Get current's page location url.
	 *
	 * @return string
	 */
	public function get_current_location() {
		if ( isset( $_SERVER['HTTPS'] ) &&
			( $_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1 ) ||
			isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}
		return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Create items into the admin bar to toggle the debug status.
	 *
	 * @param object $wp_admin_bar the admin bar object.
	 * @return void
	 */
	public function admin_bar( $wp_admin_bar ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->debug_enabled === true ) {

			$wp_admin_bar->add_node(
				array(
					'id'    => 'pno_vue_debug',
					'title' => 'Disable Vue Debug',
					'href'  => esc_url( $this->get_disable_url() ),
					'meta'  => array( 'class' => 'pno-debug-vue' ),
				)
			);

		} else {

			$wp_admin_bar->add_node(
				array(
					'id'    => 'pno_vue_debug',
					'title' => 'Enable Vue Debug',
					'href'  => esc_url( $this->get_enable_url() ),
					'meta'  => array( 'class' => 'pno-debug-vue' ),
				)
			);

		}

	}

	/**
	 * Get the url to trigger activation of debug mode.
	 *
	 * @return string
	 */
	private function get_enable_url() {
		return add_query_arg( [ 'pno-enable-vue-debug' => true ], $this->get_current_location() );
	}

	/**
	 * Get the url to trigger deactivation of debug mode.
	 *
	 * @return string
	 */
	private function get_disable_url() {
		return add_query_arg( [ 'pno-disable-vue-debug' => true ], $this->get_current_location() );
	}

	/**
	 * Detect if debug mode is enabled or not.
	 *
	 * @return boolean
	 */
	private function is_debug_mode_enabled() {

		if ( defined( 'PNO_VUE_DEV' ) ) {
			return (bool) PNO_VUE_DEV;
		}

		return (bool) get_option( 'posterno_vuejs_debug_mode', false );
	}

	/**
	 * Store the status chosen.
	 *
	 * @return void
	 */
	public function store() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$enable  = isset( $_GET['pno-enable-vue-debug'] ) && $_GET['pno-enable-vue-debug'] === '1' ? true : false;
		$disable = isset( $_GET['pno-disable-vue-debug'] ) && $_GET['pno-disable-vue-debug'] === '1' ? true : false;

		if ( $enable && ! $disable ) {

			update_option( 'posterno_vuejs_debug_mode', true );

			$url = remove_query_arg( 'pno-enable-vue-debug', $this->get_current_location() );

			wp_safe_redirect( $url );
			exit;

		} elseif ( $disable && ! $enable ) {
			delete_option( 'posterno_vuejs_debug_mode' );

			$url = remove_query_arg( 'pno-disable-vue-debug', $this->get_current_location() );

			wp_safe_redirect( $url );
			exit;
		}

	}

	/**
	 * Trigger debug status.
	 *
	 * @return void
	 */
	public function trigger() {

		if ( ! defined( 'PNO_VUE_DEV' ) && $this->debug_enabled === true ) {
			define( 'PNO_VUE_DEV', true );
		}

	}

}

( new PNO_Toggle_Vue() )->init();
