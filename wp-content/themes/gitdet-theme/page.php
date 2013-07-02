<?php get_header(); ?>

<!-- begin colLeft -->
	<div id="colLeft">
		<h1><?php the_title(); ?></h1>	<br />
       <?php  global $user_login;
			if (is_page(5) ) {
			if ( is_user_logged_in() ) {
			     echo'<meta HTTP-EQUIV="REFRESH" content="0; url=http://www.girlsintechdet.com/activity">';
			} else {
			    echo 'Please login to view the Girls in Tech Detroit member area. <br /><br />';
			    	wp_login_form();
			    	echo 'If you are not a registered member, click ' . ' <a href="http://www.girlsintechdet.com/register">here</a>' . ' to join.';
			 }};
			 		
		?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<?php the_content(); ?>
		
		<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php endif; ?>
	</div>
	<!-- end colleft -->
	
	<?php get_sidebar(); ?>	

<?php get_footer(); ?>