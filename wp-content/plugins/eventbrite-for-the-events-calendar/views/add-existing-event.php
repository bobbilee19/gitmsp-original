<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function() {
		// Register event handler for existing event toggle
		// TODO hide irrelevant form fields
		jQuery("input[name='existingEBEvent']").click(function() { 
			if ( jQuery(this).val() == 'yes' ) {
				jQuery("#eb-tec-existing-event-id-input").slideDown(200);
			} else {
				jQuery("#eb-tec-existing-event-id-input").slideUp(200);
			}
		});
		
		// has a post title been entered?, a little hacky
		var useEventTitle = false;
		
		function ebTecIsPostTitleEmpty(event) {
			if( this.textLength == 0 ) useEventTitle = true;
			else useEventTitle = false;
		}
		
		jQuery('#title').bind('focus', ebTecIsPostTitleEmpty);
		jQuery('#editorcontainer textarea').bind('focus', ebTecIsDescriptionEmpty);
		
		// has the description been entered?
		var useEventDescription = false;
		var ebTecTinyMCEActive = false;
		function ebTecIsDescriptionEmpty(event) {
			var ed;
			if( typeof(tinyMCE) != 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden() ) {
				ebTecTinyMCEActive = true;
				var content = ed.getContent();
				if(content) useEventDescription = false;
				else useEventDescription = true;
			} else {
				ebTecTinyMCEActive = false;
				if( this.textLength == 0 ) useEventDescription = true;
				else useEventDescription = false;
			}
		}
		
		function spPopulateEventDetailsForm( processedEventBriteResponse ) {		
			jQuery.each( processedEventBriteResponse, function( key, val ) {
				if(key == 'isPublishing') return true;
				if(key == 'post_title') {
					if( useEventTitle ) jQuery('#title').val(val);
					else return true;
				} else if( key == 'content' ) {
					if( useEventDescription ) {
						if( ebTecTinyMCEActive ) tinyMCE.activeEditor.setContent(val);
						else jQuery('#content').val(val);
					} else return true;
				}
				// tickets object
				if( key == 'eventbriteEventsTable' ) jQuery('#EventBriteDetailDiv').replaceWith(val);
				else {
					var input = jQuery("input[name='" + key + "']");
					if( input.length != 0 ) input.val(val);
					else {
						jQuery("select[name='" + key + "']").val(val);
					}
				}
				// commit the tags
				if( key == 'newtag[post_tag]') jQuery("input[name='" + key + "'] + input").click();
				jQuery('input[Name="SubmitExistingEventId"]').focus();
			});
			var publishSubmit = jQuery('#publish');
			publishSubmit.unbind('click');
			if(processedEventBriteResponse['isPublishing']) publishSubmit.click();
		}
		
		jQuery('input[Name="SubmitExistingEventId"], #publish').click(function(event) {
			var existingEventId = jQuery('input[Name="ExistingEventId"]').val();
			var thisId = jQuery(this).attr('id');
			if( existingEventId && !isNaN(existingEventId) ) {
				jQuery('#title').focus();
				jQuery('#editorcontainer textarea').focus();
				var lookForPostId = jQuery("input[Name='temp_ID']").val();
				if( !lookForPostId ) lookForPostId = jQuery("input[Name='post_ID']").val();
				if( lookForPostId ) {
					jQuery.post( ajaxurl, { action: 'existingEventId', existingEventId: existingEventId, postId: lookForPostId, clickedId: thisId }, spPopulateEventDetailsForm, 'json' );
				}
				// add css
				//jQuery('#EventBriteDetailDiv td').css({'font-size':'11px','padding':'6px 6px 0 0','vertical-align':'middle'});
				event.preventDefault();
			} else if( thisId != 'publish') event.preventDefault();
		});
	
}); // end document ready
</script>

<div id="eb-tec-import-event">
	<label><?php _e('Do you want to import an existing event from Eventbrite?',$this->pluginDomain); ?>&nbsp;</label>
	<input tabindex="<?php $spEvents->tabIndex(); ?>" type='radio' name='existingEBEvent' value='yes' />&nbsp;<b>Yes</b>
	<input tabindex="<?php $spEvents->tabIndex(); ?>" type='radio' name='existingEBEvent' value='no' checked="checked" />&nbsp;<b>No</b>
</div>
<div id="eb-tec-existing-event-id-input">
	<label for="ExistingEventId"><?php _e('Enter your EventBrite Event ID here:',$this->pluginDomain); ?></label>
	<input tabindex="<?php $spEvents->tabIndex(); ?>" type="text" name="ExistingEventId" size="25"  value="<?php echo $_EventBriteId; ?>" />
	<input tabindex="<?php $spEvents->tabIndex(); ?>" alt="Import Data" src="../resources/images/importData.jpg" type="image" name="SubmitExistingEventId" value="SubmitExistingEventId" />
</div>