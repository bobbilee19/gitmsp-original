<?php /* This template is only used on multisite installations */ ?>

<?php get_header(); ?>

	<div id="container">
		<div id="colLeft">

		<?php do_action( 'bp_before_activation_page' ) ?>

		<div class="page" id="activate-page">

			<?php do_action( 'template_notices' ) ?>

			<?php if ( bp_account_was_activated() ) : ?>

				<h2 class="widgettitle"><?php _e( 'Account Activated', 'buddypress' ) ?></h2>

				<?php do_action( 'bp_before_activate_content' ) ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ) ?></p>
				<?php else : ?>
					<p><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up. Click <a href="http://www.girlsintechdet.com/memberslogin">here</a> to login.', 'buddypress' ) ?></p>
				<?php endif; ?>

			<?php else : ?>

				<h3><?php _e( 'Activate your Account', 'buddypress' ) ?></h3>

				<?php do_action( 'bp_before_activate_content' ) ?>

				<p><?php _e( 'Please provide a valid activation key.', 'buddypress' ) ?></p>

				<form action="" method="get" class="standard-form" id="activation-form">

					<label for="key"><?php _e( 'Activation Key:', 'buddypress' ) ?></label>
					<input type="text" name="key" id="key" value="" />

					<p class="submit">
						<input type="submit" name="submit" value="<?php _e( 'Activate', 'buddypress' ) ?> &rarr;" />
					</p>

				</form>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ) ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ) ?>

		</div><!-- #content -->
	</div><!-- #container -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
