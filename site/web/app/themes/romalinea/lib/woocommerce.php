<?php
if ( ! function_exists( 'prosilos_brands_taxonomy' ) ) {

// Register Custom Taxonomy
function prosilos_brands_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Brands', 'Taxonomy General Name', 'prosilos' ),
		'singular_name'              => _x( 'Brand', 'Taxonomy Singular Name', 'prosilos' ),
		'menu_name'                  => __( 'Brands', 'prosilos' ),
		'all_items'                  => __( 'All Brands', 'prosilos' ),
		'parent_item'                => __( 'Parent Brand', 'prosilos' ),
		'parent_item_colon'          => __( 'Parent Brands:', 'prosilos' ),
		'new_item_name'              => __( 'New Brand Name', 'prosilos' ),
		'add_new_item'               => __( 'Add New Brand', 'prosilos' ),
		'edit_item'                  => __( 'Edit Brand', 'prosilos' ),
		'update_item'                => __( 'Update Brand', 'prosilos' ),
		'view_item'                  => __( 'View Brand', 'prosilos' ),
		'separate_items_with_commas' => __( 'Separate Brands with commas', 'prosilos' ),
		'add_or_remove_items'        => __( 'Add or remove Brands', 'prosilos' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'prosilos' ),
		'popular_items'              => __( 'Popular Brands', 'prosilos' ),
		'search_items'               => __( 'Search Brands', 'prosilos' ),
		'not_found'                  => __( 'Not Found', 'prosilos' ),
		'no_terms'                   => __( 'No items', 'prosilos' ),
		'items_list'                 => __( 'Brands list', 'prosilos' ),
		'items_list_navigation'      => __( 'Brands list navigation', 'prosilos' ),
	);
	$capabilities = array(
    'manage_terms' => 'manage_product_terms',
    'edit_terms'   => 'edit_product_terms',
    'delete_terms' => 'delete_product_terms',
    'assign_terms' => 'assign_product_terms',
	);
	$args = array(
		'labels'                     => $labels,
    'has_archive'                => true,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'capabilities'               => $capabilities,
		'show_in_rest'               => true,
    'rewrite'           => ['slug' => 'brands'],
		'supports' => array( 'thumbnail' ),
    'query_var'             => true,
	);
	register_taxonomy( 'brands', array( 'product' ), $args );
  register_taxonomy_for_object_type( 'brands', 'product' );
}
add_action( 'woocommerce_register_taxonomy', 'prosilos_brands_taxonomy', 0 );

}

function my_product_carousel_options($options) {
  $options['animation'] = 'fade';
	$options['slideshow'] = 'true';
	$options['animationLoop'] = 'true';
  return $options;
}
add_filter("woocommerce_single_product_carousel_options", "my_product_carousel_options", 10);

add_action( 'wp_enqueue_scripts', 'wsis_dequeue_stylesandscripts_select2', 100 );

function wsis_dequeue_stylesandscripts_select2() {
    if ( class_exists( 'woocommerce' ) ) {
        wp_dequeue_style( 'select2' );
        wp_deregister_style( 'select2' );

      //  wp_dequeue_script( 'selectWoo');
      //  wp_deregister_script('selectWoo');
    }
}

/**
 * Remove shop from breadcrumb
 */
add_filter( 'woocommerce_get_breadcrumb', 'remove_shop_crumb', 20, 2 );
function remove_shop_crumb( $crumbs, $breadcrumb ){
    foreach( $crumbs as $key => $crumb ){
        if( $crumb[0] === __('Shop', 'Woocommerce') ) {
            unset($crumbs[$key]);
        }
    }

    return $crumbs;
}

/**
 * Remove product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

//    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
//    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;
}

// Display Price For Variable Product With Same Variations Prices
add_filter('woocommerce_available_variation', function ($value, $object = null, $variation = null) {
  if ($value['price_html'] == '<span class="price">' . $variation->get_price_html() . '</span>') {
     $value['price_html'] = $variation->get_price_html();
  }
  return $value;
}, 10, 3);


function add_percentage_to_sale_badge( $html, $post, $product ) {
    if( $product->is_type('variable')){
        $percentages = array();

        // Get all variation prices
        $prices = $product->get_variation_prices();

        // Loop through variation prices
        foreach( $prices['price'] as $key => $price ){
            // Only on sale variations
            if( $prices['regular_price'][$key] !== $price ){
                // Calculate and set in the array the percentage for each variation on sale
                $percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
								//$percentages[] = ( floatval( $prices['regular_price'][ $key ] ) - floatval( $price ) ) / floatval( $prices['regular_price'][ $key ] ) * 100;
            }
        }
        // We keep the highest value
        $percentage = max($percentages) . '%';
    } else {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();

        $percentage    = round(100 - ($sale_price / $regular_price * 100)) . '%';
    }

    return '<span class="badge badge-pill badge-primary">' . esc_html__( 'Sale', 'prosilos' ) . ' ' . $percentage . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'add_percentage_to_sale_badge', 20, 3 );

/** WooCommerce Sales percent in Product page **/
function woocommerce_custom_sales_price( $price, $regular_price, $sale_price ) {
    $percentage = round( ( $regular_price - $sale_price ) / $regular_price * 100 ).'%';
    $percentage_txt = '<span class="percentage">' . __(' - ', 'woocommerce' ) . $percentage . '</span>';
    $price = '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $regular_price ) . '</ins><del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>' . $percentage_txt;
    return $price;
}
add_filter( 'woocommerce_format_sale_price', 'woocommerce_custom_sales_price', 10, 3 );

/** WooCommerce default variable price filter **/
add_filter('woocommerce_show_variation_price',      function() { return TRUE;});

function my_wc_hide_in_stock_message( $html, $product ) {
	if ( $product->is_in_stock() ) {
		return '';
	}

	return $html;
}

/**
 * Show cart contents / total Ajax
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start();

	?>

	<li class="menu-cart">
		<a href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
			<span>
				<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?>
					<?php echo $woocommerce->cart->get_cart_total(); ?><span class="currensy"><?php echo get_woocommerce_currency_symbol(); ?></span>
			</span>
			</br>

		</a>

</li>
	<?php
	$fragments['li.menu-cart'] = ob_get_clean();
	return $fragments;
}

/**
 * @snippet       Plus Minus Quantity Buttons @ WooCommerce Single Product Page
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.8
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

// -------------
// 1. Show Buttons

add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_plus' );

function bbloomer_display_quantity_plus() {
  echo '<button type="button" class="btn btn-transparent minus" >-</button>';
}

add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_minus' );

function bbloomer_display_quantity_minus() {
   echo '<button type="button" class="btn btn-transparent plus" >+</button>';
}

// Note: to place minus @ left and plus @ right replace above add_actions with:
// add_action( 'woocommerce_before_add_to_cart_quantity', 'bbloomer_display_quantity_minus' );
// add_action( 'woocommerce_after_add_to_cart_quantity', 'bbloomer_display_quantity_plus' );

// -------------
// 2. Trigger jQuery script

add_action( 'wp_footer', 'bbloomer_add_cart_quantity_plus_minus' );


// To change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' );
function woocommerce_custom_single_add_to_cart_text() {
    return __( 'Buy', 'prosilos' );
}

// To change add to cart text on product archives(Collection) page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );
function woocommerce_custom_product_add_to_cart_text() {
    return __( 'Buy Now', 'prosilos' );
}

function bbloomer_add_cart_quantity_plus_minus() {
   // Only run this on the single product page
   if ( ! is_product() ) return;
   ?>

   <?php
}

add_filter( 'woocommerce_get_stock_html', 'my_wc_hide_in_stock_message', 10, 2 );

function wc_get_suctom_star_rating_html( $rating, $count = 0 ) {
	$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';

	if ( 0 < $count ) {
		/* translators: 1: rating 2: rating count */
		$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'woocommerce' ), '<strong itemprop="ratingValue" class="rating test">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
	} else {
		/* translators: %s: rating */
		$html .= sprintf( esc_html__( 'Rated %s out of 5', 'prosilos' ), '<strong itemprop="ratingValue" class="rating test">' . esc_html( $rating ) . '</strong>' );
	}

	$html .= '</span>';

	return apply_filters( 'wc_get_suctom_star_rating_html', $html, $rating, $count );
}

// always display rating stars
function filter_woocommerce_product_get_rating_html( $rating_html, $rating, $count ) {
    $rating_html  = '<div class="star-rating">';
    $rating_html .= wc_get_suctom_star_rating_html( $rating, $count );
    $rating_html .= '</div>';

    return $rating_html;
};
add_filter( 'woocommerce_product_get_rating_html', 'filter_woocommerce_product_get_rating_html', 10, 3 );



remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action ('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action ('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action ('woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function roma_related_products() {
	global $product;

if( ! is_a( $product, 'WC_Product' ) ){
    $product = wc_get_product(get_the_id());
}

woocommerce_related_products( array(
    'posts_per_page' => 4,
    'columns'        => 4,
    'orderby'        => 'rand'
) );
}
add_action ('woocommerce_after_single_product_summary', 'roma_related_products', 19 );

function prosilos_pagination() {

echo facetwp_display( 'facet', 'product_pager' );
}
add_action ('woocommerce_after_shop_loop', 'prosilos_pagination', 20 );




 function simple_product_price_open() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
  <div class="row">
  <?php } elseif( $product->is_type( 'variable' ) ){
   // do something
  } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_price_open', 23 );

function simple_product_price_close() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
 </div>
  <?php } elseif( $product->is_type( 'variable' ) ){
   // do something
  } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_price_close', 32 );

function simple_product_hr() { ?>
<hr class="mt-2 mb-3"/>
<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_hr', 33 );

function simple_product_meta() {
	// get an array of the WP_Term objects for a defined product ID
	$tags = wp_get_post_terms( get_the_id(), 'product_tag' );
	if( count($tags) > 0 ){
	    foreach($tags as $term){
	        $term_id = $term->term_id; // Product tag Id
	        $term_name = $term->name; // Product tag Name
	        $term_slug = $term->slug; // Product tag slug
	        $term_link = get_term_link( $term, 'product_tag' ); // Product tag link

	        // Set the product tag names in an array
	        $output[] = '<h2 class="tag" itemprop="category"><a href="'.$term_link.'">'.$term_name.'</a></h2>';
	    }
	    // Set the array in a coma separated string of product tags for example
	    $output = implode( '   ', $output );
			echo '<div class="product-meta"><span class="tags">' . $output . '</span>';
	  }

	?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_meta', 34 );

function simple_product_media() {

	$image_url = get_the_post_thumbnail_url();
	echo '<span class="file_photo"><a href="'.$image_url.'">Φωτογραφίες</a></span></div></div>';
}
add_action ('woocommerce_single_product_summary', 'simple_product_media', 35 );

function simple_product_files() { ?>
	<?php if( get_field('help_files') ): ?>
		<div class="product-files"><span class="file_files"><a href="<?php the_field('help_files'); ?>">Βοηθητικά Αρχεία</a></span></div>
	<?php endif; ?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_files', 36 );

function prosilos_attributes_verifivation() {
  global $post;
$protypo_terms = get_the_terms($post, 'pa_πρότυπο');

if( ! empty( $protypo_terms ) ){ ?>
<span class="verification">
<?php foreach ($protypo_terms as $term) :

		$product_protypo_url = get_permalink(wc_get_page_id('shop')) . '?_eu_prototype=' . $term->slug;
		$protypo_name = $term->name;
		echo '<h2 class="iso"><a href="' . $product_protypo_url . '">' . $protypo_name . '</a></h2>';

endforeach;
} else {
    // No product attribute is set for this product
}?>
</span>
<?php }
//add_action ('prosilos_attributes', 'prosilos_attributes_verifivation', 10 );

function prosilos_attributes_protection() {
  global $post;
$ppe_terms = get_the_terms($post, 'pa_ppe-category');

if( ! empty( $ppe_terms ) ){ ?>
<span class="protection">
<?php foreach ($ppe_terms as $ppe_term) :

		$product_ppe_url = get_permalink(wc_get_page_id('shop')) . '?_ppe_protection=' . $ppe_term->slug;
		$ppe_name = $ppe_term->name;
		echo '<a href="' . $product_ppe_url . '" class="ppe level-' . $ppe_name . '"> </a>';

endforeach;
} else {
    // No product attribute is set for this product
}?>
</span>
<?php }
//add_action ('prosilos_attributes', 'prosilos_attributes_protection', 20 );



function simple_product_price_inner_open() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
  <div class="col-6 simple_price">
  <?php } elseif( $product->is_type( 'variable' ) ){
   // do something
  } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_price_inner_open', 24 );

function simple_product_price_inner_close() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
 </div>
  <?php } elseif( $product->is_type( 'variable' ) ){
   // do something
  } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'simple_product_price_inner_close', 26 );

function woocommerce_template_single_add_to_cart_open() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
  <div class="col-6">
  <?php } elseif( $product->is_type( 'variable' ) ){
   // do something ?>
   <div class="col-12 p-0">
	<?php } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart_open', 29 );

function woocommerce_template_single_add_to_cart_close() {
  global $product;
  if( $product->is_type( 'simple' ) ){
   // do something ?>
 </div>
  <?php } elseif( $product->is_type( 'variable' ) ){
    // do something ?>
  </div>
   <?php
  } elseif( $product->is_type( 'external' ) ){
   // do something
  } elseif( $product->is_type( 'grouped' ) ){
   // do something
  }
  ?>

<?php }
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart_close', 31 );

add_filter( 'woocommerce_get_image_size_single', function( $size ) {
	return array(
		'width'  => 578,
		'height' => 578,
		'crop'   => 1,
	);
} );

add_filter( 'woocommerce_get_image_size_thumbnail', function( $size ) {
return array(
'width'  => 375,
'height' => 300,
'crop'   => 1,
	);
} );


remove_action ('woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
add_action ('woocommerce_review_meta', 'woocommerce_review_display_gravatar', 9 );
remove_action ('woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );
remove_action ('woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
function woocommerce_review_display_gravatar_before() { ?>
	<div class="rating-review-info">
<?php }
add_action ('woocommerce_review_meta', 'woocommerce_review_display_gravatar_before', 8 );


add_action ('woocommerce_review_meta', 'woocommerce_review_display_rating', 11 );

function woocommerce_review_display_gravatar_after() { ?>
</div>
<?php }
add_action ('woocommerce_review_meta', 'woocommerce_review_display_gravatar_after', 12 );

	/**
	 * Display the review content.
	 */
function prosilos_review_display_comment_text() { ?>
		<div class="rating-review-content">
		<div class="row">
		<div class="review-comment-content">
		<?php echo comment_text(); ?>
		</div>
		<div class="review-comment-date">
			<time class="woocommerce-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo esc_html( get_comment_date( wc_date_format() ) ); ?></time>
		</div>
		</div>
		</div>
<?php }
add_action ('woocommerce_review_comment_text', 'prosilos_review_display_comment_text', 10 );


remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
add_action( 'woocommerce_cart_is_empty', 'custom_empty_cart_message', 10 );

function custom_empty_cart_message() {
    $html  = '<h2>';
    $html .= wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your cart is currently empty.', 'woocommerce' ) ) );
    echo $html . '</h2>';
}

add_filter( 'woocommerce_checkout_fields', 'misha_email_first' );

function misha_email_first( $checkout_fields ) {
	$checkout_fields['billing']['billing_email']['priority'] = 31;
	$checkout_fields['billing']['billing_phone']['priority'] = 32;
	$checkout_fields['billing']['billing_state']['priority'] = 41;
	$checkout_fields['billing']['billing_city']['priority'] = 42;
	$checkout_fields['billing']['billing_postcode']['priority'] = 43;
	return $checkout_fields;
}


add_filter('woocommerce_form_field_country', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_state', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_textarea', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_checkbox', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_password', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_text', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_email', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_tel', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_number', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_select', 'clean_checkout_fields_class_attribute_values', 20, 4);
add_filter('woocommerce_form_field_radio', 'clean_checkout_fields_class_attribute_values', 20, 4);
function clean_checkout_fields_class_attribute_values( $field, $key, $args, $value ){
    if( is_checkout() ){
        // remove "form-row"
        $field = str_replace( array('<p class="form-row ', '<p class="form-row'), array('<div class="', '<p class="'), $field);
				$field = str_replace( array('</p>'), array('</div>'), $field);
				$field = str_replace( array('<div class="form-row-first validate-required"'), array('<div class="form-row"><div class="form-group col-md-6 validate-required"'), $field);
				$field = str_replace( array('<div class="form-row-last validate-required"'), array('<div class="form-group col-md-6 validate-required"'), $field);
				$field = str_replace( array('<div class="form-row-wide" id="billing_company_field" data-priority="30">'), array('<div class="form-row-wide form-group col-md-4" id="billing_company_field" data-priority="30">'), $field);
				//$field = str_replace( array('<div class="form-row-wide address-field'), array('</div><div class="form-row-wide address-field'), $field);
				//$field = str_replace( array('<div class="form-row-wide address-field'), array('</div><div class="form-row"><div class="form-row-wide address-field'), $field);

				$field = str_replace( array('<span class="woocommerce-input-wrapper">', '</span>'), array(' ', ' '), $field);
				$field = str_replace( array('class="input-text "'), array('class="input-text form-control"'), $field);
				$field = str_replace( array('<div class="form-row-wide validate-required validate-email" id="billing_email_field" data-priority="31">'), array('<div class="form-row-wide form-group col-md-4 validate-required validate-email" id="billing_email_field" data-priority="31">'), $field);
				$field = str_replace( array('<div class="form-row-wide validate-required validate-phone" id="billing_phone_field" data-priority="32">'), array('<div class="form-row-wide form-group col-md-4 validate-required validate-phone" id="billing_phone_field" data-priority="32">'), $field);
				$field = str_replace( array('<div class="form-row-wide address-field update_totals_on_change validate-required"'), array('</div><div class="form-row">
    <div class="col"><hr></div>

</div><div class="form-row"><div class="form-row-wide form-group col-md-4 address-field update_totals_on_change validate-required"'), $field);
				//$field = str_replace( array('<div class="address-field validate-required form-row-wide" id="billing_address_1_field" data-priority="50">'), array('<div class="address-field validate-required form-row-wide form-group col-md-4" id="billing_address_1_field" data-priority="50">'), $field);
		//		$field = str_replace( array('<div class="address-field validate-required form-row-wide" id="billing_city_field" data-priority="42"'), array('<div class="address-field validate-required form-row-wide form-group col-md-3" id="billing_city_field" data-priority="42"'), $field);
		//		$field = str_replace( array('<div class="address-field validate-required form-row-wide" id="billing_address_1_field" data-priority="50">'), array('<div class="address-field validate-required form-row-wide form-group col-md-3" id="billing_address_1_field" data-priority="50">'), $field);
	} elseif (is_account_page() ) {
		$field = str_replace( array('<p class="form-row ', '<p class="form-row'), array('<div class="', '<p class="'), $field);
		$field = str_replace( array('</p>'), array('</div>'), $field);
		$field = str_replace( array('class="input-text "'), array('class="input-text form-control"'), $field);
		$field = str_replace( array('<div class="form-row-first validate-required"'), array('<div class="form-row"><div class="form-group col-md-4 validate-required"'), $field);
		$field = str_replace( array('<div class="form-row-last validate-required"'), array('<div class="form-group col-md-4 validate-required"'), $field);
		$field = str_replace( array('<div class="col-md-4 validate-required" id="billing_country_field" data-priority="40">'), array('</div><div class="form-row"><div class="col-md-4 validate-required" id="billing_country_field" data-priority="40">'), $field);


	}

    return $field;
}


add_filter(  'woocommerce_billing_fields', 'custom_billing_fields', 20, 1 );
function custom_billing_fields( $fields ) {
    // Only on account pages
    if( ! is_account_page() ) return $fields;

    ## ---- 2.  Sort billing email and phone fields ---- ##
		$fields = str_replace( array('class="input-text "'), array('class="input-text form-control"'), $fields);
    $fields['billing_email']['priority'] = 31;
    $fields['billing_email']['class'] = array('form-group col-md-4');
    $fields['billing_phone']['priority'] = 32;
    $fields['billing_phone']['class'] = array('form-group col-md-4');
		$fields['billing_company']['class'] = array('form-group col-md-4');
		$fields['billing_country']['class'] = array('col-md-4');
		$fields['billing_address_1']['class'] = array('form-group col-md-4');
		$fields['billing_address_2']['class'] = array('form-group col-md-4');
		$fields['billing_city']['class'] = array('form-group col-md-4');
		$fields['billing_state']['class'] = array('form-group col-md-4');

    return $fields;
}

function output_payment_button() {
    $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'prosilos' ) );
		echo '<div class="buy_button_wrapper">';
    echo '<input type="submit" class="btn btn-primary btn-lg btn-block alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />';
		echo '</div>';
}

add_action( 'woocommerce_after_checkout_form', 'output_payment_button' );

function remove_woocommerce_order_button_html() {
    return '';
}

add_filter( 'woocommerce_order_button_html', 'remove_woocommerce_order_button_html' );

function eboy_product_availability() {
	global $product;
	$koostis = $product->get_attribute( 'pa_διαθεσιμότητα' );
	echo '<span class="pa">' . $koostis . '</span>';
	}
add_action ('woocommerce_after_add_to_cart_button', 'eboy_product_availability', 20 );


function total_product_count() {
	$args = array( 'post_type' => 'product', 'post_status' => 'publish',
	'posts_per_page' => -1 );
	$products = new WP_Query( $args );
	echo $products->found_posts;
	echo esc_html( 'Προϊόντα' );
}
add_action ('roma_sidebar_header', 'total_product_count', 10 );

function woocommerce_before_notifications() { ?>
	<div class="col text-center">
	<h2><?php wp_title(''); ?></h2>
</div>
<?php }
//add_action ('woocommerce_before_cart', 'woocommerce_before_notifications', 5 );
