<?php

/*=============================================
=            BREADCRUMBS			            =
=============================================*/
//  to include in functions.php
function the_roma_breadcrumb() {
$sep = ' <span class="sep"> &middot; </span> ';
if (!is_front_page()) {
// Start the breadcrumb with a link to your homepage
echo '<ul class="breadcrumbs">';
echo '<li><a href="';
echo get_option('home');
echo '" class="home-link">';
echo '<span class="home">';
bloginfo('name');
echo '</span>';
echo '</a></li>' . $sep;
// Check if the current page is a category, an archive or a single page. If so show the category or archive name.
if (is_category() || is_single() ){
the_category('title_li=');
        } elseif (is_archive() || is_single()){
if ( is_day() ) {
printf( __( '%s', 'text_domain' ), get_the_date() );
            } elseif ( is_month() ) {
printf( __( '%s', 'text_domain' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'text_domain' ) ) );
            } elseif ( is_year() ) {
printf( __( '%s', 'text_domain' ), get_the_date( _x( 'Y', 'yearly archives date format', 'text_domain' ) ) );
            } else {
_e( 'Blog Archives', 'text_domain' );
            }
        }
// If the current page is a single post, show its title with the separator
if (is_single()) {
echo '<li>';
the_title();
echo '</li>';
        }
// If the current page is a static page, show its title.
if (is_page()) {
echo '<li><h2>';
echo the_title();
echo '</h2></li>';
        }
// if you have a static page assigned to be you posts list page. It will find the title of the static page and display it. i.e Home >> Blog
if (is_home()){
global $post;
$page_for_posts_id = get_option('page_for_posts');
if ( $page_for_posts_id ) {
$post = get_page($page_for_posts_id);
setup_postdata($post);
the_title();
rewind_posts();
            }
        }
echo '</ul>';
    }
}
