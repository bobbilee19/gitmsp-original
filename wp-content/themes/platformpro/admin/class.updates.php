<?php 
class PageLinesUpdateCheck {

	function pagelines_check_version() {
	
		if (false === ($check = get_transient('pagelines_version_check'))) {

			$request = new WP_Http;
			$body = array( 'version' => CORE_VERSION );
			$url = 'http://api.pagelines.com';
			
			$check = $request->request( $url, array( 'method' => 'POST', 'body' => $body) );
			if( !is_wp_error($check) && $check['response']['code'] == 200 && $check['response']['message'] == 'OK' ) {
				
				// good response from server.
				global $latest_version;
				$latest_version = json_decode( $check['body'] );
				
				// Set transient, if there is an update set timeout to 1 hour else 12 hours.	
				$cachetime = ( is_array($latest_version) && $latest_version[0] > CORE_VERSION ) ? 3600 : 43200;
				set_transient( 'pagelines_version_check', $check, $cachetime );
			}
		} else { 
			
			global $latest_version;
			
			$latest_version = json_decode( $check['body'] );
			
			if ( is_array($latest_version) && $latest_version[0] > CORE_VERSION ) {
				
				add_filter( 'pagelines_options_array', array(&$this, 'update_tab') );
				
			}
			
		}
	}


	function update_tab( $option_array ) {

 		$update_option_array['update_available'] = array(
 			'updates' => array(
 			'type'		=> 'text_content',
 			'layout'	=> 'full',
 			'exp'		=> $this->update_page()
 			) );
 		return array_merge($option_array, $update_option_array);
	}

	function update_page() {
		global $latest_version;
		$version = CORE_VERSION;
		
		$update = '<div class="thelog">';
		
		$update .= sprintf('<div class="emph">%s version %s is here!</div>', THEMENAME, $latest_version[0]);
		$update .= '<p><a class="button-primary" href="http://www.pagelines.com/launchpad/member.php" target="_blank">Download Framework Update &rarr;</a></p>';
		
		$update .= '</div>';
		
		$update .= '<div class="thelog">';
		
		

		$update .= sprintf('<div class="emph">Changes in %s</div><br/> %s</p> <p><a class="button-secondary" href="%s" target="_blank">View Full Changelog &rarr;</a></p><br/><div class="emph subtle">Your current version: %s</div>',  $latest_version[0], $latest_version[1], CHANGELOG_URL, CORE_VERSION);
		
		$update .= '</div>';

		return $update;
	}

} // end class
