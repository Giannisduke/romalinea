<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/sidebar_widget.php',
  'lib/controls.php',
  'lib/breadcrumb.php',
  'lib/woocommerce.php',
//  'facetwp/custom_checkboxes.php',
//  'facetwp/custom_checkboxes_b.php',
  'class-wp-bootstrap-navwalker.php',
  'lib/Gravity-Forms-ACF-Field/acf-gravity_forms.php'
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }
  require_once $filepath;
}
unset($file, $filepath);

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
  //  $mimes['swf']  = 'application/x-shockwave-flash';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

add_post_type_support( 'page', 'excerpt' );

function filter_function_name_5115( $content, $instance, $args ){
	// filter...
  echo '<div class="textwidget custom-html-widget">'; // The textwidget class is for theme styling compatibility.
  echo $content;
  echo '</div>';
	return $content;
}
//add_filter( 'widget_custom_html_content', 'filter_function_name_5115', 10, 3 );

function add_active_class($classes, $item) {
  $class_names = array( 'current-menu-item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent',  'current_page_ancestor' );

  if( $item->menu_item_parent == 0 && in_array( $class_names, $classes) ) {
    $classes[] = "active";
  }

  return $classes;
}
add_filter('nav_menu_css_class', 'add_active_class', 10, 2 );

function add_widget_name_id($params) {

    //$params[0]['before_title'] = '<div class="card-header collapsed" data-toggle="collapse" href="#' . $params[0]['widget_id'] . '"><a class="card-titles">' ;
    $params[0]['before_title'] = '<div class="panel-heading"><h2 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" href="#' . $params[0]['widget_id'] . '">' ;
    return $params;
}
add_filter('dynamic_sidebar_params', 'add_widget_name_id');

function prefix_modify_nav_menu_args( $args ) {
    return array_merge( $args, array(
        'walker' => new WP_Bootstrap_Navwalker(),
    ) );
}
//add_filter( 'wp_nav_menu_args', 'prefix_modify_nav_menu_args' );

####################################################
#    Woocommerce remove css
####################################################

add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
function jk_dequeue_styles( $enqueue_styles ) {
  unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
  unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
  unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
  return $enqueue_styles;
}

####################################################
#    Woocommerce add body class to front page
####################################################
function woo_body_classes( $classes ) {

    if ( is_front_page() ) {
    $classes[] = 'woocommerce';
    }

    return $classes;

}
add_filter( 'body_class','woo_body_classes' );


add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
	if ( isset( $query->query_vars['facetwp'] ) ) {
		$is_main_query = (bool) $query->query_vars['facetwp'];
	}
	return $is_main_query;
}, 10, 2 );

function prosilos_header_carousel_cats() {
  //wp_list_categories( array('taxonomy' => 'product_cat', 'title_li'  => '') );

  $taxonomy     = 'product_cat';
  $orderby      = 'name';
  $show_count   = 0;      // 1 for yes, 0 for no
  $pad_counts   = 0;      // 1 for yes, 0 for no
  $hierarchical = 1;      // 1 for yes, 0 for no
  $title        = '';
  $empty        = 0;

  $args = array(
         'taxonomy'     => $taxonomy,
         'orderby'      => $orderby,
         'show_count'   => $show_count,
         'pad_counts'   => $pad_counts,
         'hierarchical' => $hierarchical,
         'title_li'     => $title,
         'hide_empty'   => $empty
  );
 $all_categories = get_categories( $args );
 foreach ($all_categories as $cat) {
   $menu_cat_url = wc_get_page_permalink( 'shop' ) . '?_product_categories=' . $cat->slug;
    if($cat->category_parent == 0) {
        $category_id = $cat->term_id;
        $args2 = array(
                'taxonomy'     => $taxonomy,
                'child_of'     => 0,
                'parent'       => $category_id,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
        );

        echo '<div class="col-md-3"><div class="col-megamenu">';
        echo '<h6 class="title"><a class="dropdown-item parent '. $cat->slug . '" href="'. $menu_cat_url .' ">'. $cat->name .'</a></h6>';
        $sub_cats = get_categories( $args2 );
                    if($sub_cats) {
                      echo '<ul class="list-unstyled">';
                        foreach($sub_cats as $sub_category) {
                          $submenu_cat_url = wc_get_page_permalink( 'shop' ) . '?_product_categories=' . $sub_category->slug;
                            echo  '<li><a class="dropdown-item child '. $cat->slug . '" href="'. $submenu_cat_url .' ">'. $sub_category->name .'</a></li>';
                        }
                        echo '</ul>';
                    }
        echo '</div></div>';

    }
}
}
add_action( 'prosilos_header_carousel', 'prosilos_header_carousel_cats', 10);


if ( ! function_exists( 'woocommerce_before_shop_loop_item_title_carousel' ) ) {
    function woocommerce_before_shop_loop_item_title_carousel() {
        echo woocommerce_get_product_thumbnail_front();
    }
}
if ( ! function_exists( 'woocommerce_get_product_thumbnail_front' ) ) {
    function woocommerce_get_product_thumbnail_front( $size = 'full', $placeholder_width = 0, $placeholder_height = 0  ) {
        global $post, $woocommerce;
        $output = '<div class="col-9 mx-auto text-center relative">';

        if ( has_post_thumbnail() ) {
            $output .= get_the_post_thumbnail( $post->ID, $size );
        }
        $output .= '</div>';
        return $output;
    }
}
add_action( 'woocommerce_before_shop_loop_item_title_front', 'woocommerce_before_shop_loop_item_title_carousel', 10);

####################################################
#    CAROUSEL
####################################################

function prosilos_front_product_carousel(){ ?>
  <div id="productscarouselindicators" class="carousel slide" data-interval="false">

<div class="w-50">
  <a class="btn btn-primary" href="#" role="button">Link</a>
</div>
<div class="w-50">
  <ol class="carousel-indicators">
  <?php
  $args = array(
      'post_type' => 'product',
      'posts_per_page' => 6,
      'facetwp' => false,
      'tax_query' => array(
              array(
                  'taxonomy' => 'product_visibility',
                  'field'    => 'name',
                  'terms'    => 'featured',
              ),
          ),
      );
  $query = new WP_Query( $args );
  ?>
  <?php if($query->have_posts()) : ?>
  <?php $i = 0; ?>
  <?php while($query->have_posts()) : $query->the_post() ?>
  <li data-target="#productscarouselindicators" data-slide-to="<?php echo $i ?>" class="<?php if($i === 0): ?>active<?php endif; ?>"></li>
  <?php $i++; ?>
<?php endwhile; ?>
  <?php endif ?>
  <?php wp_reset_postdata(); ?>
  </ol>
</div>
  <div class="carousel-inner" role="listbox">
  <?php
  $args = array(
      'post_type' => 'product',
      'posts_per_page' => 6,
      'facetwp' => false,
      'tax_query' => array(
              array(
                  'taxonomy' => 'product_visibility',
                  'field'    => 'name',
                  'terms'    => 'featured',
              ),
          ),
      );
  $query = new WP_Query( $args );
  ?>
  <?php if($query->have_posts()) : ?>
  <?php $i = 0; ?>
  <?php while($query->have_posts()) : $query->the_post() ?>
  <div class="carousel-item <?php if($i === 0): ?>active<?php endif; ?>">
  <?php wc_get_template_part( 'content', 'product_front' );?>
  </div>
  <?php $i++; ?>
  <?php endwhile ?>
  <?php endif ?>
  <?php wp_reset_postdata(); ?>
  </div>
  <!-- Controls -->
  <a class="carousel-control-prev" href="#productscarouselindicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#productscarouselindicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
  </div>
<?php }
add_action('prosilos_custom_front', 'prosilos_front_product_carousel', 10);

####################################################
#    facetwp
####################################################

add_filter( 'facetwp_sort_options', function( $options, $params ) {
    $options['price_desc'] = array(
        'label' => 'Price (Highest)',
        'query_args' => array(
            'orderby' => 'meta_value_num',
            'meta_key' => '_price',
            'order' => 'DESC',
        )
    );
    return $options;
}, 10, 2 );
add_filter( 'facetwp_sort_options', function( $options, $params ) {
  $sort_text = __( 'Τιμή', 'prosilos' );
  $currency_lower = $sort_text . ' ' . get_woocommerce_currency_symbol() . ' - ' . get_woocommerce_currency_symbol() . get_woocommerce_currency_symbol();
  //$currency_lower = echo ' ';
  $options['default'] = array(
      'label' =>  $currency_lower,
      'query_args' => array(
          'orderby' => 'meta_value_num',
          'meta_key' => '_price',
          'order' => 'ASC',
      )
  );
    $options['views'] = array(
        'label' => 'Most Views',
        'query_args' => array(
            'orderby' => 'post_views',
        )
    );
    return $options;
}, 10, 3 );


function prosilos_front_shop(){  ?>
  <div class="row">
    <div class="col-3">
  <div class="panel-group" id="accordion">
  	  <?php dynamic_sidebar( 'sidebar-primary' ); ?>
  		</div>
      </div>
      <div class="col-3">
  <ul itemscope itemtype="https://schema.org/ItemList" class="facetwp-template">
     <li class="popular">
       <div class="card">
         <div class="popular_product mx-auto"></div>
         <div class="card-body test">
           <h5 class="card-title"><?php _e( 'Δημοφιλη Προϊοντα', 'prosilos' ); ?></h5>
           <p class="card-text">Είμαστε εδώ για να σας βοηθήσουμε να επιλέξετε το κατάλληλο προϊόν.</p>
         </div>
       </div>
     </li>
  	<?php $args = array(
  			'post_type' => 'product',
  			'posts_per_page' => 11,
        'facetwp' => true
  			);
  		$loop = new WP_Query( $args );
  		if ( $loop->have_posts() ) {
        rewind_posts();
  			while ( $loop->have_posts() ) : $loop->the_post();
  				wc_get_template_part( 'content', 'product' );
  			endwhile;
  		} else {
  			echo __( 'No products found' );
  		}
  		wp_reset_postdata();
  	?>
</ul>
</div>
</div>

<?php  echo facetwp_display( 'facet', 'load_more' ); ?>


<? }

add_action('prosilos_front', 'prosilos_front_shop', 10);


function prosilos_front_brands() {
  $wcatTerms = get_terms('brands',
  array(
    'hide_empty' => 1,
    'parent' =>0
  ));
 ?>
 <ul class="brands">
  <?php foreach($wcatTerms as $wcatTerm) :
    $product_brand_url = get_permalink(wc_get_page_id('shop')) . '?_brands=' . $wcatTerm->slug;

    $image = get_field('brands_category_image', 'category_' . $wcatTerm->term_id . '' );
    ?>

   <?php if( !empty($image) ): ?>
    <li>
       <a href="<?php echo $product_brand_url; ?>"><img src="<?php echo $image['url']; ?>" alt"<?php echo $wcatTerm->name; ?>"></a>
    </li>
    <?php endif; ?>

 <?php
    endforeach;
    ?>
    </ul>
  <?php
}

add_action('prosilos_front', 'prosilos_front_brands', 20);


/**
 * @snippet       Change "Add to Cart" Button Label if Product Already @ Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @source        https://businessbloomer.com/?p=73974
 * @author        Rodolfo Melogli
 * @compatible    WC 3.5.4
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

// Part 1
// Edit Single Product Page Add to Cart
add_filter( 'woocommerce_product_single_add_to_cart_text', 'bbloomer_custom_add_cart_button_single_product' );

function bbloomer_custom_add_cart_button_single_product( $label ) {

   foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
      $product = $values['data'];
      if( get_the_ID() == $product->get_id() ) {
         $label = __('Ήδη στο καλάθι. Προσθήκη?', 'woocommerce');
      }
   }

   return $label;

}

// Part 2
// Edit Loop Pages Add to Cart

add_filter( 'woocommerce_product_add_to_cart_text', 'bbloomer_custom_add_cart_button_loop', 99, 2 );

function bbloomer_custom_add_cart_button_loop( $label, $product ) {

   if ( $product->get_type() == 'simple' && $product->is_purchasable() && $product->is_in_stock() ) {

      foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
         $_product = $values['data'];
         if( get_the_ID() == $_product->get_id() ) {
            $label = __('Ήδη στο καλάθι. Προσθήκη?', 'woocommerce');
         }
      }

   }

   return $label;

}

add_filter( 'loop_shop_columns', 'bt_new_loop_columns_per_page' );
/**
 * How many columns per page
 * @since 1.0.0
 */
function bt_new_loop_columns_per_page( $cols ) {
    // Return the number of columns you want show per page.
    $cols = 3;
    return $cols;
}


function woocommerce_get_sku() {
  global $product;
  $sku = $product->get_sku(); ?>
  <div class="product-sku">
      <?php echo 'κωδικός: ' . $sku; ?>
  </div>
<?php }
//add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_get_sku', 45 );


remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
//remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
//remove_action ('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

function prosilos_template_loop_product_title() {
    echo '<div class="row"><div class="col-12"><h4 class="card-title">' . get_the_title() . '</h4></div>';
}
add_action('woocommerce_shop_loop_item_title', 'prosilos_template_loop_product_title', 10 );

function prosilos_template_loop_price_before() {
    echo '<div class="price-wrap">';
}
add_action('woocommerce_shop_loop_item_title', 'prosilos_template_loop_price_before', 20 );

$user = wp_get_current_user();
if ( in_array( 'administrator', (array) $user->roles ) ) {
    //The user has the "author" role
  //  add_action ('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
    add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_price', 30 );
}
//
if ( in_array( 'customer', (array) $user->roles ) ) {
    //The user has the "author" role
  //  add_action ('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
    add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_price', 30 );
}


function prosilos_template_loop_price_after() {
    echo '</div>';
}
add_action('woocommerce_shop_loop_item_title', 'prosilos_template_loop_price_after', 40 );

remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10);
//add_action('woocommerce_after_shop_loop_item_2','woocommerce_template_loop_add_to_cart',10);


function remove_class_price_html( $price, $product ) {
  return str_replace( '<span class="woocommerce-Price-amount amount">', '', $price );
}
//add_filter( 'woocommerce_get_price_html', 'remove_class_price_html', 10, 2 );

add_action('woocommerce_shop_loop_item_title', 'flexrow_end', 40 );
function flexrow_end() {
    echo '</div>';
}

add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);

function change_attachement_image_attributes( $attr, $attachment ){
    // Get post parent
    $parent = get_post_field( 'post_parent', $attachment);

    // Get post type to check if it's product
    $type = get_post_field( 'post_type', $parent);
    if( $type != 'product' ){
        return $attr;
    }

    /// Get title
    $title = get_post_field( 'post_title', $parent);

    $attr['alt'] = $title;
    $attr['title'] = $title;

    return $attr;
}

//add_action( 'woocommerce_after_shop_loop_item', 'add_to_cart_button_woocommerce_start', 7 );
function add_to_cart_button_woocommerce_start() {

    echo '<div class="card-footer">';
}

//add_action( 'woocommerce_after_shop_loop_item', 'add_to_cart_button_woocommerce_end', 20 );
function add_to_cart_button_woocommerce_end() {
    echo '</div>';
}

function woocommerce_template_loop_product_link_open() {
    global $product;

    $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

    echo '<a href="' . esc_url( $link ) . '" class="card-body woocommerce-LoopProduct-link woocommerce-loop-product__link">';
  }

function woocommerce_template_loop_product_link_open_front() {
      global $product;

      $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

    //  echo '<a href="' . esc_url( $link ) . '" class="test woocommerce-LoopProduct-link woocommerce-loop-product__link">';
    }
add_action( 'woocommerce_before_shop_loop_item_front', 'woocommerce_template_loop_product_link_open_front', 10 );

function woocommerce_template_loop_product_cat_front() {
      global $post;
      $terms = get_the_terms( $post->ID, 'product_cat' );

      if ( $terms && ! is_wp_error( $terms ) ) : //only displayed if the product has at least one category

              $cat_links = array();
              foreach ( $terms as $term ) {
                if($term->term_id != 31)
                      $cat_links[0] = '<h2><a class="label-cat" href="'. wc_get_page_permalink( 'shop' ) . '?_product_categories='.$term->slug.'" title="'.$term->name.'">'.$term->name.'</a></h2>';
              }

              $on_cat = join( " ", $cat_links );
      ?>

      <div class="col-9 mx-auto pt-9">
          <?php echo $on_cat; ?>
      </div>

      <?php endif;

    }
add_action( 'woocommerce_before_shop_loop_item_front', 'woocommerce_template_loop_product_cat_front', 20 );

function woocommerce_template_loop_product_tag_front() {
      global $post;
      $tags = get_the_terms( $post->ID, 'product_tag' );

      if ( $tags && ! is_wp_error( $tags ) ) : //only displayed if the product has at least one category

              $cat_links = array();
              $first = true;
              foreach ( $tags as $tag ) {


                if($first)
                      $cat_links[] = '<h2><a class="label-tag" href="'.get_home_url().'/?product_tag='.$tag->slug.'" title="'.$tag->name.'">'.$tag->name.'</a></h2>';
                      $first = false;
              }

              $on_cat = join( " ", $cat_links );
      ?>

      <div class="col-9 mx-auto">
          <?php echo $on_cat; ?>
      </div>

      <?php endif;

    }
add_action( 'woocommerce_before_shop_loop_item_front', 'woocommerce_template_loop_product_tag_front', 30 );

function woocommerce_template_loop_product_name_front() {
  global $product;

  // If the WC_product Object is not defined globally
  if ( ! is_a( $product, 'WC_Product' ) ) {
      $product = wc_get_product( get_the_id() );
  }

  echo '<div class="col-12 text-center position"><div class="col-9 mx-auto">' . $product->get_name() . '</div></div>';
  echo '<div class="col-12 more"><a class="btn btn-md btn-custom round" href="' . $product->get_permalink() . '" role="button">Πληροφορίες</a></div>';
    }
add_action( 'woocommerce_before_shop_loop_item_front', 'woocommerce_template_loop_product_name_front', 40 );

####################################################
#    Shop loop
####################################################
function woocommerce_output_content_wrapper() { ?>
  <div class="row">
  <div class="container catalog">
<?php }
//add_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );

function woocommerce_output_content_wrapper_end() { ?>
</div>
</div>
<?php }
//add_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

function jk_woocommerce_breadcrumbs() {
return array(
        'delimiter'   => '',
        'wrap_before' => '<div class="d-flex justify-content-left facetwp-selections">',
        'wrap_after' => '',
        'before'      => '<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">',
        'after'       => '</div>',
    //    'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
    );
}



function tm_child_remove_product_title( $crumbs, $breadcrumb ) {

if ( is_product() ) {

array_pop( $crumbs );

}

return $crumbs;
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );

}
add_filter( 'woocommerce_get_breadcrumb', 'tm_child_remove_product_title', 10, 2 );

add_filter( 'woocommerce_get_breadcrumb', 'custom_breadcrumb', 10, 2 );
function custom_breadcrumb( $crumbs, $object_class ){

    // Loop through all $crumb
    foreach( $crumbs as $key => $crumb ){
        $taxonomy = 'product_cat'; // The product category taxonomy

        // Check if it is a product category term
        $term_array = term_exists( $crumb[0], $taxonomy );

        // if it is a product category term
        if ( $term_array !== 0 && $term_array !== null ) {

            // Get the WP_Term instance object
            $term = get_term( $term_array['term_id'], $taxonomy );

            // HERE set your new link with a custom one
            $crumbs[$key][1] = ( wc_get_page_permalink( 'shop' ) . '?_product_categories=' . $term->slug ); // or use all other dedicated functions
        }
    }

    return $crumbs;
}



remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );


add_action('save_post', 'assign_parent_terms', 10, 2);

function assign_parent_terms($post_id, $post){

    if($post->post_type != 'product')
        return $post_id;

    // get all assigned terms
    $terms = wp_get_post_terms($post_id, 'product_cat' );
    foreach($terms as $term){
        while($term->parent != 0 && !has_term( $term->parent, 'product_cat', $post )){
            // move upward until we get to 0 level terms
            wp_set_post_terms($post_id, array($term->parent), 'product_cat', true);
            $term = get_term($term->parent, 'product_cat');
        }
    }
}



####################################################
#    VIDEO
####################################################

function loukia_front_carousel(){
  if ( is_shop() ){
		$page_id = get_option( 'woocommerce_shop_page_id' );
	}
	else {
		$page_id = get_the_ID();
	}
        if( have_rows('carousel', $page_id) ):$counter = 0;?>
        <!--Carousel Section-->
      <section class="hero collapse show" id="herocollapse" >
        <!--Carousel Wrapper-->
        <div id="video-carousel" class="carousel slide carousel-fade home-section" data-interval="false">
          <!--Slides-->
          <div class="carousel-inner" role="listbox">

                <?php while( have_rows('carousel', $page_id) ): the_row();
                  //  $slide_title = get_sub_field('slide_title');
                  //  $slide_subtitle = get_sub_field('slide_subtitle');
                    $slide_text = get_sub_field('slide_text', $page_id);
                    $slide_image = get_sub_field('slide_image_background', $page_id);

                  //  $slide_video = get_sub_field('slide_video');
                    $slide_external_video = get_sub_field('slide_external_video');
                    ?>
                    <div class="carousel-item <?php if($counter === 0){ echo "active";} ?>" data-slide-no="<?php echo $counter;?>" style="background: url('<?php echo $slide_image;?>') no-repeat center; background-size: cover;">
                        <div class="carousel-caption">

                            <?php  if (get_sub_field('slide_text', $page_id)) { ?>
                              <div class="container">
                                <div class="row">
                                  <div class="col">
                        <?php echo $slide_text;?>
                              <?php  } ?>
                                  </div>
                                </div>
                              </div>
                            </div>

                      <?php if (get_sub_field('slide_external_video' ))  { ?>
                        <div class="overlay-div"></div>
                        <video class="video-fluid" controls="top" controlsList="nofullscreen nodownload noremoteplayback" id="player" preload="auto" playsinline muted autoplay="true" loop="true">
                            <source src="<?php echo $slide_external_video;?>"  />
                        </video>
                      <?php } else if (get_sub_field('slide_video' )) { ?>
                        <video class="video-fluid" controls="top" controlsList="nofullscreen nodownload noremoteplayback" id="player" preload="auto" playsinline muted autoplay="true" loop="true">
                            <source src="<?php echo $slide_video;?>"  />
                        </video>
                        <?php  } ?>
                    </div>
                    <?php $counter++; endwhile; ?>

                      </div> <!--/.Slides-->


                    </div> <!--Carousel Wrapper-->
        <?php endif; ?>
      </section>
<?php
}
add_action('woocommerce_before_main_content', 'loukia_front_carousel', 5);


function slide_accordion_button() { ?>

  <div class="accordion_spacer">
    <div class="row">
      <div class="col text-center">
        <hr class="devider_hero">
        <a class="icon-collapse accordion-toggle" data-toggle="collapse" href="#herocollapse" role="button" aria-expanded="false" aria-controls="herocollapse">
          <span class="text">Link with href</span>
          <span class="icon-collapse"></span>
        </a>

      </div>
    </div>
  </div>
<?php }
add_action ('woocommerce_before_main_content', 'slide_accordion_button', 5 );

function test_acf() {
  if( have_rows('carousel') ):
    while ( have_rows('carousel') ) : the_row();
        $sub_value = get_sub_field('slide_text');
        // Do something...
        echo $sub_value;
    endwhile;
else :
    // no rows found
    $sub_value = get_sub_field('slide_text');
    echo 'Nothing';
    echo '<pre>';
	var_dump( $sub_value );
echo '</pre>';
endif;

}
//add_action('woocommerce_before_main_content', 'test_acf', 2);


function product_open() { ?>
  <div class="row">
  <div class="container catalog">
    <div class="row">
<?php }
add_action( 'woocommerce_before_single_product', 'product_open', 5 );

function product_close() { ?>
</div>
  
  </div>
<?php }
add_action( 'woocommerce_after_single_product', 'product_close', 20 );

function prosilos_get_product_cat() {
  global $post;
$terms = get_the_terms( $post->ID, 'product_cat' );
$output = array();

// get attribute "technical features" for a defined product ID
$features_terms = get_the_terms($post, 'pa_technical-features');

foreach ($terms as $term){
    $product_cat_id = $term->term_id;
    $product_cat_name = $term->name;
    $product_cat_description = $term->description;
    $product_cat_url = wc_get_page_permalink( 'shop' ) . '?_product_categories=' . $term->slug;

    $parent_categories_ids = get_ancestors($product_cat_id, 'product_cat');
    foreach($parent_categories_ids as $category_id) {
   // Now we retrieve the details of each category, using its
   // ID, and extract its name
  // $this_category = get_category($cat);
   $category = get_term_by('id', $category_id, 'product_cat');
   $parent_categories[$category->slug] = $category->name;
   $parent_categories_description[$category->slug] = $category->description;
 }
    break;
} ?>
<div class="product-meta">
<?php if (get_ancestors($product_cat_id, 'product_cat') == false){
  // is Parent
  ?>
<h2 class="cat" itemprop="category"><a href="<?php echo $product_cat_url; ?>"><?php echo $product_cat_name; ?></a></h2>

 <?php } else {
   // is Child
   ?>
<h2 class="cat" itemprop="category"><a href="<?php echo $product_cat_url; ?>"><?php echo $product_cat_name; ?></a></h2>

<?php } ?>
</div>
<?php }
add_action ('prosilos_product_description', 'prosilos_get_product_cat', 25 );

function prosilos_get_product_cat_name() {
  global $product;

  // If the WC_product Object is not defined globally
  if ( ! is_a( $product, 'WC_Product' ) ) {
      $product = wc_get_product( get_the_id() );
  } ?>

  <h1 itemprop="name" class="product_title"><?php echo $product->get_name(); ?></h1>
<?php }
add_action ('prosilos_product_description', 'prosilos_get_product_cat_name', 20 );

function prosilos_get_brand() {
  global $post;
  $brands = get_the_terms( $post->ID, 'brands' ); ?>
  <div class="product-meta">
  <?php if( false != get_the_term_list( $post->ID, 'brands' ) ) {

  foreach ($brands as $brand){
      $product_brand_id = $brand->term_id;
      $product_brand_name = $brand->name;
      $product_brand_slug = $brand->slug;
      $product_brand_description = $brand->description;
      $product_brand_url = get_permalink(wc_get_page_id('shop')) . '?_brands=' . $brand->slug;
      break;
  }
  ?>

  <h2 class="brand" itemprop="category"><a href="<?php echo $product_brand_url; ?>"><?php echo $product_brand_name; ?></a></h2>

<?php }
  do_action ('prosilos_attributes');
 ?>
</div>
<?php }
add_action ('prosilos_product_description', 'prosilos_get_brand', 10 );


function bbloomer_single_product_type() {
global $product;
if( $product->is_type( 'simple' ) ){
 // do something
 add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
} elseif( $product->is_type( 'variable' ) ){
 // do something
} elseif( $product->is_type( 'external' ) ){
 // do something
} elseif( $product->is_type( 'grouped' ) ){
 // do something
}
do_action( 'woocommerce_single_product_summary');
}
add_action( 'prosilos_product_description', 'bbloomer_single_product_type', 40 );

function prosilos_footer_subscribe() {
  $page = get_posts(
    array(
        'name'      => 'subscribe',
        'post_type' => 'page'
    )
);

if ( $page && !is_page_template('template-subscribe.php'))
{



  ?>
  <section class="subscribe">
    <div class="container">
      <div class="row">
        <div class="col-12">
    <h2><?php echo $page[0]->post_title; ?></h2>
    <p><?php echo $page[0]->post_content;  ?></p>
    <?php gravity_form( 1, false, false, false, '', true, 12 ); ?>
    </div>
    </div>
  </div>
  </section>
<?php }
}

add_action( 'prosilos_footer', 'prosilos_footer_subscribe', 10 );

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

function prosilos_get_contact_info() {
   if( have_rows('contact_info') ):
     $contact_title = get_field('contact_info_title');
     ?>
<div class="contact-info">
  <h2><?php echo $contact_title; ?></h2>
    <ul>

    <?php while( have_rows('contact_info') ): the_row(); ?>

        <li>
          <img src="<?php the_sub_field('contact_icon'); ?>">
          <h3><?php the_sub_field('contact_item_title'); ?></h3>
          <p><?php the_sub_field('contact_item_text'); ?></p>
        </li>
    <?php endwhile; ?>

    </ul>
</div>
<?php endif;
if(get_field('contact_form')):
  $form_object = get_field('contact_form');
  ?>
<div class="contact-form">
  <?php gravity_form( $form_object['id'], false, false, false, '', false ); ?>
  </div>

  <?php endif;
}
add_action( 'prosilos_get_contact', 'prosilos_get_contact_info', 10 );

add_filter( 'woocommerce_product_get_price' , 'products_custom_price' , 5, 2 );
function products_custom_price( $price, $product ){
  $current_user = wp_get_current_user();
  $current_user_maestri_discount = get_field('maestri_discount', 'user_' . $current_user->ID);
  $current_user_office_point_discount = get_field('office_point_discount', 'user_' . $current_user->ID);
  $current_user_keyroad_discount = get_field('keyroad_discount', 'user_' . $current_user->ID);
  $current_user_enlegend_discount = get_field('enlegend_discount', 'user_' . $current_user->ID);

   if ( has_term( 'maestri', 'brands' )  ) {
      return $price - ($price*$current_user_maestri_discount)/100;
    } elseif ( has_term( 'office-point', 'brands' )  ) {
      return $price - ($price*$current_user_office_point_discount)/100;
    }
   elseif ( has_term( 'keyroad', 'brands' )  ) {
       return $price - ($price*$current_user_keyroad_discount)/100;
    }
    elseif ( has_term( 'enlegend', 'brands' )  ) {
        return $price - ($price*$current_user_enlegend_discount)/100;
     }
}

add_filter( 'woocommerce_product_variation_get_price' , 'variation_custom_price' , 99, 2 );
function variation_custom_price( $price, $variation ){
        //Apply Discount by matching the parent Product
       $product = wc_get_product($variation->get_parent_id());
       if( '20' == $product->get_id() ){
             return $price - $discount;
       }
       //Apply Discount by matching the Product Variation
       if( '20' == $variation->get_id() ){
             return $price - $discount;
       }
}

function dequeue_gf_stylesheets() {
    wp_dequeue_style( 'gforms_reset_css' );
    wp_dequeue_style( 'gforms_datepicker_css' );
    wp_dequeue_style( 'gforms_formsmain_css' );
    wp_dequeue_style( 'gforms_ready_class_css' );
    wp_dequeue_style( 'gforms_browsers_css' );
}
//add_action( 'gform_enqueue_scripts_1', 'dequeue_gf_stylesheets', 11 );

function roma_header_form_basic() {
 if ( is_shop() ) :

echo facetwp_display( 'facet', 'product_search' );

else : ?>

<form action="/shop/"  method="get" class="d-flex flex-row">

   <input type="search" class="form-control" placeholder="Search &hellip;" value="" name="_product_search">

       <button type="submit" class="btn btn-primary">Search</button>

</form>

<?php endif;

}
//add_action ('roma_header_form', 'roma_header_form_basic', 50 );

function roma_header_form_right_icons() { ?>
  <div id="demo" class="collapse navbar-collapse">
    <ul class="menu">
    <?php $user = wp_get_current_user();
      $allowed_roles = array( 'editor', 'administrator', 'author' );
      if ( array_intersect( $allowed_roles, $user->roles ) ) { ?>
         <li class="menu-cart">
           <a href="<?php echo wc_get_cart_url(); ?>">
             <span class="text">Cart</span>
           </a>
         </li>
      <?php } else { ?>

       <?php } ?>
     <li class="list-inline-item menu-login">
       <a href="<?php echo wc_get_page_permalink( 'myaccount' ); ?>">
         <span class="text">Login / Sign Up</span>
       </a>
     </li>
    </ul>
    <?php  if ( is_shop() ) :

    echo facetwp_display( 'facet', 'product_search' );

    else : ?>

    <form action="/shop/"  method="get" class="d-flex flex-row">
       <input type="search" class="form-control-lg inner_form" placeholder="Search &hellip;" value="" name="_product_search">
           <button type="submit" class="btn btn-primary"><span class="btn-label"><i class="facetwp-btn-inner"></i></span></button>
    </form>

  <?php endif; ?>
  </div>
<?php }
add_action ('roma_header_form', 'roma_header_form_right_icons', 40 );

function roma_header_subheader_basic() {
 if ( is_shop() || is_product() ) :

   else : ?>
          <div class="subheader">
            <div class="container">
              <div class="row">
                <div class="col">
            <?php the_roma_breadcrumb(); ?>
                </div>
              </div>
            </div>
          </div>


<?php endif;

}
add_action ('roma_header_subheader', 'roma_header_subheader_basic', 40 );
