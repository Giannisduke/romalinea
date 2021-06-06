<header class="banner">
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand mr-auto" href="<?= esc_url(home_url('/')); ?>">
      <div class="d-flex flex-row align-items-center">
        <div>
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
      </div>
      <div>
      <?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>
      <h1><?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?></h1>
    </div>
    </div>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php //wp_nav_menu( array( 'theme_location'    => 'top_left' ) ); ?>
    <?php echo facetwp_display( 'facet', 'product_search' ); ?>
    <?php wp_nav_menu( array(
      'theme_location'    => 'top_right',
      'menu_class'           => 'menu top-right',
      'items_wrap'           => '<ul class="%2$s">%3$s</ul>',
      'item_separator' => '&middot;',
      //'menu_id'   => 'menu_id',
      'link_before' => '<span class="text">',
      'link_after' => '</span>'
     ) ); ?>
  </nav>
  <?php //woocommerce_mini_cart(); ?>
</header>
  <?php do_action( 'prosilos_archive_products' ); ?>
