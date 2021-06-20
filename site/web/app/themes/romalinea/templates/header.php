<header class="banner">
  <nav class="navbar navbar-expand-lg">
    <div class="container flex-column w-100 align-items-stretch" style="max-width:none;">
    <div class="row">
      <div class="d-flex align-items-center">
        <a href="<?php echo home_url(); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
        </a>
      <div>
      <?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>
      <h1><?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?></h1>
    </div>
    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#demo" aria-controls="demo" aria-expanded="false" aria-label="Toggle navigation">
     <span class="icon-bar top-bar"></span>
     <span class="icon-bar middle-bar"></span>
     <span class="icon-bar bottom-bar"></span>
     <span class="sr-only">Toggle navigation</span>
   </button>

    <?php //wp_nav_menu( array( 'theme_location'    => 'top_left' ) ); ?>
    <?php do_action( 'roma_header_form' ); ?>
  </div>
  <div class="tow">
    <?php do_action( 'roma_header_subheader' ); ?>
    </div>
  </div>
</nav>

</header>
  <?php do_action( 'prosilos_archive_products' ); ?>
