<?php
/**
 * Template Name: test Template
 */
?>
  <?php //get_template_part('templates/page', 'header'); ?>
  <?php //get_template_part('templates/unit', 'carousel'); ?>
  <?php //get_template_part('templates/content', 'page'); ?>
  <?php //do_action( 'sxhma_shop' ); ?>



  <div class="row">
    <?php  echo facetwp_display( 'facet', 'product_categories' ); ?>
    <div class="container panel facetwp-template">
      <?php $args = array(
    			'post_type' => 'product',
    			'posts_per_page' => 11,
          'facetwp' => true
    			);
    		$loop = new WP_Query( $args );
    		if ( $loop->have_posts() ) {
    			while ( $loop->have_posts() ) : $loop->the_post();
    				wc_get_template_part( 'content', 'product' );
    			endwhile;
    		} else {
    			echo __( 'No products found' );
    		}
    		wp_reset_postdata();
    	?>
    </div>

  </div>
