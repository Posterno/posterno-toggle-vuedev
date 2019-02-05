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

class PNO_Toggle_Vue {

	public function init() {

		add_action( 'admin_bar_menu', [ $this, 'admin_bar' ], 99 );

	}

	public function admin_bar( $wp_admin_bar ) {

		$wp_admin_bar->add_node(
			array(
				'id'    => 'pno_vue_debug',
				'title' => 'Vue Debug Toggle',
				'href'  => '#',
				'meta'  => array( 'class' => 'pno-debug-vue' ),
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'pno_vue_debug',
				'id'     => 'pno_vue_debug-true',
				'title'  => 'Enable debug',
				'href'   => '#',
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'pno_vue_debug',
				'id'     => 'pno_vue_debug-false',
				'title'  => 'Disable debug',
				'href'   => '#',
			)
		);

	}

}

( new PNO_Toggle_Vue() )->init();
