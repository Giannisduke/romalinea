<?php

class FacetWP_Facet_Slider_Indicators extends FacetWP_Facet
{

    function __construct() {
        $this->label = __( 'Indicators Slider', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '<div class="facetwp-slider-wrap">';
        $output .= '<span class="facetwp-slider-label"></span>';
        $output .= '<span class="facetwp-slider-label-hidden"></span>';
        $output .= '<div class="facet-price">';
        $output .= '<div class="facetwp-slider"></div>';
        $output .= '</div>';
        $output .= '</div>';

      //  $output .= '<div><input type="button" class="facetwp-slider-reset" value="' . __( 'Reset', 'fwp-front' ) . '" /></div>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $values = $params['selected_values'];
        $where = '';

        $start = ( '' == $values[0] ) ? false : $values[0];
        $end = ( '' == $values[1] ) ? false : $values[1];

        $is_dual = ! empty( $facet['source_other'] );
        $is_intersect = FWP()->helper->facet_is( $facet, 'compare_type', 'intersect' );

        /**
         * Intersect compare
         * @link http://stackoverflow.com/a/325964
         */
        if ( $is_dual && $is_intersect ) {
            $start = ( false !== $start ) ? $start : '-999999999999';
            $end = ( false !== $end ) ? $end : '999999999999';

            $where .= " AND (facet_value + 0) <= '$end'";
            $where .= " AND (facet_display_value + 0) >= '$start'";
        }
        else {
            if ( false !== $start ) {
                $where .= " AND (facet_value + 0) >= '$start'";
            }
            if ( false !== $end ) {
                $where .= " AND (facet_display_value + 0) <= '$end'";
            }
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' $where";
        return facetwp_sql( $sql, $facet );
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $where_clause = $params['where_clause'];
        $selected_values = $params['selected_values'];

        // Set default slider values
        $defaults = [
            'format' => '',
            'prefix' => '',
            'suffix' => '',
            'step' => 1,
        ];
        $facet = array_merge( $defaults, $facet );

        $sql = "
        SELECT MIN(facet_value + 0) AS `min`, MAX(facet_display_value + 0) AS `max` FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' AND facet_display_value != '' $where_clause";
        $row = $wpdb->get_row( $sql );

        $selected_min = isset( $selected_values[0] ) ? $selected_values[0] : $row->min;
        $selected_max = isset( $selected_values[1] ) ? $selected_values[1] : $row->max;

        return [
            'range' => [
                'min' => (float) $selected_min,
                'max' => (float) $selected_max
            ],
            'decimal_separator' => FWP()->helper->get_setting( 'decimal_separator' ),
            'thousands_separator' => FWP()->helper->get_setting( 'thousands_separator' ),
            'start' => [ $row->min, $row->max ],
            'format' => $facet['format'],
            'prefix' => $facet['prefix'],
            'suffix' => $facet['suffix'],
            'step' => $facet['step']
        ];
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
      //  FWP()->display->assets['nouislider.css'] = FACETWP_URL . '/assets/vendor/noUiSlider/nouislider.css';
        FWP()->display->assets['nouislider.js'] = FACETWP_URL . '/assets/vendor/noUiSlider/nouislider.min.js';
        FWP()->display->assets['nummy.js'] = FACETWP_URL . '/assets/js/src/nummy.js';
    }


    /**
     * (Admin) Output settings HTML
     */
    function settings_html() {
        $thousands = FWP()->helper->get_setting( 'thousands_separator' );
        $decimal = FWP()->helper->get_setting( 'decimal_separator' );
?>
        <div class="facetwp-row">
            <div>
                <?php _e('Other data source', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Use a separate value for the upper limit?', 'fwp' ); ?></div>
                </div>
            </div>
            <div>
                <data-sources
                    :facet="facet"
                    :selected="facet.source_other"
                    :sources="$root.data_sources"
                    settingName="source_other">
                </data-sources>
            </div>
        </div>
        <div class="facetwp-row" v-show="facet.source_other">
            <div><?php _e('Compare type', 'fwp'); ?>:</div>
            <div>
                <select class="facet-compare-type">
                    <option value=""><?php _e( 'Basic', 'fwp' ); ?></option>
                    <option value="intersect"><?php _e( 'Intersect', 'fwp' ); ?></option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Prefix', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Text that appears before each slider value', 'fwp' ); ?></div>
                </div>
            </div>
            <div><input type="text" class="facet-prefix" /></div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Suffix', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Text that appears after each slider value', 'fwp' ); ?></div>
                </div>
            </div>
            <div><input type="text" class="facet-suffix" /></div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Format', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The number format', 'fwp' ); ?></div>
                </div>
            </div>
            <div>
                <select class="facet-format">
                    <?php if ( '' != $thousands ) : ?>
                    <option value="0,0">5<?php echo $thousands; ?>280</option>
                    <option value="0,0.0">5<?php echo $thousands; ?>280<?php echo $decimal; ?>4</option>
                    <option value="0,0.00">5<?php echo $thousands; ?>280<?php echo $decimal; ?>42</option>
                    <?php endif; ?>
                    <option value="0">5280</option>
                    <option value="0.0">5280<?php echo $decimal; ?>4</option>
                    <option value="0.00">5280<?php echo $decimal; ?>42</option>
                    <option value="0a">5k</option>
                    <option value="0.0a">5<?php echo $decimal; ?>3k</option>
                    <option value="0.00a">5<?php echo $decimal; ?>28k</option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <?php _e('Step', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The amount of increase between intervals', 'fwp' ); ?> (default = 1)</div>
                </div>
            </div>
            <div><input type="text" class="facet-step" value="1" /></div>
        </div>
<?php
    }
}
