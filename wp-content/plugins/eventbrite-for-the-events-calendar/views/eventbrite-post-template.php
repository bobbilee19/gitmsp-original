<div id="eventbrite-ticketing">
	<h4><?php _e('Ticket Information', '$this->pluginDomain'); ?></h4>
	<table class="eventbrite" cellspacing="0">
		<thead>
			<tr>
				<th class='column_heading'><?php _e('Ticket Type', '$this->pluginDomain'); ?></td>
				<th class='column_heading'><?php _e('Remaining', '$this->pluginDomain'); ?></td>
				<th class='column_heading'><?php _e('Sales End', '$this->pluginDomain'); ?></td>
				<th class='column_heading'><?php _e('Price', '$this->pluginDomain'); ?></td>
				<th class='column_heading'><?php _e('Quantity', '$this->pluginDomain'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $event['event']['tickets'] as $ticket ) : ?>
			<tr>
				<td class="name"><?php echo $ticket['name']; ?></td>
				<td><?php echo $ticket['quantity_available']; ?></td>
				<td><?php echo $ticket['end_date']; ?></td>
				<td><?php echo $ticket['price']; ?> <?php echo $ticket['currency']; ?></td>
				<td class="quantity"><?php // quantity pulldown here ?></td>
			</tr>
			<?php endforeach; // ticket ?>
		</tbody>
	</table>
</div>
<?php
/*

Here is the format of an event, in case you need other fields, such as the id, to link the purchase
box up with eventbrite.

example: <?php echo $event['event']['id']; ?>


	[17-Aug-2009 11:15:15] eventbrite_event_get: Array
	(
	    [event] => Array
	        (
	            [id] => 409753584
	            [background_color] => FFFFFF
	            [box_background_color] => FFFFFF
	            [box_border_color] => D5D5D3
	            [box_header_background_color] => EFEFEF
	            [box_header_text_color] => 005580
	            [box_text_color] => 000000
	            [capacity] => 0
	            [created] => 2009-08-14 14:47:58
	            [description] => setting description
	            [end_date] => 2009-08-20 11:29:00
	            [link_color] => EE6600
	            [logo] => http://images.eventbrite.com/logos/
	            [logo_ssl] => https://www.eventbrite.com/php/logo.php?id=
	            [modified] => 2009-08-14 14:47:58
	            [privacy] => Private
	            [start_date] => 2009-08-20 11:29:00
	            [status] => Draft
	            [text_color] => 005580
	            [tickets] => Array
	                (
	                    [ticket] => Array
	                        (
	                            [currency] => USD
	                            [description] => desc
	                            [end_date] => 2009-08-20 11:29:00
	                            [id] => 8632939
	                            [name] => general admission
	                            [price] => 465.95
	                            [quantity_available] => 654
	                            [quantity_sold] => 0
	                            [start_date] => 2009-08-14 14:47:58
	                            [type] => 0
	                        )

	                )

	            [timezone] => US/Arizona
	            [title] => Test EventBrite API updating title
	            [url] => http://www.eventbrite.com/event/409753584
	        )

	)
*/
?>