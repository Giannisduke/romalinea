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

<div class="row px-3">
<aside class="sidebar">
	<div class="heading">
		<span class="h2">Filters</span>
		<button class="btn btn-lg btn-white" onclick="FWP.reset()">
			<i class="ico reset" aria-hidden="true"></i>
		</button>
	</div>
<div class="panel-group" id="accordion">
	  <?php dynamic_sidebar( 'sidebar-primary' ); ?>
		</div>

</aside>
<div class="mainbar">
	<div class="d-flex flex-row">


<?php //echo facetwp_display( 'selections' ); ?>

		<div class="w-50">
				<?php echo facetwp_display( 'facet', 'product_search' ); ?>
	</div>
<div class="ml-auto w-25">
<?php echo facetwp_display( 'sort' ); ?>
</div>
	</div>
	<ul itemscope itemtype="https://schema.org/ItemList" class="facetwp-template">
