<?php
/**
 * 
 *
 *  PageLines Debugging Information Class
 *
 *
 *  @package Platform Framework
 *  @subpackage Includes
 *  @since 1.4.0
 *
 */
class PageLinesDebug {

	// Array of debugging information
	var $debug_info = array();
	
	/**
	 * PHP5 constructor
	 * Use this to build the initial form of the object, before its manipulated by methods
	 *
	 */
	function __construct( ) {
	
		$this->wp_debug_info();
	}
	

	function debug_info_template(){
		
		$out = '';
		foreach($this->debug_info as $element ) {
			
			if ( $element['value'] ) {
				
				$out .= '<h4>'.ucfirst($element['title']).' : '. ucfirst($element['value']);
				$out .= (isset($element['extra'])) ? "<br /><code>{$element['extra']}</code>" : '';
				$out .= '</h4>';
			}
		}
	return $out;		
	}

	// Build all the debug info into an array.

	function wp_debug_info(){
		
		global $wpdb, $wp_version, $platform_build;
		
			// Set data & variables first
			$uploads = wp_upload_dir();
			// Get user role
			$current_user = wp_get_current_user();
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);

		
			// Format data for processing by a template
		
			$this->debug_info[] = array(
				'title'	=> "WordPress Version",
				'value' => $wp_version, 
				'level'	=> false,
			);
		
			$this->debug_info[] = array(
				'title'	=> "Multisite Enabled",
				'value' => ( is_multisite() ) ? 'Yes' : 'No',
				'level'	=> false
			);
		
			$this->debug_info[] = array(
				'title'	=> "Current Role",
				'value' => $user_role,
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Theme Path",
				'value' => '<code>' . TEMPLATEPATH . '</code>',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Theme URI",
				'value' => '<code>' . get_template_directory_uri() . '</code>',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Platform Version",
				'value' => CORE_VERSION,
				'level'	=> false
			);
			$this->debug_info[] = array(
				'title'	=> "Platform Build",
				'value' => $platform_build ,
				'level'	=> false
			);
			$this->debug_info[] = array(
				'title'	=> "PHP Version",
				'value' => floatval( phpversion() ),
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Child theme",
				'value' => ( TEMPLATEPATH != STYLESHEETPATH ) ? 'Yes' : '',
				'level'	=> false,
				'extra' => STYLESHEETPATH . '<br />' . get_stylesheet_directory_uri()
			);

			$this->debug_info[] = array(
				'title'	=> "Ajax disbled",
				'value' => ( get_pagelines_option( 'disable_ajax_save' ) ) ? 'Yes':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "CSS Inline",
				'value' => ( get_pagelines_option( 'inline_dynamic_css' ) ) ? 'Yes':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "CSS Error",
				'value' => ( !get_pagelines_option( 'inline_dynamic_css' ) && !is_multisite() && !is_writable( PAGELINES_DCSS ) ) ? 'File is not writable!':'',
				'level'	=> false
			);


			$this->debug_info[] = array(
				'title'	=> "PHP Safe Mode",
				'value' => ( (bool) ini_get('safe_mode') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "PHP Open basedir restriction",
				'value' => ( (bool) ini_get('open_basedir') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "PHP Register Globals",
				'value' => ( (bool) ini_get('register_globals') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "PHP Magic Quotes gpc",
				'value' => ( (bool) ini_get('magic_quotes_gpc') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "PHP low memory",
				'value' => ( !ini_get('memory_limit') || ( intval(ini_get('memory_limit')) <= 32 ) ) ? intval(ini_get('memory_limit') ):'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Pagelines API",
				'value' => ( !is_array( $this->debug_test_http() ) ) ? 'No' : 'Yes',
				'level'	=> false,
				'extra' => ( is_wp_error( $this->debug_test_http() ) ) ? $this->debug_test_http()->get_error_message() : 'Result: ' . array_shift( json_decode( array_shift( array_splice( $this->debug_test_http(), 1, 1 ) ) ) ) 
			);

			$this->debug_info[] = array(
				'title'	=> "Mysql version",
				'value' => ( version_compare( $wpdb->get_var("SELECT VERSION() AS version"), '5' ) < 0  ) ? $wpdb->get_var("SELECT VERSION() AS version"):'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Upload DIR",
				'value' => ( !is_writable( $uploads['path'] ) ) ? "Unable to write to <code>{$uploads['subdir']}</code>":'',
				'level'	=> true,
				'extra' => $uploads['path']
			);

			$this->debug_info[] = array(
				'title'	=> "PHP type",
				'value' => php_sapi_name(),
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "OS",
				'value' => PHP_OS,
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> "Plugins",
				'value' => $this->debug_get_plugins(),
				'level'	=> false
			);

	}
	

	function debug_get_plugins() {
		$plugins = get_option('active_plugins');
		if ( $plugins ) {
			$plugins_list = '';
			foreach($plugins as $plugin_file) {
					$plugins_list .= '<code>' . $plugin_file . '</code>';
					$plugins_list .= '<br />';

			}
			return ( isset( $plugins_list ) ) ? count($plugins) . "<br />{$plugins_list}" : '';
		}
	}


	// Check wordpress core http fetch is available by querying our own API
	function debug_test_http() {

		if ( $check = get_transient('pagelines_debug_http_check') ) {
			return $check; 
		}
		$debugtest = new WP_Http;
		$body = array( 'version' => CORE_VERSION, 'debug' => true );
		$url = 'http://api.pagelines.com';
		$check = $debugtest->request( $url, array( 'method' => 'POST', 'body' => $body) );
		if ( !is_wp_error($check) && $check['response']['code'] == 200 && $check['response']['message'] == 'OK' ) set_transient( 'pagelines_debug_http_check', $check, 3600 ); // expire 1 hour
		return $check;
	}
//-------- END OF CLASS --------//
}