<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="row px-0">
<aside class="sidebar">
	<div class="container heading">
		<div class="row">
			<div class="col-6">
			<a href="#" onclick="FWP.reset()" class="reset-btn"><?php echo esc_html( 'Ακύρωση φίλτρων' ); ?></a>
		</div>
			<?php  echo facetwp_display( 'facet', 'result_counts' ); ?>
	</div>



	</div>
<div class="panel-group" id="accordion">
	  <?php dynamic_sidebar( 'sidebar-primary' ); ?>
		</div>

</aside>
<div class="mainbar">
	<div class="container p-0">
	<div class="catalog-header">


<?php echo facetwp_display( 'selections' ); ?>

<div class="views">
	<div class="form-check">
		<input class="form-check-input" type="radio" name="thubs_number" id="exampleRadios2" value="option2">
		<label class="form-check-label few" for="exampleRadios2"> </label>
		<input class="form-check-input" type="radio" name="thubs_number" id="exampleRadios1" value="option1" checked>
	  <label class="form-check-label many" for="exampleRadios1"> </label>
	</div>
</div>
<div class="sort">
<?php echo facetwp_display( 'sort' ); ?>
</div>
	</div>
	<ul itemscope itemtype="https://schema.org/ItemList" class="facetwp-template">
