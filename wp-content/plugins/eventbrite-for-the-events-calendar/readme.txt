=== Eventbrite for The Events Calendar ===

Contributors: Kelsey Damas, Matt Wiebe, Justin Endler, Reid Peifer produced by Shane & Peter, Inc.
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10750983
Tags: widget, events, simple, tooltips, grid, month, list, calendar, event, venue, eventbrite, registration, tickets, ticketing, eventbright, api, dates, date, plugin, posts, sidebar, template, theme, time, google maps, google, maps, conference, workshop, concert, meeting, seminar, summit, forum, shortcode
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.6

== Description ==

Looking to track attendees, sell tickets and more? Eventbrite is a free service that provides the full power of a conference ticketing system. This plugin upgrades The Events Calendar with all the basic Eventbrite controls without ever leaving the wordpress post editor. Don't have an Eventbrite account? No problem, use the following link to set one up: http://www.eventbrite.com/r/simpleevents.

Eventbrite for The Events Calendar now has one piece of standalone functionality. This plugin includes a series of shortcodes that will allow the embedding of Eventbrite Widgets into Wordpress post/page/sidebar content. Want to sell tickets or show a registration form for an Eventbrite event that you didn't create yourself from Wordpress? Now you can.

= Eventbrite for The Events Calendar =

* Sell tickets directly from your post
* Extensive template tags for customization
* MU Compatible
* Many of the amazing features of Eventbrite - directly from Wordpress
* Use shortcode to place any event ticketing on any post

= Upcoming Features =

* Sync with Eventbrite (one by one OR full account sync)
* Improved error checking
* Payment methods (for users selling tickets who want to get their profit)
* Rework event cost

Please visit the forum for feature suggestions: http://wordpress.org/tags/eventbrite-for-the-events-calendar/

This plugin is actively supported and we will do our best to help you. In return we simply as 2 things:

1. Donate - if this is generating enough revenue to support our time it makes all the difference in the world: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10750983
1. If you make a new account with Eventbrite, please use our referral code. It helps, believe me: http://www.eventbrite.com/r/simpleevents

== Installation ==

= Install =

1. Unzip the `eventbrite-for-the-events-calendar.zip` file. 
1. Upload the the `eventbrite-for-the-events-calendar` folder (not just the files in it!) to your `wp-contents/plugins` folder. If you're using FTP, use 'binary' mode.
1. Update your permalinks to ensure that the event specific rewrite rules take effect.

= Activate =

No setup required. Just plug and play!

= Requirements =

* The Events Calendar plugin: http://wordpress.org/extend/plugins/the-events-calendar/
* PHP 5.1 ONLY!!!

== Documentation ==

= Shortcode =

*[eventbrite]*

Outputs eventbrite ticket form or registration form.

Attributes:

* eventid - Id of the eventbrite event.  If none is provided, system will attempt to extract the eventId from the post.
* type (ticket or register) - Determines the type of form.  Ticket form is returned by default.
* width - The width of the frameset.  Defaults to "100%".
* height - Height of the frameset.  Defaults to 210 for the ticket form and 600 for the reg form.

= Template Tags =

/**
 * @param int post id (optional if used in the loop)
 * @return int the number of tickets for an event
 */
the_event_ticket_count( $postId = null )

/**
 * Returns the event id for the post
 *
 * @param int post id (optional if used in the loop)
 * @return int event id, false if no event is associated with post
 */
get_event_id( $postId = null)

/**
 * Determine if an event is live
 *
 * @param int post id (optional if used in the loop)
 * @return boolean
 */
is_live_event( $postId = null)

/**
 * Outputs the eventbrite ticket form.
 *
 * @param int event id (optional if used in the loop)
 * @return void
 */
the_eventbrite_ticket_form( $eventId = false, $width = false, $height = false )

/**
 * Outputs the eventbrite registration form.
 *
 * @param int event id (optional if used in the loop)
 * @return void
 */
the_eventbrite_registration_form( $eventId = false, $width = false, $height = false )

/**
 * Outputs the eventbrite post template.  The post in question must be registered with eventbrite
 * and must have at least one ticket type associated with the event.
 *
 * @param int post id (optional if used in the loop)
 * @uses views/eventbrite-post-template.php for the HTML display
 * @return void
 */
eventbrite_event_get( $postId = null )

/**
 * Returns the eventbrite attendee data for display
 *
 * @return string with html event attendees
 */
eventbrite_event_list_attendees($id, $user, $password)

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
spEBxml2Array($contents, $get_attributes=1, $priority = 'tag')

== Screenshots ==

1. Eventbrite form in the Event Post
1. Post editor
1. User-based ID field

== FAQ ==

= Where do I go to file a bug or ask a question? =

Please visit the forum for questions or comments: http://wordpress.org/tags/eventbrite-for-the-events-calendar/

== Changelog ==

= 1.6 =

* PHP versions older than 5.1 will fail gracefully
* Import event info from Eventbrite by event id number.
* Payment options now specified from post edit
* Improved error handling and reporting
* updated to new Eventbrite logo

= 1.5.4 =

* Fix form checkbox issue

= 1.5.3 =

* Small ui changes to admin
* Fix curl issue for non-standard systems
* Bring plugin inline with proper wordpress debugging practices

= 1.5.2 =

Eventbrite Widget Shortcode Support:
* Added shortcode for ticket form: [eventbrite eventid=1234 width=100% height=300]
* Added shortcode and template tag for registration form: [eventbrite type=registration eventid=1234 width=100% height=600] and the_eventbrite_registration_form($eventId)
* Added template tags and documentation for template tags
* Shortcode above functions without The Events Calendar Plugin allowing users to embed ANY Eventbrite event widget into their posts.

Features
* Cleaned up admin layout and added more inline directions and explanations
* Added Eventbrite branding to the admin panel
* Moved donate link to the very bottom

= 1.5.1 =

* Make displayEventBriteSignupForm() public so WP doesn't throw a php warning when calling it
* Fix exception handling bugs

= 1.5 =

* Extract Eventbrite from the Events calendar into a stand alone plugin
* Fixed a whole pile of small bugs.
* Updated to use Eventbrite's new secure user account data option
* Add donate links
* Add settings panel
** Default View (calendar or list) for categories
** Default country for events
** Donate toggle on/off
* Upgrade for WP 2.9

= 1.5.a =

* Plug and Play install including default templates (list view, grid view and post)
* Theme overwrite of default templates (see instructions)
* 12 hour / 24 hour time display options
* Work with all permalink styles
* Hide data from custom fields
* Hide Eventbrite sales box in post if there is are tickets
* Multiple javascript bug fixes
* Pull price for 1st ticket from general event price
* Add some basic error messages from Eventbrite (much more to come)
* Remove dependencies on other S&P plugins

= 1.4.1 =

* Featured event checkbox and template tag is_featured_event()

= 1.4 =

* Grid View
* Additional Internationalization support added

= 1.3 =

* Built events list widget

= 1.2 =

* Added Internationalization (translation) support
* Added international addresses
* Extracted from S&P core plugin to stand alone.