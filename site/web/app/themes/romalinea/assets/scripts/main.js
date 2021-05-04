/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
        $('.widget_product_search').hide();
        $('.search').on(
          'click',
          function()
          {
            $('.widget_product_search, .navbar-brand').toggle();
          }
        );
          var coupon2 = $(".checkout_coupon.woocommerce-form-coupon");
          coupon2.insertAfter('.shop_table.woocommerce-checkout-review-order-table');

        $(document).on('facetwp-loaded', function() {
          var filter_header = ".accordion-toggle";
          $( filter_header ).each(function() {
            var a_href = $(this).attr('href');
            var a_href_clean = a_href.replace('#','');
            //$(this).nextAll(':has(.card-body):first').find('.card-body').addClass('test');
          //''  $(this).next().attr('id', a_href_clean);
            $(this).closest('.panel').find('.panel-collapse').attr('id', a_href_clean);
          });
        //  if($('#popup').find('p.filled-text').length !== 0)
          var panel = ".panel";
          if($(panel).find('.facetwp-facet-price').length > 0) {
            $('.facetwp-facet-price').parent().parent().parent().addClass('grey');
           }

        $('div.breadcrumb-item:contains("Page")').remove();
        $('div.breadcrumb-item:contains("Σελίδα")').remove();
        $('.facetwp-depth').hide();
        $('.facetwp-checkbox.checked').each(function() {
            $(this).parents('.facetwp-depth').show();
            $(this).next('.facetwp-depth').show();
        });

        $( 'a[href="#"]' ).click( function(e) {
           e.preventDefault();
        } );
        var qs = FWP.build_query_string();
          if ( '' === qs ) { // no facets are selected
              $('.reset-btn').hide();
          }
          else {
              $('.reset-btn').show();
          }

    });
    $('form').on( 'click', 'button.plus, button.minus', function() {

       // Get current quantity values
       var qty = $( this ).closest( 'form' ).find( '.qty' );
       var val   = parseFloat(qty.val());
       var max = parseFloat(qty.attr( 'max' ));
       var min = parseFloat(qty.attr( 'min' ));
       var step = parseFloat(qty.attr( 'step' ));

       // Change the value if plus or minus
       if ( $( this ).is( '.plus' ) ) {
          if ( max && ( max <= val ) ) {
             qty.val( max );
          } else {
             qty.val( val + step );
          }
       } else {
          if ( min && ( min >= val ) ) {
             qty.val( min );
          } else if ( val > 1 ) {
             qty.val( val - step );
          }
       }
       $( '.woocommerce-cart-form :input[name="update_cart"]' ).prop( 'disabled', false ).attr( 'aria-disabled', false );

    });

    $( document.body ).on( 'updated_cart_totals', function(){
      $('form').on( 'click', 'button.plus, button.minus', function() {

         // Get current quantity values
         var qty = $( this ).closest( 'form' ).find( '.qty' );
         var val   = parseFloat(qty.val());
         var max = parseFloat(qty.attr( 'max' ));
         var min = parseFloat(qty.attr( 'min' ));
         var step = parseFloat(qty.attr( 'step' ));

         // Change the value if plus or minus
         if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
               qty.val( max );
            } else {
               qty.val( val + step );
            }
         } else {
            if ( min && ( min >= val ) ) {
               qty.val( min );
            } else if ( val > 1 ) {
               qty.val( val - step );
            }
         }
         $( '.woocommerce-cart-form :input[name="update_cart"]' ).prop( 'disabled', false ).attr( 'aria-disabled', false );

      });

      });
              $("input[type=radio]").click(function (e) {
                $("label").removeClass("active");
                $(this).next().addClass("active");
            });


      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS

      }

    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
