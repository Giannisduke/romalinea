<header class="banner first">
  <nav class="navbar navbar-expand-md navbar-dark bg-dark-color fixed-top">
      <div class="navbar-collapse collapse w-25 order-1 order-md-0 dual-collapse2">
          <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                  <button class="btn btn-sm" href="#">
                    <i class="ico search" aria-hidden="true"></i>
                  </button>
              </li>
              <li class="nav-item dropdown has-megamenu">
                <a class="nav-link dropdown-toggle btn btn-sm categories" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="ico hum">&nbsp;</i>
                       <span class="category"><?php _e( 'Επιλέξτε', 'prosilos' ); ?></span>
                       <br>
                       <span class="category"><?php _e( 'Κατηγορία', 'prosilos' ); ?></span>
                 </a>
                <div class="dropdown-menu megamenu" role="menu" aria-labelledby="navbarDropdown">
                <div class="row">
                <?php do_action('prosilos_header_carousel', 'prosilos_header_carousel_cats'); ?>
                </div>
                </div>
              </li>
          </ul>
      </div>
      <div class="logo">
          <a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">
            <img src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          </a>
          <?php dynamic_sidebar('sidebar-header'); ?>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
              <span class="navbar-toggler-icon"></span>
          </button>
      </div>
      <div class="navbar-collapse collapse w-25 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link btn btn-sm categories customlocation">

             </a>

          </li>
          <li class="nav-item">
              <a class="btn btn-sm" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                <i class="ico profile" aria-hidden="true"></i>
              </a>
          </li>
            <li class="nav-item">
                <a class="btn btn-sm" href="<?php echo get_permalink(wc_get_page_id('shop')); ?>">
                  <i class="ico shop" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
      </div>
  </nav>
    <div class="overlay"></div>
    <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
      <source src="https://dl.dropboxusercontent.com/s/r4wylvou3huxz31/production%20ID_4280744.mp4?dl=0" type="video/mp4">
    </video>
    <div class="container-fluid h-103">
      <div class="d-flex h-103 align-items-center">
        <div class="w-100 text-white">
          <?php do_action('prosilos_custom_front'); ?>

        </div>
      </div>
    </div>

</header>
