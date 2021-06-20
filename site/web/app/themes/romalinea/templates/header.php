<header class="banner">
  <nav class="navbar">

      <div class="d-flex align-items-center">
        <a href="<?php echo home_url(); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
        </a>
      <?php wp_nav_menu( array(
        'theme_location'    => 'top_left',
        'menu_class'           => 'menu top-left order-md-2 ml-auto',
        'items_wrap'           => '<ul class="%2$s">%3$s</ul>',
        'item_separator' => '&middot;',
        //'menu_id'   => 'menu_id',
        'link_before' => '<span class="text">',
        'link_after' => '</span>'
       ) ); ?>
      <div>
      <?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>
      <h1><?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?></h1>
    </div>
    </div>


    <?php //wp_nav_menu( array( 'theme_location'    => 'top_left' ) ); ?>
    <?php do_action( 'roma_header_form' ); ?>
    <?php do_action( 'roma_header_subheader' ); ?>
</nav>

</header>
  <?php do_action( 'prosilos_archive_products' ); ?>
