<h3><?php _e('Eventbrite User Account') ?></h3>

<div>
	<span class="description"><?php _e('Your API Key can be found in your <a href="http://www.eventbrite.com/userkeyapi" target="_BLANK">account settings</a> in EventBrite. Don\'t have an Eventbrite account yet? It takes less than 30 seconds to set one up.',$this->pluginDomain); ?> <a target='_BLANK' href='http://www.eventbrite.com/r/simpleevents'><?php _e('Click here to Register.',$this->pluginDomain); ?></a></span></label>
</div>

<table class="form-table">
<tr>
	<th><label for="email"><?php _e('EventBrite API User Key'); ?> 
	</th>
	<td><input type="text" name="eventbrite_user_key" id="eventbrite_user_key" value="<?php echo esc_attr(get_usermeta( $profileuser->ID, 'eventbrite_user_key' ) ); ?>" class="regular-text" /></td>
</tr>
</table>
<br />
