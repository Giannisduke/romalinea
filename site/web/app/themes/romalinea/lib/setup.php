<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://roots.io/plugins/soil/
  add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-relative-urls');
  add_theme_support( 'woocommerce', array(
  'gallery_thumbnail_image_width' => 100,
  //'single_image_width' => 654,
  ) );
//  add_theme_support( 'wc-product-gallery-zoom' );
  //add_theme_support( 'wc-product-gallery-lightbox' );
  add_theme_support( 'wc-product-gallery-slider' );


  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('prosilos', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'top_left' => __('Top Left', 'prosilos')
  ]);
  register_nav_menus([
    'top_right' => __('Top Right', 'prosilos')
  ]);
  register_nav_menus([
    'footer_nav_1' => __('Footer Navigation 1', 'prosilos')
  ]);
  register_nav_menus([
    'footer_nav_2' => __('Footer Navigation 2', 'prosilos'),
    'footer_social' => __('Footer Social', 'prosilos')
  ]);


  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Register sidebars
 */
function widgets_init() {

  register_sidebar([
    'name'          => __('Primary', 'prosilos'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<div class="panel panel-default %1$s %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<div class="card-header collapsed" data-toggle="collapse" href="# %1$s"><a class="card-title %1$s %2$s">',
    'after_title'   => '</a></h2></div>'
  ]);

  register_sidebar([
    'name'          => __('Footer', 'prosilos'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<div class="col widget %1$s %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h5>',
    'after_title'   => '</h5>'
  ]);
  register_sidebar([
    'name'          => __('Header', 'prosilos'),
    'id'            => 'sidebar-header',
    'before_widget' => '<div class="col widget %1$s %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h5>',
    'after_title'   => '</h5>'
  ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');




/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
  static $display;

  isset($display) || $display = !in_array(true, [
    // The sidebar will NOT be displayed if ANY of the following return true.
    // @link https://codex.wordpress.org/Conditional_Tags
    is_404(),
    is_page(),
    is_page_template('template-custom.php'),
    is_product(),
    is_product_category(),
    is_shop(),
  ]);

  return apply_filters('sage/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);

}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
