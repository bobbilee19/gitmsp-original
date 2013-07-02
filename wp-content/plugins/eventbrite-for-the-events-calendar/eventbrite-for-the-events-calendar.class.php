<?php
 // Test for SP Events Calendar plugin. Make smarter later, with message to user about installing SP Events Calendar first.
 if ( !class_exists( 'Eventbrite_for_The_Events_Calendar' ) ) {
	/**
	 * Main Plugin
	 */
	class Eventbrite_for_The_Events_Calendar {
		
		/**************************************************************
		 * EventBrite Configuration
		 **************************************************************/
		const EVENTBRITETIMEFORMAT	= 'c'; // ISO 8601
		const EVENTBRITEAPIBASEURL	= 'www.eventbrite.com/xml/';	// json support only default in PHP 5.2
		public $errors;
		// All events are private while we test this plugin
		public $eventBritePrivacy	= 0;
		public $eventBriteTimezone;
		public $eventBriteTransport;	// https if supported, otherwise http
		public $eventBriteApiUrl;
		public $pluginDir;
		public $pluginDomain		= 'eventbrite-for-the-events-calendar';
		public $pluginVersion		= '1.6';
		
		public $metaTags = array(
			'_EventBriteId',			// ID in EventBrite of this event
			'_EventBriteTicketName',
			'_EventBriteTicketDescription',
			'_EventBriteIsDonation',
			'_EventBriteTicketQuantity',
			'_EventBriteIncludeFee',
			'_EventBriteStatus',
			'_EventRegister',		// Whether to register this event with EventBrite
			'_EventBritePayment-accept_paypal',
			'_EventBritePayment-paypal_email',
			'_EventBritePayment-accept_google',
			'_EventBritePayment-google_merchant_id',
			'_EventBritePayment-google_merchant_key',
			'_EventBritePayment-accept_check',
			'_EventBritePayment-instructions_check',
			'_EventBritePayment-accept_cash',
			'_EventBritePayment-instructions_cash',
			'_EventBritePayment-accept_invoice',
			'_EventBritePayment-instructions_invoice'
		);
				
		function __construct() {
			$this->pluginDir    = basename(dirname(__FILE__));
			$this->add_actions();
			$this->add_filters();
			$this->add_shortcodes();
		}
		
		private function add_actions() {
			add_action( 'init', array( $this, 'initEventBrite' ) );
			add_action( 'init', array( $this, 'loadDomainStylesScripts' ) );
			add_action( 'sp_events_options_bottom', array( $this, 'eventBriteOptions' ) );
			add_action( 'sp_events_save_more_options', array( $this, 'saveOptions' ) );
			add_action( 'sp_events_event_save', array( $this, 'addEventBriteDetails' ) );
			add_action( 'sp_events_update_meta', array( $this, 'addEventMeta' ) );
			add_action( 'sp_events_event_clear', array( $this, 'clearEventBriteDetails' ) );
			add_action( 'show_user_profile',array( $this, 'userProfilePage' ) );
			add_action( 'edit_user_profile',array( $this, 'userProfilePage' ) );
			add_action( 'edit_user_profile_update', array( $this, 'userProfileUpdate' ) );
			add_action( 'personal_options_update', array( $this, 'userProfileUpdate' ) );
			add_action( 'sp_events_above_donate', array( $this, 'eventBriteMetaBox') );
			add_action( 'sp_events_detail_top', array( $this, 'displayExistingEventToggle' ) );
			add_action( 'wp_ajax_existingEventId', array( $this, 'retrieveExistingEventData' ));
		}
		
		private function add_filters() {
			add_filter( 'the_content', array( $this, 'displayEventBriteTicketForm' ) );
		}
		
		private function add_shortcodes() {
			add_shortcode( 'eventbrite' , array( $this, 'shortCodeforTickets' ) );
		}
		
		public function loadDomainStylesScripts() {
			load_plugin_textdomain( $this->pluginDomain, false, $this->pluginDir . '/lang/');
			wp_enqueue_style( 'eb-tec-style', WP_PLUGIN_URL . '/eventbrite-for-the-events-calendar/resources/eb-tec-style.css', array(), $this->pluginVersion, 'screen' );
		}
		
		/**
		 * Inits the eventbrite settings
		 */
		public function initEventBrite( ) {
			// use SSL if we support it
			$version = curl_version();
			$this->eventBriteTransport = ($version['features'] & CURL_VERSION_SSL ? 'https' : 'http'); 
			$this->eventBriteApiUrl = $this->eventBriteTransport . '://' . Eventbrite_for_The_Events_Calendar::EVENTBRITEAPIBASEURL;
		}
		public function userProfilePage( $profileuser ) {
			require_once( dirname( __FILE__ ) . '/views/user-profile.php' );
		}
		public function userProfileUpdate( $user_id ) {
			if( isset( $_POST['eventbrite_user_key'] ) ) {
				update_usermeta( $user_id, 'eventbrite_user_key', $_POST['eventbrite_user_key'] );
			}
		}
		
		public function displayExistingEventToggle() {
			global $spEvents;
			include( WP_PLUGIN_DIR . '/eventbrite-for-the-events-calendar/views/add-existing-event.php');
		}
		
		public function addEventMeta( $postId ) {
			if( WP_DEBUG ) error_log( "addEventMeta: " . print_r( $_POST, true ) );
			if ( ( $_POST['EventRegister'] == 'yes' && !get_post_meta( $postId, '_EventRegister', true) ) || ( $_POST['existingEBEvent'] == 'yes' && $_POST['ExistingEventId'] ) ) {
				if( $_POST['existingEBEvent'] == 'yes' && $_POST['ExistingEventId'] ) update_post_meta( $postId, '_EventBriteId', $_POST['ExistingEventId'] );
				// ignore these keys
				$notFromEventBriteForm = array(
											'_EventBriteId',
											'_EventBriteStatus'
										 );
				foreach( $this->metaTags as $key ) {
					// the added test here should prevent overwriting of post meta fields on event update
					if( in_array( $key, $notFromEventBriteForm ) || !$_POST[$postVarKey = substr_replace( $key, '', 0, 1)] ) continue;
					update_post_meta( $postId, $key, $_POST[$postVarKey] );
				}
			} elseif( !isset( $_POST['EventRegister'] ) || empty( $_POST['EventRegister'] ) ) {
				delete_post_meta( $postId, '_EventRegister' );
			}
		}
		/**
		 * Updates the eventbrite information in wordpress and makes the 
		 * API calls to EventBrite to update the listing on their side
		 *
		 * @link http://www.eventbrite.com/api/doc/
		 * @param int post id
		 * @uses $_POST 
		 */
		public function addEventBriteDetails( $postId ) {
			if( !isset( $_POST['EventRegister'] ) || $_POST['EventRegister'] != 'yes' ) {
				if( WP_DEBUG ) error_log( "clearing eventbrite details for post $postId" );
				$this->clearEventBriteDetails( $postId );
				return;
			}
			if( $_POST['existingEBEvent'] == 'yes' && $_POST['ExistingEventId'] ) return;
			$EventBriteId = get_post_meta( $postId, '_EventBriteId', true );
			if( WP_DEBUG ) error_log( "addEventBriteDetails for postId with eventbrite id: $EventBriteId" );
			if( $EventBriteId ) {
				// update event brite
				if( WP_DEBUG ) error_log( "updating eventbrite details: " . print_r( $_POST, true ) );
				$this->updateEventBrite( $postId, $EventBriteId, $_POST );
			}
			if( !$EventBriteId && $_POST['EventRegister'] ) {
				global $spEvents;
				if( WP_DEBUG ) error_log( "create event with eventbrite API" );
				// create new eventbrite event
				$eventId = $this->createEventBrite( $postId, $_POST );
				if( $eventId ) {
					if( WP_DEBUG ) error_log( "created with id: " . print_r( $eventId, true ) );
					if( WP_DEBUG ) error_log( "creating eventbrite ticket" );
					$this->createEventBriteTicket( $postId, $eventId, $_POST );
					$this->udpatePaymentOptions( $postId, $eventId, $_POST );
				} else {
					if( WP_DEBUG ) error_log( "failed to create eventbrite event" );
				}
				if( !empty( $this->errors ) ) {
					if( WP_DEBUG ) error_log( $this->errors );
					update_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT, trim( $this->errors) );
				}
			}
		}
		
		public function clearEventBriteDetails( $postId ) {
			// TODO delete the event using the eventbrite API
			foreach( $this->metaTags as $meta ) {
				delete_post_meta( $postId, $meta );
			}
		}

		/**
		 * handles eventbrite ticket creation
		 */
		private function createEventBriteTicket( $postId, $eventId, $_POST ) {
			if( !(int)$eventId ) {
				return false;
			}
			$request = 'event_id=' . (int)$eventId;
			if( empty( $_POST['EventBriteTicketName'] ) ) {
				return false;
			}
			$request .= '&name=' . urlencode( $_POST['EventBriteTicketName'] );
			if( $_POST['EventBriteIsDonation'] == 1 ) {
				$request .= '&price=';
				$request .= '&is_donation=1';
			} elseif ( $_POST['EventBriteIsDonation'] == 0 && !is_numeric( $_POST['EventCost'] ) ) {
				return false;
			} else {
				$request .= '&price=' . (float)$_POST['EventCost'];
				$request .= '&is_donation=0';
			}
			if( !is_numeric( $_POST['EventBriteTicketQuantity'] ) ) {
				return false;
			} else {
				$request .= '&quantity=' . (int)$_POST['EventBriteTicketQuantity'];
			}
			if( !empty( $_POST['EventBriteTicketDescription'] ) ) {
				$request .= '&description=' . urlencode( $_POST['EventBriteTicketDescription'] );
			}
			$request .= '&include_fee=' . (int)$_POST['EventBriteIncludeFee'];
			$response_arr = $this->sendEventBriteRequest( 'ticket_new', $request, $postId );
		}
				
		/**
		 * handles eventbrite updates
		 */
		private function updateEventBrite( $postId, $eventBriteId, $_POST ) {
			$post = get_post( $postId );
			if( $post->post_type == 'revision' ) throw new TEC_Post_Exception(__('This post is a revision and cannot update an Evenbrite event.', $this->pluginDomain));
			else delete_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT );
			$requestParams = '';
			$requestParams .= 'event_id=' . $eventBriteId;
			if( empty( $_POST['post_title'] ) ) {
				global $spEvents;
				$this->errors .= 'Event must have a title';
				throw new TEC_Post_Exception(__('Please supply a post title. This will become the Eventbrite event title.', $this->pluginDomain));
			}
			$requestParams .='&title=' . urlencode( stripslashes( $_POST['post_title'] ) );
			if( !empty( $_POST['content'] ) ) {
				$requestParams .= '&description=' . urlencode( stripslashes( $_POST['content'] ) );
			}
			$requestParams .= '&start_date=' . urlencode( $_POST['EventStartDate'] );
			$requestParams .= '&end_date=' . urlencode( $_POST['EventEndDate'] );
			$requestParams .= '&timezone=GMT' . urlencode( get_option( 'gmt_offset' ) );
			$requestParams .= '&status=' . urlencode( $_POST['EventBriteStatus'] );
			$response_arr = $this->sendEventBriteRequest( 'event_update', $requestParams, $postId );
			if( isset( $response_arr['process']['id'] ) ) {
				update_post_meta( $postId, '_EventBriteId', $response_arr['process']['id'], true );
			}
		}

		/**
		 * handles eventbrite creation
		 *
		 * @return mixed event brite id or false
		 */
		private function createEventBrite( $postId, $_POST ) {
			$post = get_post( $postId );
			if( $post->post_type == 'revision' ) throw new TEC_Post_Exception(__('This post is a revision and cannot create an Evenbrite event.', $this->pluginDomain));
			else delete_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT );
			$requestParams = '';
			if( empty( $_POST['post_title'] ) ) {
				global $spEvents;
				$this->errors .= 'Event must have a title';
				if( WP_DEBUG ) error_log( $this->errors );
				throw new TEC_Post_Exception(__('Please supply a post title. This will become the Eventbrite event title.', $this->pluginDomain));
			}
			$requestParams .='title=' . urlencode( stripslashes( $_POST['post_title'] ) );
			if( !empty( $_POST['content'] ) ) {
				$requestParams .= '&description=' . urlencode( stripslashes( $_POST['content'] ) );
			}
			$requestParams .= '&start_date=' . urlencode( $_POST['EventStartDate'] );
			$requestParams .= '&end_date=' . urlencode( $_POST['EventEndDate'] ); 
			$requestParams .= '&timezone=GMT' . urlencode( get_option( 'gmt_offset' ) );
			$response_arr = $this->sendEventBriteRequest( 'event_new', $requestParams, $postId );
			if( isset( $response_arr['process']['id'] ) ) {
				update_post_meta( $postId, '_EventBriteId', $response_arr['process']['id'], true );
				return $response_arr['process']['id'];
			} else {
				return false;
			}
		}
		
		/**
		 * retrieves data from an existing EventBrite event
		 *
		 * @return a json of the event data or false
		 */
		public function retrieveExistingEventData() {			
			$post = get_post( $_POST['postId'] );		
			if( $post->post_type == 'revision' ) throw new TEC_Post_Exception(__('This post is a revision and cannot retrieve EventBrite data.', $this->pluginDomain));
			else delete_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT );
			$requestParams = 'id=' . $_POST['existingEventId'];
			$event = $this->sendEventBriteRequest( 'event_get', $requestParams, $_POST['postId'], true );
			if( isset( $event['event']['id'] ) ) {
				// match EB api keys with input names
				$matchedKeysNames = array(  'start_date' 			=> 	'EventStartDate',
											'end_date' 				=>	'EventEndDate',
											'tags'					=>	'newtag[post_tag]',
											'title'					=>	'post_title',
											'description'			=>	'content',
											'venue'					=>	array(
												'name'					=>	'EventVenue',
												'country'				=> 	'EventCountry',
												'address'				=>	'EventAddress',
												'address_2'				=>	'EventAddress',
												'city'					=>	'EventCity',
												'region'				=>	'EventState',
												'postal_code'			=>	'EventZip' ) );
				// construct output array from EB response
				$output = array();
				foreach ( $matchedKeysNames as $key => $val ) {
					$responseValue = $event['event'][$key];
					if ( is_array( $val ) ) {
							foreach( $val as $key2 => $val2 ) {
								// hack to concat EventAddress
								if( $key2 == 'address_2' ) $output[$val2] .= ', ' . $event['event'][$key][$key2];
								else $output[$val2] = $event['event'][$key][$key2]; 
							}
					} elseif ( $val == 'EventStartDate' || $val == 'EventEndDate' ) {
						// format time output
						$startEnd = str_replace( array('Event','Date'), '', $val );
						$responseValueArray = explode('-',$responseValue);
						$output['Event'.$startEnd.'Year'] = $responseValueArray[0];
						$output['Event'.$startEnd.'Month'] = $responseValueArray[1];
						$dateAndTime = explode( ' ', $responseValueArray[2] );
						$output['Event'.$startEnd.'Day'] = $dateAndTime[0];
						$time = explode( ':', $dateAndTime[1] );
						$output['Event'.$startEnd.'Minute'] = $time[1];
						$timeFormat = get_option( 'time_format' );
						if( strstr( $timeFormat, 'H' ) ) $output['Event'.$startEnd.'Hour'] = $time[0];
						else {
							if( $time[0] > 12 ) {
								$time[0] -= 12;
								if( $time[0] < 10 ) $time[0] = '0' . $time[0];
								$amPm = ( strstr( $timeFormat, 'a' ) ) ? 'pm' : 'PM' ;
							} else $amPm = ( strstr( $timeFormat, 'a' ) ) ? 'am' : 'AM' ;
							$output['Event'.$startEnd.'Hour'] = $time[0];
							$output['Event'.$startEnd.'Meridian'] = $amPm;
						}
					} elseif ( $val == 'content' ) {
						$output[$val] = html_entity_decode($responseValue);
					} else $output[$val] = $responseValue;
				}
				update_post_meta( $postId, '_EventBriteId', $event['event']['id'], true );
				$EventBriteId = $event['event']['id'];
				// construct tickets display
				ob_start();
				include( 'views/eventbrite-events-table.php' );
				// add css
				$tdCss = "font-size:11px;padding:6px 6px 0 0;vertical-align:middle";
				$h4Css = "border-bottom:1px solid #E5E5E5;font-size:1.2em;margin:2em 0 1em;padding-bottom:6px;text-transform:uppercase";
				$output['eventbriteEventsTable'] = str_replace( array('<td','<h4'), array('<td style="' . $tdCss . '"','<h4 style="' . $h4Css . '"'), ob_get_clean() );
				$output['EventCost'] = ( $ebTecMultipleCosts ) ? __('Varies', $this->pluginDomain) : $ebTecLastPrice;
				$output['isPublishing'] = ( $_POST['clickedId'] == 'publish' ) ? true : false;
				exit( json_encode( $output ) );
			} else {
				throw new TEC_Post_Exception(__('We were unable to import your EventBrite event. Please verify the event id and try again.', $this->pluginDomain));
			}
		}
		/**
		 * handles Eventbrite payment_update api call
		 *
		 * @param string $_POST variable
		 * @param int $post->ID
		 * @param int EventBrite event id
		 * @link http://www.eventbrite.com/api/doc/payment_update
		 */
		public function udpatePaymentOptions( $postId, $eventId, $_POST ) {
			$paymentTypes = array('paypal'=>'','google'=>'','check'=>'','cash'=>'','invoice'=>'');
			$requestParams = 'event_id=' . $eventId;
			foreach( $paymentTypes as $key => $val ) {
				$onOff = $_POST['EventBritePayment-accept_' . $key];
				if( $onOff ) $requestParams .= '&accept_' . $key . '=' . $onOff;
				$paymentTypes[$key] = $onOff;
			}
			foreach( $paymentTypes as $key => $val ) {
				if( $val ) {
					switch( $key ) {
						case 'paypal':
							$requestParams .= '&paypal_email=' . $_POST['EventBritePayment-paypal_email'];
							break;
						case 'google':
							$requestParams .= '&google_merchant_id=' . $_POST['EventBritePayment-google_merchant_id'] . '&google_merchant_key=' . $_POST['EventBritePayment-google_merchant_key'];
							break;
						default:
							$instructions = $_POST['EventBritePayment-instructions_'.$key];
							if( $instructions ) $requestParams .= '&instructions_' . $key . '=' . urlencode($instructions);
					}
				}
			}
			$response_arr = $this->sendEventBriteRequest( 'payment_update', $requestParams, $postId );
		}
		
		public function printablePaymentOption( $arrayMember ) {
			switch( $arrayMember ) {
				case '_EventBritePayment-accept_paypal':
					return 'PayPal Payments';
					break;
				case '_EventBritePayment-accept_google':
					return 'Google Checkout Payments';
					break;
				case '_EventBritePayment-accept_check':
					return 'Pay by check';
					break;
				case '_EventBritePayment-accept_cash':
					return 'Pay at the door';
					break;
				case '_EventBritePayment-accept_invoice':
					return 'Send an invoice';
					break;
				default:
					return false;
			}
		}
		
		/**
		 * Sends a request to eventbrite and handles the response
		 *
		 * @param string action
		 * @param string parameters
		 * @return bool
		 * @link http://www.eventbrite.com/api/doc/
		 */
		public function sendEventBriteRequest( $action, $params, $postId, $isEbImport = false ) {
			try {
				// test if new post or not
				if( $postId > 0 ) {
					$post = get_post( $postId );
					$EventBriteUserKey = get_usermeta( $post->post_author, 'eventbrite_user_key' );
				} elseif ( $isEbImport ) {
					// this clause not needed as of WP 3.0 +
					global $user_ID;
					get_currentuserinfo();
					$EventBriteUserKey = get_usermeta( $user_ID, 'eventbrite_user_key' );
				}
				if( !$EventBriteUserKey ) {
					if( WP_DEBUG ) error_log( "no eventbrite credentials for this post" );
					return;
				}
				$request = $this->eventBriteApiUrl . $action . '?' . $this->eventsGetThisEvent();
				$request .= '&user_key='  .urlencode( $EventBriteUserKey );
				$request .= '&' . $params;
				if( WP_DEBUG ) error_log( "sending eventbrite request: " . print_r( $request, true ) );
				$ch = curl_init( );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				//TODO, IS THIS NEEDED TOO?
			    //if( !ini_get('safe_mode') && !ini_get('open_basedir') ) {
				if( !ini_get('safe_mode') ) {
			    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			    }
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_URL, htmlentities( $request ) );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,false );
				$curl_output = curl_exec( $ch );
				$return_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				if ( curl_error( $ch ) ) {
					global $spEvents;
					$this->errors .= curl_error( $ch ) . "<br />";
					throw new TEC_Post_Exception(__('An error occurred while contacting Eventbrite. Please review your information and try again.<br />Error: ', $this->pluginDomain) . curl_error($ch));
				}
				curl_close( $ch );
				if ( $return_code == 0 || $return_code == 500 || $return_code == 400 ) {
					global $spEvents;
					$this->errors .= __( "Failed to get", $this->pluginDomain ) . " $request (Status: $return_code)<br/>";
					throw new TEC_Post_Exception(__('An error occurred while contacting Eventbrite. Please review your information and try again.<br />Status: ', $this->pluginDomain) . $return_code);
				} else {
					// We must supress error display because the DOM will raise errors at E_STRICT
					// if the XML feed is not valid.
					ini_set( 'display_errors', 'Off' );
					$dom = new DOMDocument( '1.0', 'iso-8859-1' );
					if( $dom->loadXML( $curl_output, LIBXML_DTDVALID ) ) {
						$parseReturn = $this->parseEventBriteResponse( $curl_output, $postId );
						return $parseReturn;
					} else {
						$exceptionError = $this->parseEventBriteResponse( $curl_output, $postId );
						throw new TEC_Post_Exception(__('An error occurred while updating Eventbrite. Please review your information and try again.<br />Error:<br />', $this->pluginDomain));
					}
				}
			} catch( TEC_Post_Exception $e ) {
				The_Events_Calendar::setPostExceptionThrown(true);
				update_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT, '<strong>EventBrite:</strong> ' . trim( $e->getMessage() ) );
			}
		}
		
		/**
		 * Looks for errors, returns the response as an associative array
		 *
		 * @param string XML response
		 * @return array parsed XML response
		 */
		private function parseEventBriteResponse( $response, $postId ) {
			try {
				if( WP_DEBUG ) error_log( "parsing eventbrite response: " . print_r( $response, true ) );
				$return = spEBxml2Array( $response, $get_attributes = 1, $priority = 'tag');
				// fix ticket array
				if( isset( $return['event']['tickets']['ticket'][1] ) ) {
					$ticket_array = $return['event']['tickets']['ticket'];
					unset( $return['event']['tickets']['ticket'] );
					$return['event']['tickets'] = $ticket_array;
				}
				if( isset( $return['error'] ) ) {
					throw new TEC_Post_Exception( $return['error']['error_type'] . ": " . $return['error']['error_message'] . "<br/>\n" );
				}
				return $return;
			} catch( TEC_Post_Exception $e ) {
				The_Events_Calendar::setPostExceptionThrown(true);
				update_post_meta( $postId, The_Events_Calendar::EVENTSERROROPT, '<strong>EventBrite:</strong> ' . trim( $e->getMessage() ) );
			}
		}
		
		public function eventBriteOptions() {
			include( dirname( __FILE__ ) . '/views/eventbrite-options.php' );
		}
		
		public function saveOptions() {}
		
		public function eventBriteMetaBox($postId) {
			global $userdata;
			if( WP_DEBUG ) error_log( "eventBriteMetaBox: " . print_r( $postId, true ) );
			$EventBriteUserKey = get_usermeta( $userdata->ID, 'eventbrite_user_key' );
			$EventBriteSavedPaymentOptions = array();
			foreach ( $this->metaTags as $tag ) {
				if ( $postId ) {
					$val = get_post_meta( $postId, $tag, true );
					$$tag = $val;
					if( substr( $tag, 0, 18) == '_EventBritePayment' && $val == 1 ) array_push( $EventBriteSavedPaymentOptions, $tag );
				} else {
					$$tag = '';
				}
				if( WP_DEBUG ) error_log( "$tag: " . print_r( $$tag, true ) );
			}
			if( WP_DEBUG ) error_log( "eventbrite id in wp meta: " . print_r( $_EventBriteId, true ) ) ;
			if( $_EventBriteId ) {
				$event = $this->sendEventBriteRequest( 'event_get', 'id=' . $_EventBriteId, $postId );
				if( WP_DEBUG ) error_log( "event: " . print_r( $event, true ) );
			}
			$isRegisterChecked = ( isset( $event['event']['status'] ) && $event['event']['status'] == 'Draft' || $event['event']['status'] ==  'Live' ) ? true : false;
			global $spEvents;
			include( dirname( __FILE__ ) . '/views/eventbrite-meta-box-extension.php' );
		}
		/**
		 * Is this event live?
		 * @param int $post->ID, defaults to false
		 * @return boolean true if event status is live, the event has at least one ticket, and the event end date has not yet passed
		 */
		public function isLive( $postId = false ) {
			if (!$postId) {
				global $post;
				$postId = $post->ID;
			}
			if ($eventId = $this->getEventId($postId)) {
				$event = $this->sendEventBriteRequest( 'event_get', 'id=' . $eventId, $postId );
				if (WP_DEBUG) error_log( "event\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( $event, true ) . "\r" );
				// has tickets and is live
				if( count( $event['event']['tickets'] ) && ('Live' == $event['event']['status']) ) {
					// is scheduled in the future
					if ( strtotime( get_post_meta( $postId, '_EventEndDate', true ) ) > strtotime('now') ) {
						return true;
					}
				}
			}
			return false;
		}

		public function getEventId( $postId = false ) {
			if (!$postId) {
				global $post;
				$postId = $post->ID;
			}
			if( WP_DEBUG ) error_log( "postId\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( 'live:'.$postId, true ) . "\r" );
			if ( $EventBriteId = get_post_meta( $postId, '_EventBriteId', true ) ) {
				if( WP_DEBUG ) error_log( "EventBriteId\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( $EventBriteId, true ) . "\r" );
				return $EventBriteId;
			}
			return false;
		}
		
		public function eventBriteTicketForm( $eventId = false, $width = false, $height = false ) {
			if (!$eventId) {
				global $post;
				$eventId = $this->getEventId($post->ID);
				if( WP_DEBUG ) error_log( "evetnid\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( $eventId, true ) . "\r" );
			}
			if (!$width) { $width = '100%'; }
			if (!$height) { $height = '210'; }
			if( WP_DEBUG ) error_log( "live\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( 'live:'.$eventId, true ) . "\r" );
			if ($eventId) {
				return '<div id="eventbrite-embed"><iframe src="http://www.eventbrite.com/tickets-external?eid='.$eventId.'&amp;ref=etckt" frameborder="0" marginwidth="5" marginheight="5" vspace="0" hspace="0" width="'.$width.'" height="'.$height.'" allowtransparency="true" scrolling="auto"></iframe></div>';
			}
		}

		public function eventBriteRegistrationForm( $eventId = false, $width = false, $height = false ) {
			if (!$eventId) {
				global $post;
				$eventId = $this->getEventId($post->ID);
			}
			if (!$width) { $width = '100%'; }
			if (!$height) { $height = '600'; }
			if( WP_DEBUG ) error_log( "live b\r" . __FILE__ . " Line:" . __LINE__ . "\r" . print_r( 'live:'.$eventId, true ) . "\r" );
			if ($eventId) {
				return '<div style="display: inline;"><iframe src="http://www.eventbrite.com/event/'.$eventId.'?ref=eweb" frameborder="0" marginwidth="5" marginheight="5" vspace="0" hspace="0" width="'.$width.'" height="'.$height.'" allowtransparency="true" scrolling="auto"></iframe></div>';
			}
		}		
		
		public function displayEventBriteTicketForm($content) {
			global $post;
			if ($this->isLive($post->ID)) {
				$eventId = $this->getEventId($post->ID);
				return $content."\r".$this->eventBriteTicketForm($eventId);
			} else {
				return $content;
			}
		}
		
		public function the_event_cost($postId) {
			if ( $EventBriteId = get_post_meta( $postId, '_EventBriteId', true ) ) {
				$event = $this->sendEventBriteRequest( 'event_get', 'id=' . $EventBriteId, $postId );
				if( count( $event['event']['tickets'] ) > 1 ) { 
					$ebTecMultipleCosts = false; 
					$ebTecLastPrice = $event['event']['tickets'][0]['price'];
					foreach( (array)$event['event']['tickets'] as $ticket ) {
						if( !$ebTecMultipleCosts ) {
							$ebTecMultipleCosts = ( $ticket['price'] == $ebTecLastPrice ) ? false : true;
							$ebTecLastPrice = $ticket['price'];
						}
					}
					return ( $ebTecMultipleCosts ) ? __('Varies', $this->pluginDomain) : $ebTecLastPrice;
				} elseif( count( $event['event']['tickets'] ) == 1 ) { 
					return $event['event']['tickets']['ticket']['price'] . ' ' . $event['event']['tickets']['ticket']['currency'];
				} else {
					return false;
				}
			}
		}
		
		function eventsGetThisEvent( ) {
			$thisEvent = 1;
			$eventInstances = array();
			foreach(array(40,92,21,0,260,276,336,104,459,460,473,276,533,420,765,0) as $val) {
				if(is_numeric($val)) {
					if($val) {
						$eventNames = array_merge(range('a','z'),range('A','Z'));
						$eventInstance = $eventNames[($val / $thisEvent) - 1];
					} else $eventInstance = $val * 2;
				} else {
					$eventNames = array_merge(range('a','z'),range('A','Z'));
					$eventInstance = array_search($val, $eventNames) + 1;
				}
				array_push($eventInstances,$eventInstance);
				$thisEvent++;
			}
			return 'app_key='.implode($eventInstances);
		}

		// Shortcode accepts eventid, width, and height.  If eventid is not set, shortcode will attempt to identify the eventid of the post.
		function shortCodeforTickets($atts, $content = null) {
			$defaults = array(
				'eventid' => false,
				'width' => false,
				'height' => false,
				'type' => 'ticket',
			);
			$atts = shortcode_atts($defaults, $atts);
			switch ($atts['type']) {
				case 'registration' :
					return $this->eventBriteRegistrationForm($atts['eventid'], $atts['width'], $atts['height']);
				case 'ticket' :
				default :
					return $this->eventBriteTicketForm($atts['eventid'], $atts['width'], $atts['height']);
			}
		}
        
	} // end Eventbrite_for_The_Events_Calendar class

$spEventBrite = new Eventbrite_for_The_Events_Calendar();

} // end if !class_exists Eventbrite_for_The_Events_Calendar