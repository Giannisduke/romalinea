<footer class="content-info">
  <div class="container">
  <div class="row bottom">
    <div class="company">

<div class="row">
    <a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>

  <div class="description_b">
    <?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>
    <div class="h1"><?php bloginfo( 'description' ); ?></div>
  </div>
  </div>
    </div>
    <div class="menu_1">
      <?php $menu_footer_1 = get_term(get_nav_menu_locations()['footer_nav_1'], 'nav_menu')->name; ?>
      <?php $menu_footer_2 = get_term(get_nav_menu_locations()['footer_nav_2'], 'nav_menu')->name; ?>

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
    <div class="menu_2">

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
    <div class="newsletter">
      <div class="d-flex align-items-end flex-column mb-3" style="height: 200px;">

      <?php dynamic_sidebar('sidebar-footer'); ?>
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
  <ul class="social-icons">
    <li class="ico facebook">
      <a href="#">
        <span class="text">facebook</span>
      </a>
    </li>
    <li class="ico linkedin">
      <a href="#">
        <span class="text">linkedin</span>
      </a>
    </li>
    <li class="ico twitter">
      <a href="#">
        <span class="text">twitter</span>
      </a>
    </li>

  </ul>

    <div class="mt-auto p-2 bd-highlight"></div>
  </div>
    </div>
  </div>
  </div>
</footer>
