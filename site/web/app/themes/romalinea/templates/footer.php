
<?php do_action( 'prosilos_footer', 'prosilos_footer_subscribe', 10 ); ?>


<footer class="content-info">
  <div class="container">
  <div class="row bottom">
    <div class="col-4">
      <div class="row">
        <div class="col-12">
    <a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>
  </div>
  <div class="description">
    <?php bloginfo( 'description' ); ?>
    </div>
    </div>
    </div>
    <div class="col-2">
      <?php $menu_footer_1 = get_term(get_nav_menu_locations()['footer_nav_1'], 'nav_menu')->name; ?>
      <?php $menu_footer_2 = get_term(get_nav_menu_locations()['footer_nav_2'], 'nav_menu')->name; ?>
      <h3><?php echo $menu_footer_1; ?></h3>
      <?php
      wp_nav_menu( array(
      'theme_location'  => 'footer_nav_1',
      'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
      'container'       => false,
      'container_class' => '',
      'container_id'    => '',
      'menu_class'      => 'nav flex-column',
      'items_wrap'      => '<nav id="%1$s" class="%2$s">%3$s</nav>',
      'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
      'walker'          => new WP_Bootstrap_Navwalker(),
  ) );
  ?>
    </div>
    <div class="col-2">
      <h3><?php echo $menu_footer_2; ?></h3>
      <?php
      wp_nav_menu( array(
      'theme_location'  => 'footer_nav_2',
      'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
      'container'       => false,
      'container_class' => '',
      'container_id'    => '',
      'menu_class'      => 'nav flex-column',
      'items_wrap'      => '<nav id="%1$s" class="%2$s">%3$s</nav>',
      'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
      'walker'          => new WP_Bootstrap_Navwalker(),
  ) );
  ?>
    </div>
    <div class="col-4">
      <div class="d-flex align-items-end flex-column mb-3" style="height: 200px;">
    <div class="p-2">
      <?php
      wp_nav_menu( array(
      'theme_location'  => 'footer_social',
      'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
      'container'       => false,
      'container_class' => '',
      'container_id'    => '',
      'menu_class'      => 'nav',
      'items_wrap'      => '<nav id="%1$s" class="%2$s">%3$s</nav>',
      'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
      'walker'          => new WP_Bootstrap_Navwalker(),
  ) );
  ?>
    </div>
    <div class="mt-auto p-2 bd-highlight"></div>
  </div>
    </div>
  </div>
  </div>
</footer>
