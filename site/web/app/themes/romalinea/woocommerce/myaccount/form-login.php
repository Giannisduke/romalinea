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

<div class="row">

	<form method="post" class="col-10 col-lg-3 login">
		<h2>Login</h2>
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
<div class="col-12 col-md-9 px-5">
	<h2>Εγγραφή</h2>
<?php echo gravity_form( 2, false, false, false, '', false ); ?>
</div>
	<?php endif; ?>
	</div>
	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
