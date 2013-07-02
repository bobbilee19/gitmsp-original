<?php
/*
Plugin Name: jPic WordPress Widget
Plugin URI: http://www.jhack.it/wp-jpic
Description: A Jeep (General Purpose) plugin which, given a url of an image, shows it in a widget
Author: Giacomo Boccardo
Version: 0.4
Author URI: http://www.jhack.it


Copyright 2008  Giacomo Boccardo  (email : gboccard@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

define('jpicDirpath','/wp-content/plugins' . strrchr(dirname(__FILE__),'/') . '/');

$jpic_defaults = array(
	'title'	=> 'jPic',
	'imgSrc'		=> '', 
	'imgHref'		=> '',
	'imgAlt'		=> 'What the hell is it? :O',
	'imgTitle'		=> '',
	'imgWidth'		=> '',
	'imgHeight'		=> '',
	'showCredits'	=> 1
);


function jpic_output($args = NULL)
{
	global $jpic_defaults;
	if (isset($args)) extract($args);
	
	$options = (array) get_option('widget_jpic');

	$title =  ( $title == '' ) ? ( ( $options['title'] != '' ) ? $options['title'] : $jpic_defaults['title'] ) : $title;
	$imgSrc =  ( $imgSrc == '' ) ? ( ( $options['imgSrc'] != '' ) ? $options['imgSrc'] : $jpic_defaults['title'] ) : $imgSrc;
	$imgHref =  ( $imgHref == '' ) ? ( ( $options['imgHref'] != '' ) ? $options['imgHref'] : $jpic_defaults['imgHref'] ) : $imgHref;	
	$imgAlt =  ( $imgAlt == '' ) ? ( ( $options['imgAlt'] != '' ) ? $options['imgAlt'] : $jpic_defaults['imgAlt'] ) : $imgAlt;		
	$imgTitle =  ( $imgTitle == '' ) ? ( ( $options['imgTitle'] != '' ) ? $options['imgTitle'] : $jpic_defaults['imgTitle'] ) : $imgTitle;	
	
	$imgWidth =  ( $imgWidth == '' ) ? ( ( $options['imgWidth'] != '' && intval($options['imgWidth']) >= 1 ) ? $options['imgWidth'] : $jpic_defaults['imgWidth'] ) : $imgWidth;	
	$imgHeight =  ( $imgHeight == '' ) ? ( ( $options['imgHeight'] != '' && intval($options['imgHeight']) >= 1 ) ? $options['imgHeight'] : $jpic_defaults['imgHeight'] ) : $imgHeight;	

	$showCredits =  ( ! isset($showCredits) ) ? ( ( isset($options['showCredits']) ) ? $options['showCredits'] : $jpic_defaults['showCredits'] ) : $showCredits;		

	$before_widget = isset($before_widget) ? $before_widget : '<li id="jpic_widget" class="widget widget_jpic">';
	$before_title = isset($before_title) ? $before_title : '<h2 class="widgettitle">';
	$after_title = isset($after_title) ? $after_title : '</h2>';						
	$after_widget = isset($after_widget) ? $after_widget : '</li>';
		
		
	echo $before_widget;
	
	echo $before_title . $title . $after_title;
		
				
	$style = "";		
	if ( $imgWidth != '' || $imgHeight != '' ) {
		$style = "";
		if ( $imgWidth != '' )
	 	{
			if ( $imgHeight != '' )
				$style .= ' style="width: '.$imgWidth.'px !important; height: '.$imgHeight.'px !important;" ';	
			else
				$style .= ' style="width: '.$imgWidth.'px !important; "';					
		} else {
			if ( $imgHeight != '' )
				$style .= ' style="height: '.$imgHeight.'px !important; "';	
		}
	}
		
	$img = '<img id="jPicImg" src="'.$imgSrc.'" alt="'.$imgAlt.'" title="'.$imgTitle.'"'.$style.' />';


	if ( $imgHref != "" )
		$content = '<a href="'.$imgHref.'">'. $img .'</a>';
	

	if ( ! $showCredits ) $hide = 'class="hide"';
	
	$developed = '<div id="jpicCredits" '. $hide.'>developed by <a href="http://www.jhack.it">Jhack</a></div>';
		
	$content = 	'<div id="widget_jpic_div">'. $content . $developed . '</div>';
	
	echo $content . $after_widget;

}


function widget_jpic_control()
{
	global $jpic_defaults;
	
	if ( $_POST['jpic_submit'] )
	{
		$newValues['title'] 	= strip_tags(stripslashes($_POST['jpic_title']));
		$newValues['imgSrc'] 		= strip_tags(stripslashes($_POST['jpic_imgSrc']));
		$newValues['imgHref'] 		= strip_tags(stripslashes($_POST['jpic_imgHref']));
		$newValues['imgAlt'] 		= strip_tags(stripslashes($_POST['jpic_imgAlt']));
		$newValues['imgTitle'] 		= strip_tags(stripslashes($_POST['jpic_imgTitle']));
		
		$newValues['imgWidth'] 		= intval($_POST['jpic_imgWidth']);
		$newValues['imgHeight'] 	= intval($_POST['jpic_imgHeight']);
		
		$newValues['showCredits'] 	= isset($_POST['jpic_showCredits']);
		
		update_option('widget_jpic', $newValues);
	}
	
	$options = get_option('widget_jpic');
	

	
	$title 	= ( $options['title'] != '' ) ? $options['title'] : $jpic_defaults['title'];
	$imgSrc 		= ( $options['imgSrc'] != '' ) ? $options['imgSrc'] : $jpic_defaults['imgSrc'];
	$imgHref		= ( $options['imgHref'] != '' ) ? $options['imgHref'] : $jpic_defaults['imgHref'];
	$imgAlt 		= ( $options['imgAlt'] != '' ) ? $options['imgAlt'] : $jpic_defaults['imgAlt'];
	$imgTitle 		= ( $options['imgTitle'] != '' ) ? $options['imgTitle'] : $jpic_defaults['imgTitle'];
	
	$imgWidth 		= ( intval($options['imgWidth']) >= 1 ) ? $options['imgWidth'] : $jpic_defaults['imgWidth'];
	$imgHeight 		= ( intval($options['imgHeight']) >= 1 ) ? $options['imgHeight'] : $jpic_defaults['imgHeight'];	

	$showCredits 	= isset($options['showCredits']) ? ( $options['showCredits'] ? 'checked="checked"' : '' ) : 'checked="checked"';
	
	
	echo '<p style="text-align:right;"><label for="jpic_title">Widget Title: <input style="width: 300px;" id="jpic_title" name="jpic_title" type="text" value="'.$title.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgSrc">Image\'s URL: <input style="width: 300px;" id="jpic_imgSrc" name="jpic_imgSrc" type="text" value="'.$imgSrc.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgHref">Image\'s Link (optional): <input style="width: 300px;" id="jpic_imgHref" name="jpic_imgHref" type="text" value="'.$imgHref.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgAlt">Image\'s Alternative Text: <input style="width: 300px;" id="jpic_imgAlt" name="jpic_imgAlt" type="text" value="'.$imgAlt.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgTitle">Image\'s Title (optional): <input style="width: 300px;" id="jpic_imgTitle" name="jpic_imgTitle" type="text" value="'.$imgTitle.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgWidth">Image\'s width (px, optional): <input style="width: 300px;" id="jpic_imgWidth" name="jpic_imgWidth" type="text" value="'.$imgWidth.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_imgHeight">Image\'s height (px, optional): <input style="width: 300px;" id="jpic_imgHeight" name="jpic_imgHeight" type="text" value="'.$imgHeight.'" /></label></p>';
	echo '<p style="text-align:right;"><label for="jpic_showCredits">Show credits: <input style="width: 300px;" '. $showCredits.' type="checkbox" id="jpic_showCredits" name="jpic_showCredits"  /></label></p>';	
	echo '<input type="hidden" id="jpic_submit" name="jpic_submit" value="1" />';
}


function widget_jpic_init()
{
	if ( ! function_exists('register_sidebar_widget') || ! function_exists('register_widget_control') ) return;


	function widget_jpic($args) {
		extract($args);
		?>
		
		<?php echo jpic_output(); ?>
		
		<?php
	}

	register_sidebar_widget(array('jPic', 'widgets'), 'widget_jpic');
	register_widget_control(array('jPic', 'widgets'), 'widget_jpic_control', 500, 280);
}


function jpic_stylesheet() 
{
	$curPath = get_option('siteurl').jpicDirpath;
	
	echo "<link rel=\"stylesheet\" href=\"".$curPath."css/jpic.css\" type=\"text/css\" media=\"screen\" />";	
}


// Initialize the widget
add_action('widgets_init', 'widget_jpic_init');

// Add stylesheet to head
add_action('wp_head', 'jpic_stylesheet');



?>
