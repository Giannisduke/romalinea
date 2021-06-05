<header class="banner">
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand mr-auto" href="<?= esc_url(home_url('/')); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php echo facetwp_display( 'facet', 'product_search' ); ?>

      <?php
     wp_nav_menu( array(
         'theme_location'    => 'primary_navigation'
     ) );
     ?>


  </nav>
</header>
  <?php do_action( 'prosilos_archive_products' ); ?>
