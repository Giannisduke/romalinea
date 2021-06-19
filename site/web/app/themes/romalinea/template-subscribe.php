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
          <div class="row h-100 justify-content-center align-items-center">
            <div class="col-12">
              <?php the_content(); ?>
          </div>
              <?php do_action ('prosilos_get_contact'); ?>
          </div>
        </div>
    </div>
    <?php endwhile; ?>
