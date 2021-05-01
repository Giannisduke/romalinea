<?php while (have_posts()) : the_post(); ?>
  <?php //get_template_part('templates/page', 'header'); ?>
  <div class="row">
    <div class="container catalog">
      <div class="row">
  <?php get_template_part('templates/content', 'page'); ?>
  </div>
</div>
</div>
<?php endwhile; ?>
