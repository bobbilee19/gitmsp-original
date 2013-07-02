<?php if( $event['event']['status'] == 'Draft' && $spEvents ) : ?>
	<div id='eventBriteDraft' class='error'>
		<p><?php _e('Eventbrite status is set to DRAFT. You can update this in wordpress or via the eventbrite admin.'); ?>
	</div>
<?php endif; ?>
<tr>
	<td colspan="2" class="snp_sectionheader">
		<h4><?php _e('Eventbrite Information', '$this->pluginDomain'); ?></h4>
	</td>
</tr>
<tr>
	<td width="125">
		<?php _e('Eventbrite Event ID:',$this->pluginDomain); ?>
	</td>
	<td>
		<a target="_blank" href="http://www.eventbrite.com/edit?eid=<?php echo $event['event']['id']; ?>"><?php echo $event['event']['id']; ?></a>
	</td>
</tr>
<tr>
	<td>
		<?php _e('Eventbrite Event Status:', $this->pluginDomain); ?>
	</td>
	<td>
	<?php if( $spEvents ) : ?>
		<select name="EventBriteStatus" tabindex="<?php $spEvents->tabIndex(); ?>">
	<?php else : ?>
		<select name="EventBriteStatus">
	<?php endif; ?>
			<option value='Draft' <?php if( $event['event']['status'] == 'Draft' ) echo "SELECTED"; ?> ><?php _e('Draft', '$this->pluginDomain'); ?></option>
			<option value='Live' <?php if( $event['event']['status'] == 'Live' ) echo "SELECTED"; ?> ><?php _e('Live', '$this->pluginDomain'); ?></option>
		</select>
	</td>
</tr>
<?php if (count( $event['event']['tickets'] )) : ?>
	<?php 
	$ebTecMultipleCosts = false; 
	$ebTecLastPrice = $event['event']['tickets'][0]['price'];
	?>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<h4><?php _e('Ticket Count:',$this->pluginDomain); ?></h4>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<table class="EB-table" border=0><tr><td width="120px"><?php _e('Ticket', $this->pluginDomain); ?></td><td width="85px">Cost</td><td width="40px"><?php _e('Sold', $this->pluginDomain); ?></td><td width="40px"><?php _e('Available', '$this->pluginDomain'); ?></td></tr>
			<?php foreach( (array)$event['event']['tickets'] as $ticket ) : ?>
				<?php 
				if( !$ebTecMultipleCosts ) {
					$ebTecMultipleCosts = ( $ticket['price'] == $ebTecLastPrice ) ? false : true;
					$ebTecLastPrice = $ticket['price'];
				}
				?>
				<tr>
					<td><a href="<?php echo $event['event']['url']; ?>" target="_blank"><?php echo $ticket['name']; ?></a></td>
					<td><?php echo $ticket['currency']; ?> <?php echo $ticket['price']; ?></td>
					<td><?php echo $ticket['quantity_sold']; ?></td>
					<td><?php echo $ticket['quantity_available']; ?></td>
				</tr>
			<?php endforeach; ?>
			</table>
		</td>
	</tr>
<?php else : ?>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<h4><?php _e('There are no tickets associated with this event!',$this->pluginDomain); ?></h4>
		</td>
	</tr>
<?php endif; ?>
<tr>
	<td colspan="2" class="snp_sectionheader">
		<h4><?php _e('Eventbrite Shortcuts:',$this->pluginDomain); ?></h4>
	</td>
</tr>
<tr>
	<td colspan="2">
		<ul>
			<li><a href="http://www.eventbrite.com/myevent?eid=<?php echo $_EventBriteId; ?>"><?php _e('Manage my Event', '$this->pluginDomain'); ?></a></li>
			<li><a href="http://www.eventbrite.com/discounts?eid=<?php echo $_EventBriteId; ?>"><?php _e('Manage Discounts', '$this->pluginDomain'); ?></a></li>
			<li><a href="http://www.eventbrite.com/attendees?eid=<?php echo $_EventBriteId; ?>"><?php _e('Manage Attendees', '$this->pluginDomain'); ?></a></li>
			<li><a href="http://www.eventbrite.com/attendees-email?eid=<?php echo $_EventBriteId; ?>"><?php _e('Email Attendees', '$this->pluginDomain'); ?></a></li>
			<li><a href="http://www.eventbrite.com/attendees-badges?eid=<?php echo $_EventBriteId; ?>"><?php _e('Print Badges', '$this->pluginDomain'); ?></a></li>
			<li><a href="http://www.eventbrite.com/attendees-list?eid=<?php echo $_EventBriteId; ?>"><?php _e('Print Check-In List', '$this->pluginDomain'); ?></a></li>
		</ul>
	</td>
</tr>