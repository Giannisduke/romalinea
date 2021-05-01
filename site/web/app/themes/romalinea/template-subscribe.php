<?php
/**
 * Template Name: Subscribe Template
 */
?>
  <?php //get_template_part('templates/page', 'header'); ?>

  <?php while (have_posts()) : the_post(); ?>
    <?php //get_template_part('templates/page', 'header'); ?>
    <div class="row">
      <div class="container catalog">
        <div class="row">
          <div class="col-12">
    <?php the_content(); ?>
    <?php gravity_form( 1, false, false, false, '', true, 12 ); ?>
    </div>
    </div>
  </div>
  </div>
  <?php endwhile; ?>
