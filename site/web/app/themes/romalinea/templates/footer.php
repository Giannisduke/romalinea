
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

    </div>
    <div class="col-2">

    </div>
    <div class="col-4">
      <div class="d-flex align-items-end flex-column mb-3" style="height: 200px;">
    <div class="p-2">

    </div>
    <div class="mt-auto p-2 bd-highlight"></div>
  </div>
    </div>
  </div>
  </div>
</footer>
