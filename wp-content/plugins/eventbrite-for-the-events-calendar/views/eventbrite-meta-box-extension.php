<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function(){
	// hide/show EventBrite fields
	jQuery('#EventBriteDetailDiv').hide();
	if ( jQuery("input[name='EventRegister']:checked").val() == 'yes' ) {
		jQuery("#EventBriteDetailDiv").show();
	}
	jQuery("#EventBriteToggleOn").click(function(){
		jQuery("#EventBriteDetailDiv").slideDown(200);
	});
	jQuery("#EventBriteToggleOff").click(function(){
		jQuery("#EventBriteDetailDiv").slideUp(200);
	});
	
	// hide/show additional payment option fields
	
	var ebTecAcceptPaymentInputs = jQuery('#eb-tec-payment-options-checkboxes input');
	function ebTecShowHideAdditionalPaymentOptions(event) {
		if(event) {
			ebTecAcceptPaymentInputs.change(function() {
				var divIndex = ebTecAcceptPaymentInputs.index(this) + 1;
				if(this.checked) jQuery('#eb-tec-payment-options div:eq('+divIndex+')').slideDown(200);
				else jQuery('#eb-tec-payment-options div:eq('+divIndex+')').slideUp(200);
			});
		} else {
			jQuery.each('#eb-tec-payment-options-checkboxes ~ #eb-tec-payment-options div', function() {
				var thisInput = jQuery(this).find('input');
				if(thisInput.val() != null) thisInput.closest('div').slideDown(200);
			});
		}
	}
	ebTecShowHideAdditionalPaymentOptions();
	ebTecAcceptPaymentInputs.bind('click', ebTecShowHideAdditionalPaymentOptions);

	// Define error checking routine on submit
	jQuery("form[name='post']").submit(function() {
		var ticket_name = jQuery("input[name='EventBriteTicketName']").val();
		if( jQuery("#EventBriteToggleOn").attr('checked') == true && typeof( ticket_name ) != 'undefined' ) {
			var ticket_price = jQuery("input[name='EventCost']").val();
			var ticket_quantity = jQuery("input[name='EventBriteTicketQuantity']").val();
			
			if( typeof( ticket_name ) == 'undefined' || !ticket_name.length ) {
				alert('<?php _e("Please provide a ticket name for the Eventbrite ticket.",$this->pluginDomain); ?>');
				jQuery("input[name='EventBriteTicketName']").focus();
				return false;
			}
			if( !ticket_price.length) {
				alert('<?php _e("You must set a price for the ticket ",$this->pluginDomain); ?>' + ticket_name);
				jQuery("input[name='EventCost']").focus();
				return false;
			}
			if( (parseInt(ticket_quantity) == 0 || isNaN(parseInt(ticket_quantity) ) ) ) {
				alert('<?php _e("Ticket quantity is not a number",$this->pluginDomain); ?>');
				jQuery("input[name='EventBriteTicketQuantity']").focus();
				return false;
			}
			if( jQuery('input[name="EventBritePayment-accept_paypal"]').is(':checked') ) {
				var emailField = jQuery('input[name="EventBritePayment-paypal_email"]');
				if( !emailField.val().length ) {
					alert('<?php _e("A Paypal email address must be provided.",$this->pluginDomain); ?>');
					emailField.focus();
					return false;
				}
			}
			if( jQuery('input[name="EventBritePayment-accept_google"]').is(':checked') ) {
				var merchantIdField = jQuery('input[name="EventBritePayment-google_merchant_id"]');
				if( !merchantIdField.val().length ) {
					alert('<?php _e("A Google Merchant Id must be provided.",$this->pluginDomain); ?>');
					merchantIdField.focus();
					return false;
				}
				var merchantKeyField = jQuery('input[name="EventBritePayment-google_merchant_key"]');
				if( !merchantKeyField.val().length ) {
					alert('<?php _e("A Google Merchant Key must be provided.",$this->pluginDomain); ?>');
					merchantKeyField.focus();
					return false;
				}
			}
			return true;
		}
	});
 
}); // end document ready
</script>
    <?php if( $EventBriteUserKey ) : ?>
    <div id="eventBriteTicketing">
    <h2><?php _e('Eventbrite Ticketing:',$this->pluginDomain); ?></h2>
 
        <div>
        <table class="eventForm">
           <tr>
     <td>
<p>
<?php _e('Register this event with Eventbrite.com?',$this->pluginDomain);?>&nbsp;
<input id='EventBriteToggleOn' tabindex="<?php $spEvents->tabIndex(); ?>" type='radio' name='EventRegister' value='yes' <?php checked($isRegisterChecked, true); ?> />&nbsp;<b><?php _e('Yes', $this->pluginDomain); ?></b>
<input id='EventBriteToggleOff' tabindex="<?php $spEvents->tabIndex(); ?>" type='radio' name='EventRegister' value='no'  <?php checked($isRegisterChecked, false); ?>/>&nbsp;<b><?php _e('No', $this->pluginDomain); ?></b>
</p>
            <?php // if an EventBrite event has been created by a different user, give a warning
$thisAuthorId = get_post( $postId )->post_author;
if ( get_post_meta( $postId, '_EventBriteId', true ) && $EventBriteUserKey != get_usermeta( $thisAuthorId, 'eventbrite_user_key' ) ) {
_e('<em class="eventsWarning">This event was created by another user. Please consider contacting <a href="mailto:' . get_userdata($thisAuthorId)->user_email . '">' . get_userdata($thisAuthorId)->display_name . '</a> about editing these EventBrite details.</em>',$this->pluginDomain);
} ?>
</td>
</tr>
        </table>
     </div> <!-- end EventbriteTicketing -->
     
     <div id='EventBriteDetailDiv'>
       <table class="eventForm">
     
        <?php if ( !$_EventBriteId ) :?>
            <tr>
            <td colspan="2" class="snp_sectionheader">
           <h4><?php _e('Set up your first ticket',$this->pluginDomain);?>
           <small style="text-transform:none; display:block; margin-top:8px; font-weight:normal;"><?php _e('To create multiple tickets per event, submit this form, then follow the link to Eventbrite.', '$this->pluginDomain'); ?></small></h4>
</td>
</tr>
<tr>
<td width="125">
                <?php _e('Name:',$this->pluginDomain); ?>
              </td>
              <td>
                    <input tabindex="<?php $spEvents->tabIndex(); ?>" type="text" name="EventBriteTicketName" size="14" value="<?php echo $_EventBriteTicketName; ?>" />
               </td>
<tr>
<td></td>
<td class="snp_message"><?php _e('<small >Examples: Member, Non-member, Student, Early Bird</small>',$this->pluginDomain); ?></td>
</tr>
	<tr>
		<td>
			<?php _e('Description:',$this->pluginDomain); ?>
		</td>
		<td>
			<textarea class="description_input" tabindex="<?php $spEvents->tabIndex(); ?>" name="EventBriteTicketDescription" rows="2" cols="75"><?php echo $_EventBriteTicketDescription; ?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			<?php _e('Type:',$this->pluginDomain); ?>
		</td>
		<td>
			<span class="tec-radio-option" ><input tabindex="<?php $spEvents->tabIndex(); ?>" type="radio" name="EventBriteIsDonation" value="0" <?php if (!isset( $_EventBriteIsDonation ) || $_EventBriteIsDonation == "0"){echo "checked";}?> /><?php _e(' Set Price', '$this->pluginDomain'); ?></span>
			<span class="tec-radio-option" ><input tabindex="<?php $spEvents->tabIndex(); ?>" type="radio" name="EventBriteIsDonation" value="1" <?php if ($_EventBriteIsDonation == "1"){echo "checked";}?> /><?php _e(' Donation Based', '$this->pluginDomain'); ?></span>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="snp_message"><small><?php _e('The price for this event\'s first ticket will be taken from the <em>Cost</em> field above.', $this->pluginDomain); ?></small></td>
	</tr>
	<tr>
		<td>
			<?php _e('Quantity:',$this->pluginDomain); ?>
		</td>
		<td>
			<input tabindex="<?php $spEvents->tabIndex(); ?>" type='text' name='EventBriteTicketQuantity' size='14' value='<?php echo $_EventBriteTicketQuantity; ?>' />
		</td>
	<tr>
		<td>
			<?php _e('Include Fee in Price:',$this->pluginDomain); ?>
		</td>
		<td>
			<span class="tec-radio-option" ><input tabindex="<?php $spEvents->tabIndex(); ?>" type="radio" class="radio" name="EventBriteIncludeFee" value="0" <?php if (!isset( $_EventBriteIncludeFee ) || $_EventBriteIncludeFee == "0"){echo "checked";}?> /><?php _e(' Add Service Fee on top of price', '$this->pluginDomain'); ?></span>
			<span class="tec-radio-option"><input tabindex="<?php $spEvents->tabIndex(); ?>" type="radio" class="radio" name="EventBriteIncludeFee" value="1" <?php if ($_EventBriteIncludeFee == "1"){echo "checked";}?> /><?php _e(' Include Service fee in price', '$this->pluginDomain'); ?></span>
		</td>
	</tr>
	<tr id="eb-tec-payment-options">
		<td>
			<?php _e('Payment Options:',$this->pluginDomain); ?>
		</td>
		<td>
			<div id="eb-tec-payment-options-checkboxes">
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="checkbox" class="checkbox" name="EventBritePayment-accept_paypal" value="1" /><label for="EventBritePayment-accept_paypal"><?php _e('Paypal', $this->pluginDomain); ?></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="checkbox" class="checkbox" name="EventBritePayment-accept_google" value="1" /><label for="EventBritePayment-accept_google"><?php _e('Google Checkout', $this->pluginDomain); ?></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="checkbox" class="checkbox" name="EventBritePayment-accept_check" value="1" /><label for="EventBritePayment-accept_check"><?php _e('Check', $this->pluginDomain); ?></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="checkbox" class="checkbox" name="EventBritePayment-accept_cash" value="1" /><label for="EventBritePayment-accept_cash"><?php _e('Cash', $this->pluginDomain); ?></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="checkbox" class="checkbox" name="EventBritePayment-accept_invoice" value="1" /><label for="EventBritePayment-accept_invoice"><?php _e('Send an Invoice', $this->pluginDomain); ?></label>
			</div>
			<hr />
			<div>
				<label for="EventBritePayment-paypal_email"><?php _e('Paypal Account Email Address', $this->pluginDomain); ?><span>&#10035;</span></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="text" name="EventBritePayment-paypal_email" size="20" value="" />
			</div>
			<div>
				<label for="EventBritePayment-google_merchant_id"><?php _e('Google Merchant Id', $this->pluginDomain); ?><span>&#10035;</span></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="text" name="EventBritePayment-google_merchant_id" size="14" value="" />

				<label for="EventBritePayment-google_merchant_key"><?php _e('Google Merchant Key', $this->pluginDomain); ?><span>&#10035;</span></label>
				<input tabindex="<?php $spEvents->tabIndex(); ?>" type="text" name="EventBritePayment-google_merchant_key" size="14" value="" />
			</div>
			<div class="eb-tec-payment-instructions">
				<label for="EventBritePayment-instructions_check"><?php _e('Instructions for Payment by Check', $this->pluginDomain); ?></label>
				<textarea tabindex="<?php $spEvents->tabIndex(); ?>" name="EventBritePayment-instructions_check" rows="2" cols="75"></textarea>
			</div>
			<div class="eb-tec-payment-instructions">
				<label for="EventBritePayment-instructions_cash"><?php _e('Instructions for Payment by Cash', $this->pluginDomain); ?></label>
				<textarea tabindex="<?php $spEvents->tabIndex(); ?>" name="EventBritePayment-instructions_cash" rows="2" cols="75"></textarea>
			</div>
			<div class="eb-tec-payment-instructions">
				<label for="EventBritePayment-instructions_invoice"><?php _e('Instructions for Payment by Invoice', $this->pluginDomain); ?></label>
				<textarea tabindex="<?php $spEvents->tabIndex(); ?>" name="EventBritePayment-instructions_invoice" rows="2" cols="75"></textarea>
			</div>
		</td>
	</tr>
            <tr>
             <td colspan="2" class="snp_sectionheader">
  <h4><?php _e('Save this post to create the Event with Eventbrite.com',$this->pluginDomain);?></h4>
               <div><p><?php _e('When you save this post, an event will be created for you within EventBrite. You will be able to further configure your event here after saving. For more advanced controls visit the EventBrite administration here: <a href="http://eventbrite.com" target="_blank">http://eventbrite.com</a>',$this->pluginDomain);?></p></div>
                </td>
            </tr>
    
        <?php else : // have eventbrite id ?>
			<?php include( 'eventbrite-events-table.php' ); ?>
        <?php endif; // !$EventBriteId ?>
         </table>
        </div><!--//EventBriteDetailDiv -->
        
    <?php else : // no login or password ?>
     <div class="tec-event-configure-warning">
        <?php _e('You must configure your Eventbrite API User Key in your', $this->pluginDomain ); ?>
        <a href='<?php echo get_bloginfo('url') . '/wp-admin/profile.php'; ?>'><?php _e('profile page', $this->pluginDomain); ?></a>
        <?php _e('before you can use the Eventbrite features of this plugin.', $this->pluginDomain ); ?>
</div>
    
    <?php endif; // no login or password ?>
</div><!--//eventBriteTicketing-->