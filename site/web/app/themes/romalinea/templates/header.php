<header class="banner">
  <nav class="navbar navbar-expand-md navbar-dark bg-dark-color fixed-top">
      <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
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
      <div class="mx-auto order-0">
          <a class="navbar-brand mx-auto" href="<?= esc_url(home_url('/')); ?>">
            <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
              <span class="navbar-toggler-icon"></span>
          </button>
      </div>
      <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
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

</header>
  <?php do_action( 'prosilos_archive_products' ); ?>
