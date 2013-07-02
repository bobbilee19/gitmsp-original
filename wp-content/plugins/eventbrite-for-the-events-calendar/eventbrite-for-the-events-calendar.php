<?php
/*
 Plugin Name:  Eventbrite for The Events Calendar
 Plugin URI: http://wordpress.org/extend/plugins/eventbrite-for-the-events-calendar/
 Description: Eventbrite for The Events Calendar adds Eventbrite integration to the Shane & Peter plugin <a href='http://wordpress.org/extend/plugins/the-events-calendar/'>The Events Calendar</a>. This plugin depends on <a href='http://wordpress.org/extend/plugins/the-events-calendar/'>The Events Calendar</a>. Both plugins need to be activated in order for Eventbrite functionality to work although you may use this plugin to display Eventbrite widgets via shortcode in your content without The Events Calendar. When updating this plugin, The Events Calendar must be updated too. Requires PHP 5.1 or above.
 Version: 1.6
 Author URI: http://www.shaneandpeter.com/
 Text Domain: events
 */
register_activation_hook(__FILE__, 'eventbrite_for_the_events_calendar_activate');

add_action( 'admin_head', 'eventbrite_for_the_events_calendar_version_check' );

function eventbrite_for_the_events_calendar_version_check() {
	if ( version_compare( PHP_VERSION, "5.1", "<") ) {
		echo "<div class='error'>Eventbrite For The Events Calendar requires PHP 5.1 or greater.  Please de-activate Eventbrite For The Events Calendar.</div>";
    }
}
function eventbrite_for_the_events_calendar_activate() {
    if ( version_compare( PHP_VERSION, "5.1", "<") ) {
        trigger_error('', E_USER_ERROR);
    } else {
	    require_once(dirname(__FILE__) . "/eventbrite-for-the-events-calendar.class.php");
	    require_once(dirname(__FILE__) . "/template-tags.php");
    }
}

if (version_compare(phpversion(), "5.1", ">=")) {
    require_once(dirname(__FILE__) . "/eventbrite-for-the-events-calendar.class.php");
    require_once(dirname(__FILE__) . "/template-tags.php");
}