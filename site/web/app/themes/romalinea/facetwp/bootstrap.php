<?php

class FacetWP_Facet_Bootstrap
{

    function __construct() {
        $this->label = __( 'Bootstrap', 'fwp' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $from_clause = $wpdb->prefix . 'facetwp_index f';

        // Facet in "OR" mode
        $where_clause = $this->get_where_clause( $facet );

        // Orderby
        $orderby = $this->get_orderby( $facet );

        $orderby = apply_filters( 'facetwp_facet_orderby', $orderby, $facet );
        $from_clause = apply_filters( 'facetwp_facet_from', $from_clause, $facet );
        $where_clause = apply_filters( 'facetwp_facet_where', $where_clause, $facet );

        // Limit
        $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 20;

        $sql = "
        SELECT f.facet_value, f.facet_display_value, f.term_id, f.parent_id, f.depth, COUNT(DISTINCT f.post_id) AS counter
        FROM $from_clause
        WHERE f.facet_name = '{$facet['name']}' $where_clause
        GROUP BY f.facet_value
        ORDER BY $orderby
        LIMIT $limit";

        return $wpdb->get_results( $sql, ARRAY_A );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        if ( FWP()->helper->facet_is( $facet, 'hierarchical', 'yes' ) ) {
            $values = FWP()->helper->sort_taxonomy_values( $params['values'], $facet['orderby'] );
        }

        $label = $facet['label'];
        $name = $facet['name'];
        $id = 'bootstrap_'.$name;

        $label_any = empty( $facet['label_any'] ) ? __( 'Any', 'fwp' ) : $facet['label_any'];
        $label_any = facetwp_i18n( $label_any );

        $style = $facet['style'];
        $size = $facet['size'];

        $output .= '<div class="dropdown">
                        <button class="btn btn-'.$style.' btn-'.$size.' dropdown-toggle" type="button" id="'.$id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          '.$label.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="'.$id.'">';

        if($label_any){
          $output .= '<div class="dropdown-item facetwp-link" data-value="any">' . $label_any . '</div><div class="dropdown-divider"></div>';
        }
        foreach ( $values as $result ) {
            $selected = in_array( $result['facet_value'], $selected_values ) ? ' active' : '';

            $display_value = '';
            for ( $i = 0; $i < (int) $result['depth']; $i++ ) {
                $display_value .= '&nbsp;&nbsp;';
            }

            // Determine whether to show counts
            $display_value .= esc_attr( $result['facet_display_value'] );
            $show_counts = apply_filters( 'facetwp_facet_dropdown_show_counts', true, array( 'facet' => $facet ) );

            if ( $show_counts ) {
                $display_value .= ' <span class="facetwp-counter">(' . $result['counter'] . ')</span>';
            }

            $output .= '<div class="dropdown-item facetwp-link ' . $selected . '" data-value="' . esc_attr( $result['facet_value'] ) . '">' . $display_value . '</div>';
        }

        $output .= '</div></div>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        if ( empty( $selected_values ) ) {
            return 'continue';
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' AND facet_value IN ('$selected_values')";
        return facetwp_sql( $sql, $facet );
    }

  /**
   * Parse the facet selections + other front-facing handlers
   */
  function front_scripts() {
    ?>
      <script>
        (function($) {
          wp.hooks.addAction('facetwp/refresh/bootstrap', function($this, facet_name) {
            var selected_values = [];
            $this.find('.facetwp-link.active').each(function() {
              selected_values.push($(this).attr('data-value'));
            });
            FWP.facets[facet_name] = selected_values;
          });

          wp.hooks.addFilter('facetwp/selections/bootstrap', function(output, params) {
            var choices = [];
            $.each(params.selected_values, function(idx, val) {
              var choice = params.el.find('.facetwp-link[data-value="' + val + '"]').clone();
              choice.find('.facetwp-counter').remove();
              choices.push({
                value: val,
                label: choice.text()
              });
            });
            return choices;
          });

          $(document).on('click', '.facetwp-type-bootstrap .facetwp-link:not(.disabled)', function() {
            if($(this).data('value') == 'any'){
              $(this).parent().find('.facetwp-link.active').removeClass('active');
              FWP.autoload();
            }else{
              $(this).toggleClass('active');
              FWP.autoload();
            }
          });
        })(jQuery);
      </script>
    <?php
  }

    /**
     * Output admin settings HTML
     */
    function settings_html() {
?>
        <div class="facetwp-row">
            <div>
                <?php _e( 'Default label', 'fwp' ); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content">
                        Customize the first option label (default: "Any")
                    </div>
                </div>
            </div>
            <div>
                <input type="text" class="facet-label-any" value="<?php _e( 'Any', 'fwp' ); ?>" />
            </div>
        </div>
        <div class="facetwp-row" v-show="facet.source.substr(0, 3) == 'tax'">
            <div>
                <?php _e('Parent term', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content">
                        To show only child terms, enter the parent <a href="https://facetwp.com/how-to-find-a-wordpress-terms-id/" target="_blank">term ID</a>.
                        Otherwise, leave blank.
                    </div>
                </div>
            </div>
            <div>
                <input type="text" class="facet-parent-term" />
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e('Sort by', 'fwp'); ?>:</div>
            <div>
                <select class="facet-orderby">
                    <option value="count"><?php _e( 'Highest Count', 'fwp' ); ?></option>
                    <option value="display_value"><?php _e( 'Display Value', 'fwp' ); ?></option>
                    <option value="raw_value"><?php _e( 'Raw Value', 'fwp' ); ?></option>
                    <option value="term_order"><?php _e( 'Term Order', 'fwp' ); ?></option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Hierarchical', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Is this a hierarchical taxonomy?', 'fwp' ); ?></div>
                </div>
            </div>
            <div>
                <label class="facetwp-switch">
                    <input type="checkbox" class="facet-hierarchical" true-value="yes" false-value="no" />
                    <span class="facetwp-slider"></span>
                </label>
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Count', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'fwp' ); ?></div>
                </div>
            </div>
            <div><input type="text" class="facet-count" value="20" /></div>
        </div>
        <div class="facetwp-row">
            <div><?php _e('Style', 'fwp'); ?>:</div>
            <div>
                <select class="facet-style">
                    <option value="primary"><?php _e( 'Primary', 'fwp' ); ?></option>
                    <option value="secondary"><?php _e( 'Secondary', 'fwp' ); ?></option>
                    <option value="success"><?php _e( 'Success', 'fwp' ); ?></option>
                    <option value="info"><?php _e( 'Info', 'fwp' ); ?></option>
                    <option value="warning"><?php _e( 'Warning', 'fwp' ); ?></option>
                    <option value="danger"><?php _e( 'Danger', 'fwp' ); ?></option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e('Size', 'fwp'); ?>:</div>
            <div>
                <select class="facet-size">
                    <option value="md"><?php _e( 'Default', 'fwp' ); ?></option>
                    <option value="lg"><?php _e( 'Large', 'fwp' ); ?></option>
                    <option value="sm"><?php _e( 'Small', 'fwp' ); ?></option>
                </select>
            </div>
        </div>
<?php
    }
  /**
   * Grab the orderby, as needed by several facet types
   * @since 3.0.4
   */
  function get_orderby( $facet ) {
    $key = $facet['orderby'];

    // Count (default)
    $orderby = 'counter DESC, f.facet_display_value ASC';

    // Display value
    if ( 'display_value' == $key ) {
      $orderby = 'f.facet_display_value ASC';
    }
    // Raw value
    elseif ( 'raw_value' == $key ) {
      $orderby = 'f.facet_value ASC';
    }
    // Term order
    elseif ('term_order' == $key && 'tax' == substr( $facet['source'], 0, 3 ) ) {
      $term_ids = get_terms( array(
        'taxonomy' => str_replace( 'tax/', '', $facet['source'] ),
        'fields' => 'ids',
      ) );

      if ( ! empty( $term_ids ) && ! is_wp_error( $term_ids ) ) {
        $term_ids = implode( ',', $term_ids );
        $orderby = "FIELD(f.term_id, $term_ids)";
      }
    }

    // Sort by depth just in case
    $orderby = "f.depth, $orderby";

    return $orderby;
  }


  /**
   * Adjust the $where_clause for facets in "OR" mode
   * @since 3.2.0
   */
  function get_where_clause( $facet ) {

    // Apply filtering (ignore the facet's current selections)
    if ( isset( FWP()->or_values ) && ( 1 < count( FWP()->or_values ) || ! isset( FWP()->or_values[ $facet['name'] ] ) ) ) {
      $post_ids = array();
      $or_values = FWP()->or_values; // Preserve the original
      unset( $or_values[ $facet['name'] ] );

      $counter = 0;
      foreach ( $or_values as $name => $vals ) {
        $post_ids = ( 0 == $counter ) ? $vals : array_intersect( $post_ids, $vals );
        $counter++;
      }

      // Return only applicable results
      $post_ids = array_intersect( $post_ids, FWP()->unfiltered_post_ids );
    }
    else {
      $post_ids = FWP()->unfiltered_post_ids;
    }

    $post_ids = empty( $post_ids ) ? array( 0 ) : $post_ids;
    return ' AND post_id IN (' . implode( ',', $post_ids ) . ')';
  }
}
// Add this class as a Custom Facet Type
add_filter( 'facetwp_facet_types', function( $facet_types ) {
  $facet_types['bootstrap'] = new FacetWP_Facet_Bootstrap();
  return $facet_types;
});
