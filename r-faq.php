<?php
	
/*
Plugin Name: rFAQ
Plugin URI: https://www.raphaelramos.com.br/wp/plugins/r-faq/
Description: FAQ - Perguntas e Respostas
Author: Raphael Ramos
Author URI: https://www.raphaelramos.com.br/
Version: 0.2
License: GPLv2 or later
Text Domain: r-faq
*/

	/***
	 *	Date: 2018-06-13
	 */
	
	define( 'R_FAQ_PATH',		plugin_dir_path( __FILE__ ) );
	define( 'R_FAQ_URL',		plugins_url( '', __FILE__ ) );
	define( 'R_FAQ_VERSION',	'0.2' );

	if( !class_exists( 'rCore' ) ){
		require_once 'inc/class/array.php';
		require_once 'inc/class/html.php';
		require_once 'inc/class/json.php';
		require_once 'inc/class/string.php';
	}
	
	require_once 'backend.php';
	if( class_exists( 'rFAQ_Backend' ) ){
		rFAQ_Backend::init();
		
		register_activation_hook( __FILE__, 'r_faq_activate' );
		function r_faq_activate(){
			rFAQ_Backend::activate();
		}

		register_deactivation_hook( __FILE__, 'r_faq_deactivate' );
		function r_faq_deactivate(){
			rFAQ_Backend::deactivate();
		}
	}

	require_once 'frontend.php';
	if( class_exists( 'rFAQ_Frontend' ) ){
		rFAQ_Frontend::init();
	}
