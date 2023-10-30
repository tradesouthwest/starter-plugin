<?php 
/**
 * Plugin Name Admin Helpers.
 *
 * @author 		Your Name / Your Company Name
 * @category 	Admin
 * @package 	Plugin Name/includes
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option
	 * @return string
	 */
function starter_plugin_get_option( $option_name, $default = '' ) {
	// Array value
	if ( strstr( $option_name, '[' ) ) {

		parse_str( $option_name, $option_array );

		// Option name is first key
		$option_name = current( array_keys( $option_array ) );

		// Get value
		$option_values = get_option( $option_name, '' );

		$key = key( $option_array[ $option_name ] );

		if ( isset( $option_values[ $key ] ) ) {
			$option_value = $option_values[ $key ];
		}
		else {
			$option_value = null;
		}

	// Single value
	} else {
		$option_value = get_option( $option_name, null );
	}

	if ( is_array( $option_value ) ) {
		$option_value = array_map( 'stripslashes', $option_value );
	}
	elseif ( ! is_null( $option_value ) ) {
		$option_value = stripslashes( $option_value );
	}

      return $option_value === null ? $default : $option_value;
}
/**
 * Supported ob_end_flush() for all levels
 *
 * This replaces the WordPress `wp_ob_end_flush_all()` function
 * with a replacement that doesn't cause PHP notices.
 */
// @TODO Conditional options boolean to start can be added here. @uses starter_plugin_get_option()
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
   while ( @ob_end_flush() );
} );

if ( ! function_exists( 'starter_plugin_is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @access public
	 * @return bool
	 */
	function starter_plugin_is_ajax() {
		if ( defined('DOING_AJAX') ) {
			return true;
		}

		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
	}
}

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 *
 * Doesn't use sanitize_title as this destroys utf chars.
 *
 * @access public
 * @param mixed $taxonomy
 * @return string
 */
function starter_plugin_sanitize_taxonomy_name( $taxonomy ) {
	$filtered = strtolower( remove_accents( stripslashes( strip_tags( $taxonomy ) ) ) );
	$filtered = preg_replace( '/&.+?;/', '', $filtered ); // Kill entities
	$filtered = str_replace( array( '.', '\'', '"' ), '', $filtered ); // Kill quotes and full stops.
	$filtered = str_replace( array( ' ', '_' ), '-', $filtered ); // Replace spaces and underscores.

	return apply_filters( 'sanitize_taxonomy_name', $filtered, $taxonomy );
}
