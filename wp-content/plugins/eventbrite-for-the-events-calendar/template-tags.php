<?php
if( !function_exists('the_event_ticket_count') ) {
	/**
	 * @param int post id (optional if used in the loop)
	 * @return int the number of tickets for an event
	 */
	function the_event_ticket_count( $postId = null ) {
		global $spEventBrite, $post;
		if ( $postId === null || !is_numeric( $postId ) ) {
			$postId = $post->ID;
		}
		$retval = 0;
		if ( $EventBriteId = get_post_meta( $postId, '_EventBriteId', true ) ) {
			$event = $spEventBrite->sendEventBriteRequest( 'event_get', 'id=' . $EventBriteId, $postId );
			$retval = count( $event['event']['tickets'] );
		}
		return $retval;
	}

	/**
	 * Returns the event id for the post
	 *
	 * @param int post id (optional if used in the loop)
	 * @return int event id, false if no event is associated with post
	 */
	function get_event_id( $postId = null) {
		global $spEventBrite;
		return $spEventBrite->getEventId($postId);
	}

	/**
	 * Determine if an event is live
	 *
	 * @param int post id (optional if used in the loop)
	 * @return boolean
	 */
	function is_live_event( $postId = null) {
		global $spEventBrite;
		return $spEventBrite->isLive($postId);
	}

	/**
	 * Outputs the eventbrite ticket form.
	 *
	 * @param int event id (optional if used in the loop)
	 * @return void
	 */
	function the_eventbrite_ticket_form( $eventId = false, $width = false, $height = false ) {
		global $spEventBrite;
		echo $spEventBrite->eventBriteTicketForm($eventId, $width, $height);
	}

	/**
	 * Outputs the eventbrite registration form.
	 *
	 * @param int event id (optional if used in the loop)
	 * @return void
	 */
	function the_eventbrite_registration_form( $eventId = false, $width = false, $height = false ) {
		global $spEventBrite;
		echo $spEventBrite->eventBriteRegistrationForm($eventId, $width, $height);
	}

	/**
	 * Outputs the eventbrite post template.  The post in question must be registered with eventbrite
	 * and must have at least one ticket type associated with the event.
	 *
	 * @param int post id (optional if used in the loop)
	 * @uses views/eventbrite-post-template.php for the HTML display
	 * @return void
	 */
	function eventbrite_event_get( $postId = null ) {
		global $spEventBrite;
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if ( $EventBriteId = get_post_meta( $postId, '_EventBriteId', true ) ) {
			$event = $spEventBrite->sendEventBriteRequest( 'event_get', 'id=' . $EventBriteId, $postId );
			if( count( $event['event']['tickets'] ) ) { 
				include( dirname( __FILE__ ) . '/views/eventbrite-post-template.php' );
			} else {
			}
		}
	}

	/**
	 * Returns the eventbrite attendee data for display
	 *
	 * @return string with html event attendees
	 */
	function eventbrite_event_list_attendees($id, $user, $password) {
		global $spEventBrite;
		$base_url = "https://www.eventbrite.com/xml/event_list_attendees?" . $this->eventsGetThisEvent() . "&user=".$user."&password=".$password."&id=".$id;
		// Load the XML with a cURL request
		$xml = load_xml($base_url);
		if ($xml->error_message != '') { echo $xml->error_message;}
		else
		{
		$cnt = count($xml->attendee);
	
		echo '<p>For a detailed list of attendees, visit eventbrite.</p><table class="EB-table" border="0"><tr><td width="120px">Attendee</td><td width="95px">Paid</td><td width="40px">Qty</td><td width="80px">Purchase Date</td></tr>';
	
		for($i=0; $i<$cnt; $i++) {
			$firstname 	= $xml->attendee[$i]->first_name;
			$lastname 	= $xml->attendee[$i]->last_name;
			$email 	= $xml->attendee[$i]->email;
			$quantity 	= $xml->attendee[$i]->quantity;
			$created 	= date_create($xml->attendee[$i]->created);		
			$currency 	= $xml->attendee[$i]->currency;
			$amount_paid 	= $xml->attendee[$i]->amount_paid;				

			echo '<tr><td><a href="mailto:'.$email.'">'.$firstname.'&nbsp;'.$lastname.'</a></td><td>'.$currency.' '.$amount_paid.'</td><td>'.$quantity.'</td><td>'.date_format($created,__("m.d.Y", $spEventBrite->pluginDomain)).'</td></tr>';
		}
		echo "</table>";
		//print_r($xml);
		}
		return;
	}

	/**
	 * spEBxml2Array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/spEBxml2Array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
	 * Examples: $array =  spEBxml2Array(file_get_contents('feed.xml'));
	 *              $array =  spEBxml2Array(file_get_contents('feed.xml', 1, 'attribute'));
	 */
	function spEBxml2Array($contents, $get_attributes=1, $priority = 'tag') {
	    if(!$contents) return array();

	    if(!function_exists('xml_parser_create')) {
	        //print "'xml_parser_create()' function not found!";
	        return array();
	    }

	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);

	    if(!$xml_values) return;//Hmm...

	    //Initializations
	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();

	    $current = &$xml_array; //Refference

	    //Go through the tags.
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	    foreach($xml_values as $data) {
	        unset($attributes,$value);//Remove existing values, or there will be trouble

	        //This command will extract these variables into the foreach scope
	        // tag(string), type(string), level(int), attributes(array).
	        extract($data);//We could use the array by itself, but this cooler.

	        $result = array();
	        $attributes_data = array();

	        if(isset($value)) {
	            if($priority == 'tag') $result = $value;
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	        }

	        //Set the attributes too.
	        if(isset($attributes) and $get_attributes) {
	            foreach($attributes as $attr => $val) {
	                if($priority == 'tag') $attributes_data[$attr] = $val;
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	            }
	        }

	        //See tag status and do the needed.
	        if($type == "open") {//The starting of the tag '<tag>'
	            $parent[$level-1] = &$current;
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	                $current[$tag] = $result;
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	                $repeated_tag_index[$tag.'_'.$level] = 1;

	                $current = &$current[$tag];

	            } else { //There was another element with the same tag name

	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    $repeated_tag_index[$tag.'_'.$level]++;
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                    $repeated_tag_index[$tag.'_'.$level] = 2;

	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                        unset($current[$tag.'_attr']);
	                    }

	                }
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	                $current = &$current[$tag][$last_item_index];
	            }

	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	            //See if the key is already taken.
	            if(!isset($current[$tag])) { //New Key
	                $current[$tag] = $result;
	                $repeated_tag_index[$tag.'_'.$level] = 1;
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

	            } else { //If taken, put all things inside a list(array)
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

	                    // ...push the new element into that array.
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

	                    if($priority == 'tag' and $get_attributes and $attributes_data) {
	                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++;

	                } else { //If it is not an array...
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                    $repeated_tag_index[$tag.'_'.$level] = 1;
	                    if($priority == 'tag' and $get_attributes) {
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

	                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                            unset($current[$tag.'_attr']);
	                        }

	                        if($attributes_data) {
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                        }
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	                }
	            }

	        } elseif($type == 'close') { //End of tag '</tag>'
	            $current = &$parent[$level-1];
	        }
	    }

	    return($xml_array);
	}

} // end if !function_exists('the_event_ticket_count')