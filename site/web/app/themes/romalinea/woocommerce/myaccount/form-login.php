<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>



	<?php endif; ?>

	<form method="post" class="col-10 col-lg-3 login test">
	<?php do_action( 'woocommerce_login_form_start' ); ?>
	<fieldset class="form-group">
	<label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="text" class="form-control" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" name="username" id="username">
	</fieldset>
	<fieldset class="form-group">
	<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="password" class="form-control" name="password" id="password">
	</fieldset>
	<?php do_action( 'woocommerce_login_form' ); ?>
	<fieldset class="form-group">
	<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
	<input type="submit" class="btn btn-primary" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
	</fieldset>
	<div class="checkbox">
	<label>
	<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
	</label>
	</div>
	<p class="woocommerce-LostPassword lost_password">
	<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
	</p>
	<?php do_action( 'woocommerce_login_form_end' ); ?>
	</form>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

	<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>
	<form method="post" class="register">
	<?php do_action( 'woocommerce_register_form_start' ); ?>
	<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
	<fieldset class="form-group">
	<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="text" class="form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
	</fieldset>
	<?php endif; ?>
	<fieldset class="form-group">
	<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="email" class="form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
	</fieldset>

	<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
	<fieldset class="form-group">
	<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="text" class="form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
	</fieldset>
	<?php endif; ?>
	<fieldset class="form-group">
	<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="email" class="form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
	</fieldset>
	<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
	<fieldset class="form-group">
	<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="password" class="form-control" name="password" id="reg_password" />
	</fieldset>
	<?php endif; ?>
	<!-- Spam Trap -->
	<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>
	<?php do_action( 'woocommerce_register_form' ); ?>
	<?php do_action( 'register_form' ); ?>
	<fieldset class="form-group">
	<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
	<input type="submit" class="btn btn-primary" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
	</fieldset>
	<?php do_action( 'woocommerce_register_form_end' ); ?>
	</form>
	</div>
	<?php endif; ?>
	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
