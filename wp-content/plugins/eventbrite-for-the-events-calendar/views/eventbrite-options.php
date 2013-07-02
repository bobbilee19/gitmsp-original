<?php
global $current_user;
get_currentuserinfo();
$EventBriteUserKey = get_usermeta( $current_user->ID, 'eventbrite_user_key' );
?>
<?php if( !$EventBriteUserKey ) : ?>
<tr>
	<th scope="row"><?php _e('Eventbrite',$this->pluginDomain);?></th>
	<td>
		<?php _e('In order to enable Eventbrite for the The Events Calendar, please enter your Eventbrite User Key on <a href="' . get_bloginfo('url') . '/wp-admin/profile.php" target="_blank">your user profile page.</a>',$this->pluginDomain);?>
    </td>
</tr>
<?php endif; ?>