<?php
GFForms::include_addon_framework();

class StylesPro extends GFAddOn {

    protected $_version = GF_STYLES_PRO_ADDON_VERSION;
    protected $_min_gravityforms_version = '2.5-rc3';
    protected $_slug = 'gf_stylespro';
    protected $_path = 'gfstylespro/gfstylespro.php'; 
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Styles Pro';
    protected $_short_title = 'Styles Pro';

    private static $_instance = null;

    private $_iconsets = array();

    private $_theme;

    private $_support_url = "https://gravitystylespro.com/docs/";

    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new StylesPro();
        }

        return self::$_instance;
    }

    /**
    * Members plugin integration
    */
    protected $_capabilities = array(
        'gravityforms_stylespro',
        'gravityforms_stylespro_uninstall',
        'gravityforms_stylespro_settings',
    );

    /**
    * Permissions
    */
    protected $_capabilities_settings_page = 'gravityforms_stylespro_settings';
    protected $_capabilities_form_settings =  'gravityforms_stylespro';
    protected $_capabilities_plugin_page = 'gravityforms_stylespro';
    protected $_capabilities_uninstall = 'gravityforms_stylespro_uninstall';

    
    public function pre_init() {
        parent::pre_init();
        // Tasks or filters to perform during the class constructor - before WordPress has been completely initialized
    }

    public function init() {
        parent::init();
        // Tasks or filters to perform both in the backend and frontend and for ajax requests

        // Exit to avoid Fatal Error if minimum version requirements not met
        if ( version_compare( GFCommon::$version, $this->_min_gravityforms_version, '<' ) ) {
            return;
        }

        /*
         * Front-end actions and filters
         */
        add_action( 'gform_enqueue_scripts', array($this, 'gf_stylespro_enq') );
        add_filter( 'gform_pre_render', array($this, 'gf_stylespro_add'), 11 );
        // add_filter( 'gform_pre_render', array($this, 'gf_stylespro_add_ornaments_icons') );

        /**
         * Add init scripts on form render
         */
        add_action( 'gform_register_init_scripts', array($this, 'gf_stylespro_init_scripts') );

        // TODO: Check if needed; probably not
        add_action( 'gform_pre_enqueue_scripts', array($this, 'action_gform_pre_enqueue_scripts'), 10, 2 );
        add_action( 'wp_enqueue_scripts', array($this, 'gf_stylespro_dusting'), 999 );

    }



    public function init_admin() {
        parent::init_admin();
        // Tasks or filters to perform only in admin
        // TODO: Remove
        // add_filter( 'gform_form_settings', array( $this, 'gf_stylespro_theme_select' ), 10, 2 );

        // TODO: Remove
        // add_filter( 'gform_pre_form_settings_save', array( $this, 'save_gf_stylespro_form_settings' ) );

        // Ornaments for choices fields
        add_action( 'gform_field_standard_settings', array( $this, 'gf_sylespro_list_ornaments_settings' ), 10, 2 );

        add_action( 'admin_enqueue_scripts', array($this, 'maybe_remove_editor_script') );
        
        add_action( 'gform_field_appearance_settings', array( $this, 'gf_stylespro_appearance_settings' ), 10, 2 );
        add_action( 'gform_field_appearance_settings', array( $this, 'gf_stylespro_appearance_settings_icon'), 10, 2 );
        
        // TODO: Only for Legacy forms
        add_filter( 'gform_field_size_choices', array( $this, 'gf_stylespro_add_field_size_option'), 10 );

        // NEW:
        add_action( 'gform_settings_gf_stylespro', array( $this, 'settings_page_content' ), 10, 2 );
        // Add Ornaments markup
        add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'gf_sylespro_list_ornaments_markup' ), 500, 4 );

        // Register scripts and scripts for no-conflict mode
        // add_filter( 'gform_noconflict_styles', 'register_style' );
        // add_filter( 'gform_noconflict_scripts', 'register_script' );

        // Register scripts and scripts for no-conflict mode
        add_filter( 'gform_noconflict_styles', function( $styles ) {
            // Add WP media manager styles
            array_push( $styles,
                'media-views',
                'imgareaselect'
            );
            return $styles;

        } );
        
        add_filter( 'gform_noconflict_scripts', function( $scripts ) {
            // Add WP media manager scripts
            array_push ( $scripts, 
                'media-editor',
                'media-views',
                'media-audiovideo',
                'mce-view',
                'image-edit'
            );
            return $scripts;

        } );


        // Enque WP Media Library
        add_action( 'admin_enqueue_scripts', 'wp_enqueue_media');
        
        // Saperately load WP Color Picker to avoid conflicts
        add_action( 'admin_enqueue_scripts', function() {
            wp_enqueue_script( 'wp-color-picker' );
        } );

        // Cleans up unnecessary fields
        add_filter( 'pre_update_option_gravityformsaddon_' . $this->_slug . '_settings', array($this, 'pre_save_clean_empty_options'), 10, 2);
    }
    



    /**
     * Creates a custom page for Styles Pro add-on
     */
    public function plugin_page() {
        ?>
        <div class="gforms_box"><h3>Go to</h3>
            <ul style="list-style-type:disc; margin-top: 0em; margin-left: 2em; font-size: 1.2em;">
                <li><a class="stylespro_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=gf_settings&subview=gf_stylespro' ) ) ?>"><strong>Styles Pro Settings:</strong> Customize themes</a></li>
            </ul>
        </div>
        <div class="gforms_helpbox gforms_box"><h3>Help</h3>
            <ul class="resource_list" style="list-style-type:disc; margin-top: 0em; margin-left: 1em;">
                <li><a href="https://gravitystylespro.com/docs/" target="_blank">Using Gravity Forms Styles Pro</a></li>
                <li><a href="https://gravitystylespro.com/faq/" target="_blank">FAQ</a></li>
                <li><a href="https://gravitystylespro.com/ideas/" target="_blank">Have some ideas about new features or improvements? Share with us, we're all ears!</a></li>
                <li><a href="https://gravitystylespro.com/support/" target="_blank" style="color: #38bc31;">Get support</a></li>
            </ul>
        </div>
        <style>
            .gforms_box li a {            
                display: block;
                width: 270px;
                padding: 10px 10px;
                text-decoration: none;
                background: white;
                color: #36a5e0;
                border: 1px solid #c5d8e6;
                box-shadow: 0 9px 30px -18px;
                border-radius: 3px;
            }
            .gforms_box li { list-style: none; }
            .gforms_box li a.stylespro_btn {
                width: 338px;
                text-align: center;
                border: 1px solid #c5d8e6;
                box-shadow: 0 9px 30px -18px;
                border-radius: 3px;
            }
            .gforms_box a.stylespro_btn:active { box-shadow: none; border: 2px solid; }
            a.stylespro_btn:hover, .gforms_box a:hover {background: #f2fbff;}

        </style>
        <?php
    }

    public function init_frontend() {

        parent::init_frontend();

        add_filter( 'gfpdf_template_args', array($this, 'stylespro_gfpdf_add_field_css_classes'), 10, 4 );

        /* Handling Graviy Flow Edit Forms */
        add_action( 'gravityflow_enqueue_frontend_scripts', array($this , 'maybe_gravityflow_unlink_styles_pro') );

        // Prevent Jetpack lazyload from being added to Label images 
        // On Non-AJAX forms, the Product Option field images are not shown when Jetpack is enabled
        // TODO: Check if the issues are resolved with newer version of Jetpack
        add_filter( 'jetpack_lazy_images_blacklisted_classes', array($this, 'jetpack_lazyload_exclude_label_img_class'), 10, 1 );

    }



    function action_gform_pre_enqueue_scripts( $form, $is_ajax ){

        // We might need to enqueue default GF styling with Gravity Flow
        if ( class_exists('Gravity_Flow')) {
            return;
        }
        
        // $theme = self::get_styles_pro_theme($form);

        // if (!empty($theme) && $theme != "none") {
        //     add_filter( 'pre_option_rg_gforms_disable_css', function() { return true; } );
        // }
    }

    /*
    * Front End
    * Add Styles Pro classes to the field container li
    */
    function gf_stylespro_add_css_classes($classes, $field, $form) {
        if( $field["gfStylesPro"] ) {
            $classes .= " " . $field["gfStylesPro"];
        }

        return $classes;
    }

    /**
    * Clean up extra styles by some themes
    */
    function gf_stylespro_dusting() {
        wp_dequeue_style( 'x-gravity-forms' );
        wp_dequeue_style( 'us-gravityforms' );
        wp_dequeue_style( 'woo-gravity-forms' );
        wp_dequeue_style( 'avia-gravity' );
        wp_dequeue_style( 'astra-gravity-forms' );
        
        if ( class_exists( 'X_Bootstrap' ) ) {
            wp_dequeue_style( 'x-gravity-forms' );
            remove_action( 'gform_enqueue_scripts', 'x_gravity_forms_enqueue_styles', 10 );
        }
    }

    /**
    * Configures the settings which should be rendered on the Form Settings > Styles Pro tab.
    *
    * @return array
    */
    public function form_settings_fields( $form ) {

        if ( !class_exists('GFSPCommon') ) {
            require_once( plugin_dir_path( __FILE__ ) . 'common.php' );
        }

        $gfsp_all_themes = GFSPCommon::get_themes();

        $themes_data = array(
            'label' => 'Theme Name',
            'value' => 'Theme Slug',
            'desc' => 'Description',
            'scripts' => 'Features',
        );

        $desc_str = '';        
        
        foreach ( $gfsp_all_themes as $gfsp_themes  ) {
            
            // Make sure the file returns values in headers to exclude minified files
            if ( $gfsp_themes['value'] != '' ) {
                $options[$gfsp_themes['value']] = $gfsp_themes['label'];
                if ( $gfsp_themes['desc'] != '' ) {
                    
                    $has_scripts_str = '';
                    if ( $gfsp_themes['scripts'] != '' ) 
                        $has_scripts_str = '<br><i style="font-size: .9em">(Contains scripts for advanced features that can be enabled from Customization options)</i>';
                    
                    $desc_str .= '<p id="desc_'.$gfsp_themes['value'].'">' . $gfsp_themes['desc'] . $has_scripts_str . '</p>';
                }

            }
        }

        // Sort theme list
        asort($options);

        // Get currently selected theme form settings (v1)
        $sel_form = rgar( $form, 'gf_stylespro_theme' );

        $theme_choices = array(
            array(
                'label' => 'Default Theme',
                'value' => '',
            ),
            array(
                'label' => 'No theme',
                'value' => 'none',
            )
        );
    
        foreach ( $options as $value => $name ) {
            array_push($theme_choices, array(
                'label' => htmlentities($name),
                'value' => htmlentities($value),
                ));
        }


        $gfsp_plugin_settings = $this->get_plugin_settings();
        
        $default_theme = '';

        if ( is_array($gfsp_plugin_settings) && array_key_exists('default_theme', $gfsp_plugin_settings) && $gfsp_plugin_settings['default_theme'] != '' )
            $default_theme = $gfsp_plugin_settings['default_theme'];

        $description_warning  = "";
        if  ( !is_array($gfsp_plugin_settings) || empty($gfsp_plugin_settings) )
            $description_warning = "<div class='notice alert warning is-dismissible'><p><strong>Important! </strong>Default fonts for some themes might not work as expected until settings are saved once. Please go to <a href='" . esc_url( admin_url( 'admin.php?page=gf_settings&subview=gf_stylespro' ) ) . "'>Styles Pro Settings</a> and press the <strong>Update All Settings</strong> button to complete the setup.</p></div>";

        $settings = array(
            array(
                'title'  => esc_html__( 'Styles Pro Theme', 'gf_stylespro' ),
                'id'=> "theme_select",
                'description' => $description_warning,
                'fields' => array(
                    array(
                        'label'   => esc_html__( 'Theme', 'gf_stylespro' ),
                        'type'    => 'select',
                        'name'    => 'theme',
                        'default_value'=> $sel_form,
                        'default_theme' => $default_theme,
                        'tooltip' => esc_html__( '<h6>Styles Pro Theme</h6>This setting will apply the selected visual theme for this form. Choose <i>No theme</i> to use default Gravity Forms styles.<br>Multiple forms with different themes can be used on the same page, except when <i>No theme</i> or <i>*Inherit Theme Styles</i> are loaded with other themes.', 'gf_stylespro' ),
                        'after_select' =>  '<a class="button edit_theme" href="' . esc_url( admin_url( 'admin.php?page=gf_settings&subview=gf_stylespro' ) ) . '">Edit themes <i>in Styles Pro Settings</i></a><div class="themes_descriptions alert">'.$desc_str.'</div>',
                        'choices' => $theme_choices,
                    ),
                )
            ),
            array(
                'title'  => esc_html__( 'Popup Validation Message', 'gf_stylespro' ),
                'id'=> "validation_messages",
                'fields' => array(
                    array(
                        'label'   => esc_html__( 'Enable', 'gf_stylespro' ),
                        'type'    => 'toggle',
                        'name'    => 'v_popup',
                        'onclick' => "if(this.checked){jQuery('#gform_setting_v_message').show();} else{jQuery('#gform_setting_v_message').hide();}",
                        'tooltip' => esc_html__( 'This setting will place the validation message in a CSS popup, that can be closed by clicking/tapping anywhere on the page.', 'gf_stylespro' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Show validation error in a CSS popup', 'gf_stylespro' ),
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'Popup message', 'gf_stylespro' ),
                        'type'    => 'textarea',
                        'placeholder' => 'There was a problem with your submission. Errors have been highlighted below.',
                        'name'    => 'v_message',
                        'tooltip' => esc_html__( 'Enter a new validation message here. HTML is allowed.', 'gf_stylespro' ),
                        'class'   => 'medium',
                    ),  
                    array(
                        'label'   => esc_html__( 'Auto-scroll to first error', 'gf_stylespro' ),
                        'type'    => 'toggle',
                        'name'    => 'v_scroll',
                        'tooltip' => esc_html__( 'This setting will scroll the page to the first field with errors that need to be changed. This makes for a great User Experience, specially in conjunction with popup message.', 'gf_stylespro' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Auto-scroll to the first error field', 'gf_stylespro' ),
                            ),
                        ),
                    ),
                )
            ),
            array(
                'title'  => esc_html__( 'Icons', 'gf_stylespro' ),
                'id'=> "icons",
                'fields' => array(
                    array(
                        'label'   => esc_html__( 'Enable icon set', 'gf_stylespro' ),
                        'type'    => 'checkbox',
                        'name'    => 'iconsets',
                        'tooltip' => esc_html__( 'Select icons-sets to enable for this form.', 'gf_stylespro' ),
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Font Awesome', 'gf_stylespro' ),
                                'name'  => 'icn_fa',
                                'tooltip' => esc_html__( '<h6>785 icons</h6><i>By <a href="http://fontawesome.io/" rel="nofollow" target="_blank">Fort Awesome</a> (v 4.7)</i>', 'gf_stylespro' ),
                            ),
                            array(
                                'label' => esc_html__( 'Elegant Font Icons', 'gf_stylespro' ),
                                'name'  => 'icn_et',
                                'tooltip' => esc_html__( '<h6>360 icons</h6><i>By <a href="https://www.elegantthemes.com/blog/resources/elegant-icon-font" target="_blank">Elegant Themes</a></i>', 'gf_stylespro' ),
                            ),
                            array(
                                'label' => esc_html__( 'Elegant Line Style Icons', 'gf_stylespro' ),
                                'name'  => 'icn_et_line',
                                'tooltip' => esc_html__( '<h6>100 icons</h6><i>By <a href="https://www.elegantthemes.com/blog/freebie-of-the-week/free-line-style-icons" target="_blank">Elegant Themes</a></i>', 'gf_stylespro' ),
                            ),
                            array(
                                'label' => esc_html__( 'Material Icons', 'gf_stylespro' ),
                                'name'  => 'icn_md',
                                'tooltip' => esc_html__( '<h6>932 icons</h6><i>By <a href="https://material.io/icons/" rel="nofollow" target="_blank">Google</a> (v 3.0.1)</i>', 'gf_stylespro' ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'title'  => esc_html__( 'Footer', 'gf_stylespro' ),
                'id'=> "footer",
                'fields' => array(
                    array(
                        'label'   => esc_html__( 'Footer Style', 'gf_stylespro' ),
                        'type'    => 'radio',
                        'name'    => 'footer_style',
                        'horizontal' => true,
                        'choices' => array(
                            array(
                                'label' => esc_html__( 'Default', 'gf_stylespro' ),
                                'class'  => 'has_img default',
                                'value'  => '',
                                'tooltip' => esc_html__( '<h6>Default</h6>Uses the default Footer', 'gf_stylespro' ),
                            ),
                            array(
                                'label' => esc_html__( 'Left Aligned', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_left',
                                'value'  => 'gf_footer_left',
                            ),
                            array(
                                'label' => esc_html__( 'Right Aligned', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_right',
                                'value'  => 'gf_footer_right',
                            ),
                            array(
                                'label' => esc_html__( 'Center Aligned', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_center',
                                'value'  => 'gf_footer_center',
                            ),
                            array(
                                'label' => esc_html__( 'Spread', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_spread',
                                'value'  => 'gf_footer_spread',
                            ),
                            array(
                                'label' => esc_html__( 'Full Footer', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_full',
                                'value'  => 'gf_footer_full',
                            ),
                            array(
                                'label' => esc_html__( 'Center and Left', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_center_left',
                                'value'  => 'gf_footer_center_left',
                            ),
                            array(
                                'label' => esc_html__( 'Center and Right', 'gf_stylespro' ),
                                'class'  => 'has_img gf_footer_center_right',
                                'value'  => 'gf_footer_center_right',
                            ),
                        ),
                    ),
                ),
            ),
        );

        // Add inline footer style only for legacy markup
        if ( GFCommon::is_legacy_markup_enabled( $form ) ) {
            
            $footer_key = array_search('footer', array_column($settings, 'id'));
            $footer_style_key = array_search('footer_style', array_column($settings[$footer_key]['fields'], 'name'));

            $settings[$footer_key]['fields'][$footer_style_key]['choices'][] = array (
                'label' => esc_html__( 'Inline Form', 'gf_stylespro' ),
                'class'  => 'has_img gf_inline_form',
                'value'  => 'gf_inline_form',
                'tooltip' => esc_html__( '<h6>Inline Button</h6>For very small forms. Read more <a href="https://gravitystylespro.com/docs/inline-submit-button/" target="_blank">here</a>', 'gf_stylespro' ),
            );

        }

        return $settings;
    }

    function gf_stylespro_init_scripts( $form ) {
        // Move the icon after the field
        $script = 'jQuery(document).ready(function(){ $wrapper = jQuery("#gform_wrapper_'. $form['id'] .'"); $wrapper.find(".gf_icon_after .gfsp_icon").each(function() { jQuery(this).next().after(this); });  $wrapper.find(".gf_icon_after .ginput_container_date .gfsp_icon, .gf_icon_after .ginput_container_time .gfsp_icon").each(function() { jQuery(this).closest(".ginput_container").siblings(":last").find("input, select").after(this); }) });';
        GFFormDisplay::add_init_script( $form['id'], 'icon_after_field', GFFormDisplay::ON_PAGE_RENDER, $script );

        $is_legacy = GFCommon::is_legacy_markup_enabled($form);

        // Add classes on Product Add-on form
        $theme = self::get_styles_pro_theme($form);
        if ( class_exists('WC_GFPA_Main') ) {
            
            if ( ! empty($theme) && $theme != "none" ) {
                $add_classes_to_form = 'jQuery("#gform_wrapper_'. $form['id'] .'").closest("form").addClass("gf_stylespro '. $theme .'");';
                GFFormDisplay::add_init_script( $form['id'], 'gfsp_classes_to_form', GFFormDisplay::ON_PAGE_RENDER, $add_classes_to_form );
            }
        }
        if ( $theme == "sp_inherit") {
            $gfsp_inherit_icon_height = 'window.gf_stylespro_icon_default=!1,window.setGfspIconHeights=function(){!0!==gf_stylespro_icon_default?(gf_wr=jQuery(".gform_wrapper"),textHeight=gf_wr.find(\'.ginput_container [type="text"]:not(.chosen-search-input):visible\').first().outerHeight(),selectHeight=gf_wr.find(".ginput_container select:not([multiple]):visible").first().outerHeight(),numberHeight=gf_wr.find(\'.ginput_container [type="number"]:visible\').first().outerHeight(),fileHeight=gf_wr.find(\'.ginput_container [type="file"]:visible\').first().outerHeight(),dragDrop=gf_wr.find(".ginput_container .gform_button_select_files:visible").first().outerHeight(),add_styles="",null!=textHeight&&(window.gf_stylespro_icon_default=!0,add_styles+=".gfsp_icon, .gfield_calculation .ginput_container_number .gfsp_icon {height: "+textHeight+"px}"),null!=selectHeight&&(add_styles+=".ginput_container_select .gfsp_icon,.gfield_date_dropdown_day .gfsp_icon,.gfield_date_dropdown_month .gfsp_icon,.gfield_date_dropdown_year .gfsp_icon {height: "+selectHeight+"px}"),null!=numberHeight&&(add_styles+=".ginput_container_number .gfsp_icon {height: "+numberHeight+"px}"),null!=fileHeight&&(add_styles+=".ginput_container_post_image .gfsp_icon,.ginput_container_post_image div:not(.gform_drop_area) .gfsp_icon {height: "+fileHeight+"px}"),null!=dragDrop&&(add_styles+=".gform_drop_area .gfsp_icon {height: "+dragDrop+"px}"),null==gf_wr.children("#gf_stylespro_adjustments")[0]?gf_wr.append(\'<style id="gf_stylespro_adjustments">\'+add_styles+"</style>"):gf_wr.children("#gf_stylespro_adjustments").first().html(add_styles)):gform.removeHook("action","gform_post_conditional_logic_field_action",10,"setGfspIconHeights_tag")},gform.addAction("gform_post_conditional_logic_field_action","setGfspIconHeights",10,"setGfspIconHeights_tag"),jQuery(document).ready((function(){setGfspIconHeights()})),jQuery(window).on("load",(function(){setGfspIconHeights()}));';
            GFFormDisplay::add_init_script( $form['id'], 'gfsp_inherit_icon_height', GFFormDisplay::ON_PAGE_RENDER, $gfsp_inherit_icon_height );
        }

        // Remove default wrapper class
        if ( $is_legacy && self::get_styles_pro_setting_remove_default_wrapper_class() && ! empty($theme) && $theme != "none" ) {
            $gfsp_remove_default_wrapper_class = 'if(window.jQuery){jQuery(".gform_wrapper").removeClass("gform_wrapper")}';
            GFFormDisplay::add_init_script( $form['id'], 'gfsp_remove_default_wrapper_class', GFFormDisplay::ON_PAGE_RENDER, $gfsp_remove_default_wrapper_class );
        }
    }


    /**
     * Front-end
     * Customized validation message
     */
    function gf_stylespro_validation( $message, $form ) {

        $gfsp_form_settings  = $this->get_form_settings( $form );

        // Do nothing, if no theme is selected
        $theme = self::get_styles_pro_theme( $form );
        
        if ( empty($theme) || !is_array($gfsp_form_settings) ){
            return $message;
        }

        $message_append =  "";

        // If popup is true
        if ( $gfsp_form_settings['v_popup'] &&  !empty($gfsp_form_settings['v_message']) ) {
            $message_append = '<div class="gfsp_popup" onclick="jQuery(this).fadeOut()"><div class="validation_error">' . $gfsp_form_settings['v_message'] . '</div></div>';
        }

        // If scrolling is true
        if ( $gfsp_form_settings['v_scroll'] ) {
            $message_append .= "<script>
// For AJAX forms
if (window.jQuery) {
    jQuery(document).one('gform_page_loaded', function(event, form_id, current_page){
        if ( jQuery('.gfield_error')[0] != undefined && !jQuery(document).is(':animated') )
            jQuery('html, body').animate({scrollTop: jQuery('#gform_'+form_id+' .gfield_error').offset().top - 150}, 800);
    });
    //For Non-AJAX forms
    jQuery(document).on('gform_post_render', function(event, form_id, current_page) {
        jQuery(window).on('load', function(){
                if ( jQuery('.gfield_error')[0] != undefined )
                    jQuery('html, body').animate({scrollTop: jQuery('#gform_'+form_id+' .gfield_error').offset().top - 150}, 800);
        });
    });
}</script>";
        }
        
        return $message . $message_append;
    }



    /**
     * Front-end
     * Manage stylesheets
     */
    function gf_stylespro_enq( $form ) {
        // $theme = $form['gf_stylespro_theme'];
        $theme = self::get_styles_pro_theme($form);

        if ( ! empty($theme) && $theme != "none" ) {

            add_filter( 'option_widget_gform_widget', array($this, 'update_gform_widget') );
            

            /*
             * Essemble customized theme settings CSS and Custom CSS
             */
            $style_out = "";
            $gfsp_plugin_settings = $this->get_plugin_settings();

            if ( $gfsp_plugin_settings !== false ) {

                // add customized styles if present
                if ( $this->get_plugin_setting($theme . '_theme_css') !== null ) {
                    $style_out .=$gfsp_plugin_settings[$theme.'_theme_css'];
                }

                // add custom CSS if enabled
                if ( $this->get_plugin_setting('enable_css') === "1" && $this->get_plugin_setting('gfsp_custom_css') !== null ) {
                    $style_out .= '/* Custom CSS */ ' . $gfsp_plugin_settings['gfsp_custom_css'];
                }

                #region Embed fonts
                $embed_font = array();

                if ( $this->get_plugin_setting($theme .'_font_load') == false ) {

                    $font_arr = explode('/', $gfsp_plugin_settings[$theme .'_font']);

                    // Indices:
                    // 0: Font name
                    // 1: Type (Default is Google): Possible values: Native
                    if ( count($font_arr) == 1 ) {
                        // No separator means not native
                        $embed_font[] = $font_arr[0];
                    }
                }

                if ( $this->get_plugin_setting($theme .'_label_font_load') == false ) {

                    $font_arr = explode('/', $gfsp_plugin_settings[$theme .'_label_font']);

                    // Indices:
                    // 0: Font name
                    // 1: Type (Default is Google): Possible values: Native
                    if ( count($font_arr) == 1 ) {
                        // No separator means not native
                        $embed_font[] = $font_arr[0];
                    }

                }

                if ( ! empty($embed_font) ) {
                    
                    $query_args = array(
                        'family' => implode('|', array_unique($embed_font) )
                    );

                    wp_enqueue_style( "{$theme}_google_fonts", add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
                }

            }

            #endregion


            if ( GFCommon::is_legacy_markup_enabled( $form ) ) {
                
                self::enqueue_legacy_form_styles($form, $theme, $style_out);
                
            } else {

                self::enqueue_form_styles($form, $theme, $style_out);
            }
            
            /*
             * Font icons
             */
            $plugin_form_settings  = $this->get_form_settings( $form );
            self::add_icon_fonts( $plugin_form_settings );

        }   // Ends if theme
    }       // Ends funtion



    function enqueue_legacy_form_styles($form, $theme, $style_out) {

        $gfsp_dir = plugin_dir_url( __FILE__ );

        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

        $gfsp_plugin_settings = $this->get_plugin_settings();

        /*
        * Handle Styles Queue
        */
        // Shouldn't be enqued. But just in case
        wp_dequeue_style( 'gforms_css' );
        wp_dequeue_style( 'gforms_reset_css' );
        wp_dequeue_style( 'gforms_formsmain_css' );
        wp_dequeue_style( 'gforms_ready_class_css' );
        wp_dequeue_style( 'gforms_browsers_css' );

        /* RTL */
        wp_register_style( 'gforms_stylespro_rtl_css', $gfsp_dir . "styles/legacy/rtl{$min}.css", array(), $this->_version  );

        if ( $theme == 'sp_inherit' ) {

            // Required for inline inherit scripts
            wp_enqueue_script( 'gform_gravityforms' );
            
            // Enque Styles Pro and Styles Pro Theme
            wp_enqueue_style( 'gforms_stylespro_css', $gfsp_dir . "styles/legacy/gfstylespro_inherit{$min}.css", array(), $this->_version );
            
            // Add inline CSS
            if ($style_out != '') {
                wp_add_inline_style('gforms_stylespro_css', $style_out);
            }

        } else {
            // Enque Styles Pro and Styles Pro Theme
            wp_enqueue_style( 'gforms_stylespro_css', $gfsp_dir . "styles/legacy/gfstylespro{$min}.css", array(), $this->_version );

            if ( file_exists(  plugin_dir_path( __FILE__ ) . 'themes/' . $theme . '.min.css') ) {
                wp_enqueue_style( 'gforms_stylespro_theme_'. $theme, $gfsp_dir . "themes/{$theme}{$min}.css", array(), $this->_version );
            }
            else {
                wp_enqueue_style( 'gforms_stylespro_theme_'. $theme, $gfsp_dir . "themes/{$theme}.css", array(), $this->_version );
            }
            
            // Add inline CSS
            if ($style_out != '') {
                wp_add_inline_style('gforms_stylespro_theme_'. $theme, $style_out);
            }

        }

        if ( is_rtl() ) {
            wp_enqueue_style( 'gforms_stylespro_rtl_css' );
        }

        if ( self::has_datepicker_field( $form ) ) {
            wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . "/css/datepicker{$min}.css", null, GFCommon::$version );
        }

        if ( isset($gfsp_plugin_settings[$theme . '_scripts'] ) && $gfsp_plugin_settings[$theme . '_scripts_load'] ) {
            $theme_scripts = $gfsp_plugin_settings[$theme . '_scripts'];
            wp_enqueue_script( 'gforms_stylespro_'. $theme_scripts, $gfsp_dir . "themes/{$theme_scripts}.js" );
        }
    }

    
    
    function enqueue_form_styles($form, $theme, $style_out) {

        $gfsp_dir = plugin_dir_url( __FILE__ );

        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

        $gfsp_plugin_settings = $this->get_plugin_settings();

        /*
        * Handle Styles Queue
        */

        // Enqueue Styles Pro Styles and Styles Pro Theme
        if ( $theme == 'sp_inherit' ) {
            wp_enqueue_style( 'gforms_stylespro_css', $gfsp_dir . "styles/gfstylespro{$min}.css", array(), $this->_version );
            
            // Add inline CSS
            if ($style_out != '') {
                wp_add_inline_style('gforms_stylespro_css', $style_out);
            }

        } else {
            // Enque Styles Pro and Styles Pro Theme
            wp_enqueue_style( 'gforms_stylespro_css', $gfsp_dir . "styles/gfstylespro{$min}.css", array(), $this->_version );

            if ( file_exists(  plugin_dir_path( __FILE__ ) . 'themes/' . $theme . '.min.css') ) {
                wp_enqueue_style( 'gforms_stylespro_theme_'. $theme, $gfsp_dir . "themes/{$theme}{$min}.css", array(), $this->_version );
            }
            else {
                wp_enqueue_style( 'gforms_stylespro_theme_'. $theme, $gfsp_dir . "themes/{$theme}.css", array(), $this->_version );
            }
            
            // Add inline CSS
            if ($style_out != '') {
                wp_add_inline_style('gforms_stylespro_theme_'. $theme, $style_out);
            }

        }

        if ( $this->get_plugin_setting('reinforce_styles') ) {
            wp_enqueue_style( 'gforms_stylespro_reinf', $gfsp_dir . "styles/reinf{$min}.css", array(), $this->_version );
        }

        if ( self::has_datepicker_field( $form ) ) {
            wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . "/css/datepicker{$min}.css", null, GFCommon::$version );
        }

        if ( isset($gfsp_plugin_settings[$theme . '_scripts'] ) && $gfsp_plugin_settings[$theme . '_scripts_load'] ) {
            $theme_scripts = $gfsp_plugin_settings[$theme . '_scripts'];
            wp_enqueue_script( 'gforms_stylespro_'. $theme_scripts, $gfsp_dir . "themes/{$theme_scripts}.js" );
        }
    }


    /**
     * Front-end
     * Makes sure styles are disabled in widgets
     */
    function update_gform_widget( $options ) {

        foreach ($options as $key => $sub_array) {
            if ( is_array($sub_array) && array_key_exists('disable_scripts', $sub_array) ) {
                    $options[$key]['disable_scripts'] = 1;
            }
        }

        return $options;
    }



    /**
     * Front-end
     * Applies theme to the form and add theme classes
     */
    function gf_stylespro_add( $form ) {

        // $theme = $form['gf_stylespro_theme'];
        $theme = self::get_styles_pro_theme($form);
        $theme_scripts = self::get_styles_pro_theme_scripts( $theme );

        $current_class = isset( $form['cssClass'] ) ? $form['cssClass'] . ' ' : '';

        if (!empty($theme) && $theme != "none") {

            // Add Footer CSS Class
            $form_settings = $this->get_form_settings( $form );
            $footer_class = "";
            if ( is_array($form_settings) && array_key_exists('footer_style', $form_settings) ) {
                $footer_class = " " . $form_settings['footer_style'];
            }

            $form['cssClass'] = $current_class . 'gf_stylespro ' . $theme . $footer_class . ($theme_scripts?' '.$theme_scripts : '');
            self::gf_stylespro_add_ornaments_icons(); // Hooks markup filters
            add_filter( 'gform_validation_message', array($this, 'gf_stylespro_validation'), 9, 2);

            /**
             * Add classes to fields
             */
            add_filter("gform_field_css_class", array($this, "gf_stylespro_add_css_classes"), 10, 3);
        }

        return $form;
    }



    /**
    * Add Icons to fields on form render
    * Hooked from Pre-render
    */
    function gf_stylespro_field_icon( $field_content, $field, $value, $lead_id, $form_id ) {

        if ( $field->gfStylesProIcon ) {

            $icon_data = explode('|', $field->gfStylesProIcon);
            $icon_is_img = ($icon_data[0] == "img") ? true : false;
            $icon = $icon_data[1];
            $iconset = isset( $icon_data[2] ) ? $icon_data[2] : '';

            // Return if the iconset is not enabled to avoid bad charachters
            if ( !$icon_is_img && ( !in_array( 'icn_'.$iconset, $this->_iconsets ) && $iconset != "custom" ) )
                return $field_content;

            // Return if field type is checkbox or radio for fields with multiple inputType
            if ( $field->inputType == 'checkbox' || $field->inputType == 'radio'  || $field->inputType == 'textarea' || $field->inputType == 'list'  || $field->inputType == 'hidden' )
                return $field_content;

            // if has color
            $color = '';
            if ( isset($icon_data[3]) )
                $color = " style='color:" . $icon_data[3] . "'";

            $icon_is_img_class = $icon_is_img?'has_gfsp_icn_img ':'';
            $field_content = str_replace( 'ginput_container ', 'ginput_container has_gfsp_icn ' . $icon_is_img_class , $field_content );

            $icon_string = '';
            if ( $icon_is_img ) {
                $icon_string = '<i style=\'background-image:url("'.$icon.'")\' class="gfsp_icn_img"></i>';
            } else {
                $icon_string = '<i '. $color .'class="'. $icon . '"></i>';
            }

            if ( $field->type == 'select' || $field->inputType == 'select') {
                    $field_content = str_replace( '<select', '<span class="gfsp_icon">'. $icon_string .'</span><select', $field_content );
            }

            else if ( $field->type == 'date' || $field->type == 'address' || $field->type == 'name' || $field->type == 'time' || $field->type == 'fileupload' || $field->type == 'post_image' || $field->type == 'post_custom_field' || $field->type == 'coupon' ) {
                // Since date, address and similar fields have more than 1 input fields, place icon on only the first occurance
                $replace_input  = false;
                $replace_select = false;
                $pos_input  = strpos($field_content, '<input');
                $pos_select = strpos($field_content, '<select');

                // Place the icon only to the first field, whether it be an input or a select
                // Determine if input or select appears first if either or both are present
                if ($pos_input !== false && $pos_select !== false){
                    if ($pos_input < $pos_select)
                        $replace_input = true;
                    else
                        $replace_select = true;
                    }

                else if ($pos_input !== false)
                    $replace_input = true;
                
                else if ($pos_select !== false)
                    $replace_select = true;

                // If input appears first
                if ( $replace_input ) {
                        $replace = '<span class="gfsp_icon">'.$icon_string.'</span><input';

                    $field_content = substr_replace($field_content, $replace, $pos_input, strlen('<input'));
                }

                // If select appears first
                if ($replace_select) {                  
                        $replace = '<span class="gfsp_icon">'.$icon_string.'</span><select';

                    $field_content = substr_replace($field_content, $replace, $pos_select, strlen('<select'));
                }
            }
            
            else {
                $field_content = str_replace( '<input', '<span class="gfsp_icon">'.$icon_string.'</span><input', $field_content );
            }
        }

        return $field_content;
    }

    /*
    * Front-end
    * Add Ornaments to fields: Checkbox, Radio, Polls and Quiz.
    */
    function gf_sylespro_list_ornaments_markup ( $choice_markup, $choice, $field, $value ) {

        $field_type = $field->get_input_type();

        // Return if not a checkbox or radio field
        if ( $field_type != 'radio' && $field_type != 'checkbox' && $field_type != 'poll' && $field_type != 'quiz' )
            return $choice_markup;

        // Return if no value is set
        $gf_ornament = rgar( $choice, 'spOrnament' );

        if ( $gf_ornament == '' )
            return $choice_markup;

        $tag = GFCommon::is_legacy_markup_enabled( $field->formId ) ? 'li' : 'div';

        /* Array indices:
        * 0    Type
        * 1    Data
        * 2    Iconset
        * 3    Color
        */
        $gf_icn_img = explode('|', $gf_ornament);
        
        // If icon
        if ( $gf_icn_img[0] == "icn" ) {
            // if has color
            $color = '';
            if ( isset($gf_icn_img[3]) )
                $color = " style='color:" . $gf_icn_img[3] . "'";
            
            $ornamant = "<i{$color} class='" . $gf_icn_img[1] . "'></i>";
        }

        // If image
        if ( $gf_icn_img[0] == "img" ) {
            // $image_id = attachment_url_to_postid( $gf_icn_img[1] );
            $image_alt = "";
            // $image_src = wp_get_attachment_image_src( $gf_icn_img[1], 'full' );
            if ( isset ( $gf_icn_img[2] ) ) {
                $image_alt = 'alt="'.$gf_icn_img[2].'" ' ;
            }

            $ornamant = "<div class='o_img_wr'><img class='gfsp_label_img' src='" . $gf_icn_img[1] . "' $image_alt/></div>";
        }

        // Add class to the markup
        $update_markup = str_replace( "<{$tag} class='", "<{$tag} class='gfsp_choice_ornament gfsp_choice_". $gf_icn_img[0] . " ", $choice_markup );
        
        // Check ornament position, add markup
        if ( strpos($field['gfStylesPro'], 'o_after') ) {
            // check if choice label is empty
            if ($choice['text'] != '') {
                $update_markup = str_replace( ">{$choice['text']}<", "><div class='o_label'><div class='o_text'>{$choice['text']}</div>{$ornamant}</div><", $update_markup );
            } else {
                $update_markup = str_replace( "></label>", "><div class='o_label'><div class='o_text o_text_empty'>{$choice['text']}</div>{$ornamant}</div></label>", $update_markup );
            }
        }
        else {
            // check if choice label is empty
            if ($choice['text'] != '') {
                $update_markup = str_replace( ">{$choice['text']}<", "><div class='o_label'>{$ornamant}<div class='o_text'>{$choice['text']}</div></div><", $update_markup );
            } else {
                $update_markup = str_replace( "></label>", "><div class='o_label'>{$ornamant}<div class='o_text o_text_empty'>{$choice['text']}</div></div></label>", $update_markup );            
            }
        }

        return $update_markup;    
        
    }


    function gf_sylespro_list_add_other_label ( $choice_markup, $choice, $field, $value ) {
        if ( $field['enableOtherChoice'] && isset( $choice['isOtherChoice'] ) && $choice['isOtherChoice']) {
            $lastChoiceId = count($field['choices']) - 1;
            $findInput = "<input id='input_{$field['formId']}_{$field['id']}_other'";
            
            // Adjust inline jQ to target the right field, since another field is added in the middle
            $choice_markup = str_replace( '.prev', '.prev().prev', $choice_markup );
            $choice_markup = str_replace( '.next', '.next().next', $choice_markup );
            
            $choice_markup = str_replace( $findInput, "<label class='gchoice_other_label' id=\"choice_{$field['formId']}_{$field['id']}_{$lastChoiceId}\" for=\"choice_{$field['formId']}_{$field['id']}_{$lastChoiceId}\"></label>".$findInput, $choice_markup );
        }
        return $choice_markup;
    }


    /**
     * Adds font icons to the _iconsets and enqueues icon fonts
     *
     * @param array $plugin_form_settings
     * @version 1.0.0
     */
    function add_icon_fonts( $plugin_form_settings ) {

        $plugin_dir = plugin_dir_url( __FILE__ );

        $icons_enq = array();

        if ( is_array($plugin_form_settings) ) {
            foreach( $plugin_form_settings as $icon => $val ) {
                // if key starts with 'icn_' and is set to true
                if ( strpos( $icon, 'icn_' ) === 0 && $val ) {
                    array_push( $this->_iconsets, $icon );
                    array_push( $icons_enq, $icon );
                }
            }
        }

        foreach( $icons_enq as $icon ) {
            $icon_src = self::get_icn_source($icon);
            wp_enqueue_style( $icon_src['handle'], $plugin_dir . $icon_src['src'] );
        }
    }


    /*
    * Front-end
    * Hooked to 'gform_pre_render' filter via 'gf_stylespro_add'
    */        
    function gf_stylespro_add_ornaments_icons() {
        
        // Add label element to the 'Other' option in radio field
        add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'gf_sylespro_list_add_other_label' ) , 10, 4 );
        
        // Add Ornaments markup
        add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'gf_sylespro_list_ornaments_markup' ), 500, 4 );

        // Add Icons markup
        if ( is_array( $this->_iconsets ) ) {
            add_filter( 'gform_field_content', array($this, 'gf_stylespro_field_icon' ), 10, 5 );
        }
    
    }



    public function init_ajax() {
        parent::init_ajax();
        // Tasks or filters to perform only during ajax requests
    }



    public function plugin_settings_fields() {
        
        if ( !class_exists("GFSPCommon") ) {
            require_once( plugin_dir_path( __FILE__ ) . 'common.php' );
        }

        return self::get_all_theme_options();
    }

    

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
    public function scripts() {
        $scripts = array(
            array(
                'handle'    => 'jq_color_picker',
                'src'       => $this->get_base_url() . '/scripts/jqColorPicker.min.js',
                // 'version'   => $this->_version,
                'deps'      => array( 'jquery' ),
                'in_footer' => true,
                'callback'  => array( $this, 'localize_scripts' ),
                'enqueue'   => array(
                    array(
                        'admin_page' => array( 'plugin_settings' ),
                        'tab'        => 'gf_stylespro'
                    )
                )
            ),
            array(
                'handle'    => 'gfsp_settings',
                'src'       => $this->get_base_url() . '/scripts/settings.js',
                'deps'      => array( 'jquery' ),
                'version'   => $this->_version,
                'in_footer' => true,
                'callback'  => array( $this, 'localize_scripts' ),
                'enqueue'   => array(
                    array(
                        'admin_page' => array( 'plugin_settings' ),
                        'tab'        => 'gf_stylespro'
                    )
                )
            ),
            array(
                'handle'    => 'gfsp_form_settings',
                'src'       => $this->get_base_url() . '/scripts/form-settings.js',
                'deps'      => array( 'jquery' ),
                'version'   => $this->_version,
                'in_footer' => true,
                'callback'  => array( $this, 'localize_scripts' ),
                'enqueue'   => array(
                    array(
                        'admin_page' => array( 'form_settings' ),
                        'tab'        => 'gf_stylespro'
                    )
                )
            ),
            array(
                'handle'    => 'gfsp_form_editor',
                'src'       => $this->get_base_url() . '/scripts/form-editor.js',
                'deps'      => array( 'jquery', 'wp-color-picker' ),
                'version'   => $this->_version,
                'in_footer' => true,
                'callback'  => array( $this, 'localize_scripts' ),
                'enqueue'   => array(
                    array(
                        'admin_page' => array( 'form_editor' ),
                        'tab'        => 'gf_stylespro'
                    )
                )
            ),
            // array(
			// 	'handle'  => 'wp-color-picker',
			// 	'enqueue' => array(
			// 		array( 'admin_page' => array( 'form_editor' ) )
			// 	)
			// )
        );

        return array_merge( parent::scripts(), $scripts );
    }

    
    function maybe_remove_editor_script() {

        $form = $this->get_current_form();
        $theme = self::get_styles_pro_theme( $form );
        
        if (empty($theme) || $theme == "none") {        
            wp_dequeue_script( 'gfsp_form_editor' );
        }

    }

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
        $styles = array(
			array(
				'handle'  => 'gforms-styles-pro-admin',
				'src'     => $this->get_base_url() . '/styles/admin/admin-editor.css',
                'version' => $this->_version,
                'deps'    => array("gform_settings"),
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'form_settings' ) )
				)
			),
            array(
				'handle'  => 'gforms-styles-pro-admin-settings',
				'src'     => $this->get_base_url() . '/styles/admin/admin-settings.css',
				'version' => $this->_version,
				'enqueue' => array(
                    array(
                        'admin_page' => array( 'plugin_settings' ),
                        'tab'        => 'gf_stylespro'
                    )
				)
			),
            array(
				'handle'  => 'wp-color-picker',
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) )
				)
			)
		);

        /*
         * Font icons
         */
        if ( is_admin() ) {
            $gfsp_dir = plugin_dir_url( __FILE__ );

            $styles_icons = array();
            $plugin_form_settings  = $this->get_form_settings( $this->get_current_form() );

            if (  is_array( $plugin_form_settings ) ) {

                foreach( $plugin_form_settings as $icon => $val ) {
                    // if key starts with 'icn_' and is set to true
                    if ( strpos( $icon, 'icn_' ) === 0 && $val ) {
                        
                        $icon_src = self::get_icn_source($icon);
                        
                        array_push( $styles_icons, array(
                                    'handle' => $icon_src['handle'],
                                    'src' => $gfsp_dir . $icon_src['src'],
                                    'enqueue' => array( array( 'admin_page' => array( 'form_editor' ) ) )
                            )
                        );

                        array_push ( $this->_iconsets, $icon );
                    }
                }
            }
            $styles_all = array_merge($styles, $styles_icons);
        } else {
            $styles_all = $styles;
        }

		return array_merge( parent::styles(), $styles_all );
	}


    /**
     * Adds ornament selections to Choice options
     * Hooks into action 'gform_field_standard_settings'
     * 
     * @uses self::pupulate_icons()
     *
     * @param int $position
     * @param string $form_id
     * 
     * @version 1.0.0
     */
    public function gf_sylespro_list_ornaments_settings( $position, $form_id ) {
        // Create settings on position 1362
        if ( $position == 1362 ) {
            ?>
            <!-- Modal -->
            <div id="add_gf_stylespro_choice_modal" style="display: none">
            <div class="gf_stylespro_selectors" id="gf_stylespro_current_modal">
            <div class="gf_stylespro_bg"></div>
            <div class="box">
                    <?php
                    if ( empty ( $this->_iconsets ) ){
                        $gfsp_settings_link = admin_url( "admin.php?page=gf_edit_forms&view=settings&subview={$this->_slug}&id={$form_id}" );
                        echo "<h4>No icon-sets are enabled. To enable icon-sets for this form, go to <a href='{$gfsp_settings_link}'>Form Settings > {$this->_short_title}</a>.</h4>";
                    } else {
                        ?>
                        <div style="text-align: center; margin-top: 10px">
                            <input id="gfsp_search_icons" type="text" placeholder="Search icons"><button id="gfsp_search_icons_clear" onclick="jQuery('#gfsp_search_icons').val(''); searchIcons()">Clear</button>
                        </div>
                        <?php
                        self::pupulate_icons();
                    }
                    //h h_checkbox h_radio h_poll h_quiz h_option h_post_tags-checkbox h_post_tags-radio h_post_custom_field-checkbox h_post_custom_field-radio h_product-checkbox h_product-radio
                    ?>
            <h3 class="gfsp_icon_custom">Icon <em>(custom)</em></h3>
            <div class="gfsp_icon_custom h" style="display: none">
                <?php gform_tooltip( 'gf_stylespro_custom_icon' ) ?>
                <input class="ornament_icon_custom fieldwidth-2" type="text" value="" placeholder="Custom icon class names" />
                <button class="icon-custom-set button">Set icon</button>
            </div>
            <h3 class="gfsp_image">Image</h3>
            <div class="gfsp_image h" style="display: none">
                <input class="ornament_image fieldwidth-2" type="text" value="" />
                <button class="media-button button">Select image</button>
                <input style="display: none;" id="gfsp_ornament_image_alt" class="ornament_image_alt fieldwidth-2  h h_checkbox h_radio h_poll h_quiz h_option h_post_tags-checkbox h_post_tags-radio h_post_custom_field-checkbox h_post_custom_field-radio h_post_category-checkbox h_post_category-radio h_product-checkbox h_product-radio h_survey-checkbox h_survey-radio" type="text" value="" placeholder="Alt text" />
            </div>
            <input id="gfsp_icon_temp" type="hidden" value='' />
            <div style="text-align: center;">
                <span class="sp_choice_preview" style="display: inline-block"></span>
                <span class="gfsp_color_wrapper" style="display: inline-block; margin-top: 10px; text-align: center"><input id="gfsp_icon_color" type="text" value /></span>
            </div>
            <div class="gfsp_footer">
                <a href="<?php echo $this->_support_url; ?>" target="_blank" style="float: left;">Need help?</a>
                <input type="button" class="button-primary" value="Save" onclick="gfsp_ornament_save();">
                <input type="button" class="button" value="Cancel" onclick="tb_remove();">
                </div>
            </div>
            </div>
            </div>
            <!-- Modal ends -->
        <?php
        }
    }



    /**
     * Populates all icons in icon selection options
     * 
     * @see self::gf_sylespro_list_ornaments_settings()
     * 
     * @version 1.0.0
     */
    public function pupulate_icons() {
        if ( in_array( 'icn_fa' , $this->_iconsets) ) {
            $icon_list = 'address-book,address-book-o,address-card,address-card-o,adjust,adn,align-center,align-justify,align-left,align-right,amazon,ambulance,american-sign-language-interpreting,anchor,android,angellist,angle-double-down,angle-double-left,angle-double-right,angle-double-up,angle-down,angle-left,angle-right,angle-up,apple,archive,area-chart,arrow-circle-down,arrow-circle-left,arrow-circle-o-down,arrow-circle-o-left,arrow-circle-o-right,arrow-circle-o-up,arrow-circle-right,arrow-circle-up,arrow-down,arrow-left,arrow-right,arrow-up,arrows,arrows-alt,arrows-h,arrows-v,asl-interpreting,assistive-listening-systems,asterisk,at,audio-description,automobile,backward,balance-scale,ban,bandcamp,bank,bar-chart,bar-chart-o,barcode,bars,bath,bathtub,battery,battery-0,battery-1,battery-2,battery-3,battery-4,battery-empty,battery-full,battery-half,battery-quarter,battery-three-quarters,bed,beer,behance,behance-square,bell,bell-o,bell-slash,bell-slash-o,bicycle,binoculars,birthday-cake,bitbucket,bitbucket-square,bitcoin,black-tie,blind,bluetooth,bluetooth-b,bold,bolt,bomb,book,bookmark,bookmark-o,braille,briefcase,btc,bug,building,building-o,bullhorn,bullseye,bus,buysellads,cab,calculator,calendar,calendar-check-o,calendar-minus-o,calendar-o,calendar-plus-o,calendar-times-o,camera,camera-retro,car,caret-down,caret-left,caret-right,caret-square-o-down,caret-square-o-left,caret-square-o-right,caret-square-o-up,caret-up,cart-arrow-down,cart-plus,cc,cc-amex,cc-diners-club,cc-discover,cc-jcb,cc-mastercard,cc-paypal,cc-stripe,cc-visa,certificate,chain,chain-broken,check,check-circle,check-circle-o,check-square,check-square-o,chevron-circle-down,chevron-circle-left,chevron-circle-right,chevron-circle-up,chevron-down,chevron-left,chevron-right,chevron-up,child,chrome,circle,circle-o,circle-o-notch,circle-thin,clipboard,clock-o,clone,close,cloud,cloud-download,cloud-upload,cny,code,code-fork,codepen,codiepie,coffee,cog,cogs,columns,comment,comment-o,commenting,commenting-o,comments,comments-o,compass,compress,connectdevelop,contao,copy,copyright,creative-commons,credit-card,credit-card-alt,crop,crosshairs,css3,cube,cubes,cut,cutlery,dashboard,dashcube,database,deaf,deafness,dedent,delicious,desktop,deviantart,diamond,digg,dollar,dot-circle-o,download,dribbble,drivers-license,drivers-license-o,dropbox,drupal,edge,edit,eercast,eject,ellipsis-h,ellipsis-v,empire,envelope,envelope-o,envelope-open,envelope-open-o,envelope-square,envira,eraser,etsy,eur,euro,exchange,exclamation,exclamation-circle,exclamation-triangle,expand,expeditedssl,external-link,external-link-square,eye,eye-slash,eyedropper,fa,facebook,facebook-f,facebook-official,facebook-square,fast-backward,fast-forward,fax,feed,female,fighter-jet,file,file-archive-o,file-audio-o,file-code-o,file-excel-o,file-image-o,file-movie-o,file-o,file-pdf-o,file-photo-o,file-picture-o,file-powerpoint-o,file-sound-o,file-text,file-text-o,file-video-o,file-word-o,file-zip-o,files-o,film,filter,fire,fire-extinguisher,firefox,first-order,flag,flag-checkered,flag-o,flash,flask,flickr,floppy-o,folder,folder-o,folder-open,folder-open-o,font,font-awesome,fonticons,fort-awesome,forumbee,forward,foursquare,free-code-camp,frown-o,futbol-o,gamepad,gavel,gbp,ge,gear,gears,genderless,get-pocket,gg,gg-circle,gift,git,git-square,github,github-alt,github-square,gitlab,gittip,glass,glide,glide-g,globe,google,google-plus,google-plus-circle,google-plus-official,google-plus-square,google-wallet,graduation-cap,gratipay,grav,group,h-square,hacker-news,hand-grab-o,hand-lizard-o,hand-o-down,hand-o-left,hand-o-right,hand-o-up,hand-paper-o,hand-peace-o,hand-pointer-o,hand-rock-o,hand-scissors-o,hand-spock-o,hand-stop-o,handshake-o,hard-of-hearing,hashtag,hdd-o,header,headphones,heart,heart-o,heartbeat,history,home,hospital-o,hotel,hourglass,hourglass-1,hourglass-2,hourglass-3,hourglass-end,hourglass-half,hourglass-o,hourglass-start,houzz,html5,i-cursor,id-badge,id-card,id-card-o,ils,image,imdb,inbox,indent,industry,info,info-circle,inr,instagram,institution,internet-explorer,intersex,ioxhost,italic,joomla,jpy,jsfiddle,key,keyboard-o,krw,language,laptop,lastfm,lastfm-square,leaf,leanpub,legal,lemon-o,level-down,level-up,life-bouy,life-buoy,life-ring,life-saver,lightbulb-o,line-chart,link,linkedin,linkedin-square,linode,linux,list,list-alt,list-ol,list-ul,location-arrow,lock,long-arrow-down,long-arrow-left,long-arrow-right,long-arrow-up,low-vision,magic,magnet,mail-forward,mail-reply,mail-reply-all,male,map,map-marker,map-o,map-pin,map-signs,mars,mars-double,mars-stroke,mars-stroke-h,mars-stroke-v,maxcdn,meanpath,medium,medkit,meetup,meh-o,mercury,microchip,microphone,microphone-slash,minus,minus-circle,minus-square,minus-square-o,mixcloud,mobile,mobile-phone,modx,money,moon-o,mortar-board,motorcycle,mouse-pointer,music,navicon,neuter,newspaper-o,object-group,object-ungroup,odnoklassniki,odnoklassniki-square,opencart,openid,opera,optin-monster,outdent,pagelines,paint-brush,paper-plane,paper-plane-o,paperclip,paragraph,paste,pause,pause-circle,pause-circle-o,paw,paypal,pencil,pencil-square,pencil-square-o,percent,phone,phone-square,photo,picture-o,pie-chart,pied-piper,pied-piper-alt,pied-piper-pp,pinterest,pinterest-p,pinterest-square,plane,play,play-circle,play-circle-o,plug,plus,plus-circle,plus-square,plus-square-o,podcast,power-off,print,product-hunt,puzzle-piece,qq,qrcode,question,question-circle,question-circle-o,quora,quote-left,quote-right,ra,random,ravelry,rebel,recycle,reddit,reddit-alien,reddit-square,refresh,registered,remove,renren,reorder,repeat,reply,reply-all,resistance,retweet,rmb,road,rocket,rotate-left,rotate-right,rouble,rss,rss-square,rub,ruble,rupee,s15,safari,save,scissors,scribd,search,search-minus,search-plus,sellsy,send,send-o,server,share,share-alt,share-alt-square,share-square,share-square-o,shekel,sheqel,shield,ship,shirtsinbulk,shopping-bag,shopping-basket,shopping-cart,shower,sign-in,sign-language,sign-out,signal,signing,simplybuilt,sitemap,skyatlas,skype,slack,sliders,slideshare,smile-o,snapchat,snapchat-ghost,snapchat-square,snowflake-o,soccer-ball-o,sort,sort-alpha-asc,sort-alpha-desc,sort-amount-asc,sort-amount-desc,sort-asc,sort-desc,sort-down,sort-numeric-asc,sort-numeric-desc,sort-up,soundcloud,space-shuttle,spinner,spoon,spotify,square,square-o,stack-exchange,stack-overflow,star,star-half,star-half-empty,star-half-full,star-half-o,star-o,steam,steam-square,step-backward,step-forward,stethoscope,sticky-note,sticky-note-o,stop,stop-circle,stop-circle-o,street-view,strikethrough,stumbleupon,stumbleupon-circle,subscript,subway,suitcase,sun-o,superpowers,superscript,support,table,tablet,tachometer,tag,tags,tasks,taxi,telegram,television,tencent-weibo,terminal,text-height,text-width,th,th-large,th-list,themeisle,thermometer,thermometer-0,thermometer-1,thermometer-2,thermometer-3,thermometer-4,thermometer-empty,thermometer-full,thermometer-half,thermometer-quarter,thermometer-three-quarters,thumb-tack,thumbs-down,thumbs-o-down,thumbs-o-up,thumbs-up,ticket,times,times-circle,times-circle-o,times-rectangle,times-rectangle-o,tint,toggle-down,toggle-left,toggle-off,toggle-on,toggle-right,toggle-up,trademark,train,transgender,transgender-alt,trash,trash-o,tree,trello,tripadvisor,trophy,truck,try,tty,tumblr,tumblr-square,turkish-lira,tv,twitch,twitter,twitter-square,umbrella,underline,undo,universal-access,university,unlink,unlock,unlock-alt,unsorted,upload,usb,usd,user,user-circle,user-circle-o,user-md,user-o,user-plus,user-secret,user-times,users,vcard,vcard-o,venus,venus-double,venus-mars,viacoin,viadeo,viadeo-square,video-camera,vimeo,vimeo-square,vine,vk,volume-control-phone,volume-down,volume-off,volume-up,warning,wechat,weibo,weixin,whatsapp,wheelchair,wheelchair-alt,wifi,wikipedia-w,window-close,window-close-o,window-maximize,window-minimize,window-restore,windows,won,wordpress,wpbeginner,wpexplorer,wpforms,wrench,xing,xing-square,y-combinator,y-combinator-square,yahoo,yc,yc-square,yelp,yen,yoast,youtube,youtube-play,youtube-square';

            echo '<h3>Font Awesome Icons <span class="search_count"></span></h3>
            <div class="gfsp_icons gfsp_fontawesome" style="display: none">
                <div data-iconset="fa" class="all_icons">
                    <span><i class=""></i>No icon</span>';
            
            $icons = explode(',', $icon_list);
            foreach($icons as $icon){
                echo '<span><i class="fa fa-' . $icon . '"></i>' . $icon . '</span>';
            }
            echo '</div></div>';
        }

        if ( in_array( 'icn_et' , $this->_iconsets) ) {
            $icon_list = 'arrow_up,arrow_down,arrow_left,arrow_right,arrow_left-up,arrow_right-up,arrow_right-down,arrow_left-down,arrow-up-down,arrow_up-down_alt,arrow_left-right_alt,arrow_left-right,arrow_expand_alt2,arrow_expand_alt,arrow_condense,arrow_expand,arrow_move,arrow_carrot-up,arrow_carrot-down,arrow_carrot-left,arrow_carrot-right,arrow_carrot-2up,arrow_carrot-2down,arrow_carrot-2left,arrow_carrot-2right,arrow_carrot-up_alt2,arrow_carrot-down_alt2,arrow_carrot-left_alt2,arrow_carrot-right_alt2,arrow_carrot-2up_alt2,arrow_carrot-2down_alt2,arrow_carrot-2left_alt2,arrow_carrot-2right_alt2,arrow_triangle-up,arrow_triangle-down,arrow_triangle-left,arrow_triangle-right,arrow_triangle-up_alt2,arrow_triangle-down_alt2,arrow_triangle-left_alt2,arrow_triangle-right_alt2,arrow_back,icon_minus-06,icon_plus,icon_close,icon_check,icon_minus_alt2,icon_plus_alt2,icon_close_alt2,icon_check_alt2,icon_zoom-out_alt,icon_zoom-in_alt,icon_search,icon_box-empty,icon_box-selected,icon_minus-box,icon_plus-box,icon_box-checked,icon_circle-empty,icon_circle-slelected,icon_stop_alt2,icon_stop,icon_pause_alt2,icon_pause,icon_menu,icon_menu-square_alt2,icon_menu-circle_alt2,icon_ul,icon_ol,icon_adjust-horiz,icon_adjust-vert,icon_document_alt,icon_documents_alt,icon_pencil,icon_pencil-edit_alt,icon_pencil-edit,icon_folder-alt,icon_folder-open_alt,icon_folder-add_alt,icon_info_alt,icon_error-oct_alt,icon_error-circle_alt,icon_error-triangle_alt,icon_question_alt2,icon_question,icon_comment_alt,icon_chat_alt,icon_vol-mute_alt,icon_volume-low_alt,icon_volume-high_alt,icon_quotations,icon_quotations_alt2,icon_clock_alt,icon_lock_alt,icon_lock-open_alt,icon_key_alt,icon_cloud_alt,icon_cloud-upload_alt,icon_cloud-download_alt,icon_image,icon_images,icon_lightbulb_alt,icon_gift_alt,icon_house_alt,icon_genius,icon_mobile,icon_tablet,icon_laptop,icon_desktop,icon_camera_alt,icon_mail_alt,icon_cone_alt,icon_ribbon_alt,icon_bag_alt,icon_creditcard,icon_cart_alt,icon_paperclip,icon_tag_alt,icon_tags_alt,icon_trash_alt,icon_cursor_alt,icon_mic_alt,icon_compass_alt,icon_pin_alt,icon_pushpin_alt,icon_map_alt,icon_drawer_alt,icon_toolbox_alt,icon_book_alt,icon_calendar,icon_film,icon_table,icon_contacts_alt,icon_headphones,icon_lifesaver,icon_piechart,icon_refresh,icon_link_alt,icon_link,icon_loading,icon_blocked,icon_archive_alt,icon_heart_alt,icon_printer,icon_calulator,icon_building,icon_floppy,icon_drive,icon_search-2,icon_id,icon_id-2,icon_puzzle,icon_like,icon_dislike,icon_mug,icon_currency,icon_wallet,icon_pens,icon_easel,icon_flowchart,icon_datareport,icon_briefcase,icon_shield,icon_percent,icon_globe,icon_globe-2,icon_target,icon_hourglass,icon_balance,icon_star_alt,icon_star-half_alt,icon_star,icon_star-half,icon_tools,icon_tool,icon_cog,icon_cogs,arrow_up_alt,arrow_down_alt,arrow_left_alt,arrow_right_alt,arrow_left-up_alt,arrow_right-up_alt,arrow_right-down_alt,arrow_left-down_alt,arrow_condense_alt,arrow_expand_alt3,arrow_carrot_up_alt,arrow_carrot-down_alt,arrow_carrot-left_alt,arrow_carrot-right_alt,arrow_carrot-2up_alt,arrow_carrot-2dwnn_alt,arrow_carrot-2left_alt,arrow_carrot-2right_alt,arrow_triangle-up_alt,arrow_triangle-down_alt,arrow_triangle-left_alt,arrow_triangle-right_alt,icon_minus_alt,icon_plus_alt,icon_close_alt,icon_check_alt,icon_zoom-out,icon_zoom-in,icon_stop_alt,icon_menu-square_alt,icon_menu-circle_alt,icon_document,icon_documents,icon_pencil_alt,icon_folder,icon_folder-open,icon_folder-add,icon_folder_upload,icon_folder_download,icon_info,icon_error-circle,icon_error-oct,icon_error-triangle,icon_question_alt,icon_comment,icon_chat,icon_vol-mute,icon_volume-low,icon_volume-high,icon_quotations_alt,icon_clock,icon_lock,icon_lock-open,icon_key,icon_cloud,icon_cloud-upload,icon_cloud-download,icon_lightbulb,icon_gift,icon_house,icon_camera,icon_mail,icon_cone,icon_ribbon,icon_bag,icon_cart,icon_tag,icon_tags,icon_trash,icon_cursor,icon_mic,icon_compass,icon_pin,icon_pushpin,icon_map,icon_drawer,icon_toolbox,icon_book,icon_contacts,icon_archive,icon_heart,icon_profile,icon_group,icon_grid-2x2,icon_grid-3x3,icon_music,icon_pause_alt,icon_phone,icon_upload,icon_download,icon_rook,icon_printer-alt,icon_calculator_alt,icon_building_alt,icon_floppy_alt,icon_drive_alt,icon_search_alt,icon_id_alt,icon_id-2_alt,icon_puzzle_alt,icon_like_alt,icon_dislike_alt,icon_mug_alt,icon_currency_alt,icon_wallet_alt,icon_pens_alt,icon_easel_alt,icon_flowchart_alt,icon_datareport_alt,icon_briefcase_alt,icon_shield_alt,icon_percent_alt,icon_globe_alt,icon_clipboard,social_facebook,social_twitter,social_pinterest,social_googleplus,social_tumblr,social_tumbleupon,social_wordpress,social_instagram,social_dribbble,social_vimeo,social_linkedin,social_rss,social_deviantart,social_share,social_myspace,social_skype,social_youtube,social_picassa,social_googledrive,social_flickr,social_blogger,social_spotify,social_delicious,social_facebook_circle,social_twitter_circle,social_pinterest_circle,social_googleplus_circle,social_tumblr_circle,social_stumbleupon_circle,social_wordpress_circle,social_instagram_circle,social_dribbble_circle,social_vimeo_circle,social_linkedin_circle,social_rss_circle,social_deviantart_circle,social_share_circle,social_myspace_circle,social_skype_circle,social_youtube_circle,social_picassa_circle,social_googledrive_alt2,social_flickr_circle,social_blogger_circle,social_spotify_circle,social_delicious_circle,social_facebook_square,social_twitter_square,social_pinterest_square,social_googleplus_square,social_tumblr_square,social_stumbleupon_square,social_wordpress_square,social_instagram_square,social_dribbble_square,social_vimeo_square,social_linkedin_square,social_rss_square,social_deviantart_square,social_share_square,social_myspace_square,social_skype_square,social_youtube_square,social_picassa_square,social_googledrive_square,social_flickr_square,social_blogger_square,social_spotify_square,social_delicious_square';

            echo '<h3>Elegant Theme\'s Font Icons <span class="search_count"></span></h3>
            <div class="gfsp_icons gfsp_elegantfont" style="display: none">
                <div data-iconset="et" class="all_icons">
                    <span><i class=""></i>No icon</span>';
            
            $icons = explode(',', $icon_list);
            foreach($icons as $icon){
                echo '<span><i class="et ' . $icon . '"></i>' . $icon . '</span>';
            }
            echo '</div></div>';
        }

        if ( in_array( 'icn_et_line' , $this->_iconsets) ) {
            $icon_list = 'icon-mobile,icon-laptop,icon-desktop,icon-tablet,icon-phone,icon-document,icon-documents,icon-search,icon-clipboard,icon-newspaper,icon-notebook,icon-book-open,icon-browser,icon-calendar,icon-presentation,icon-picture,icon-pictures,icon-video,icon-camera,icon-printer,icon-toolbox,icon-briefcase,icon-wallet,icon-gift,icon-bargraph,icon-grid,icon-expand,icon-focus,icon-edit,icon-adjustments,icon-ribbon,icon-hourglass,icon-lock,icon-megaphone,icon-shield,icon-trophy,icon-flag,icon-map,icon-puzzle,icon-basket,icon-envelope,icon-streetsign,icon-telescope,icon-gears,icon-key,icon-paperclip,icon-attachment,icon-pricetags,icon-lightbulb,icon-layers,icon-pencil,icon-tools,icon-tools-2,icon-scissors,icon-paintbrush,icon-magnifying-glass,icon-circle-compass,icon-linegraph,icon-mic,icon-strategy,icon-beaker,icon-caution,icon-recycle,icon-anchor,icon-profile-male,icon-profile-female,icon-bike,icon-wine,icon-hotairballoon,icon-glob,icon-genius,icon-map-pin,icon-dial,icon-chat,icon-heart,icon-cloud,icon-upload,icon-download,icon-traget,icon-hazardous,icon-piechart,icon-speedometer,icon-global,icon-compass,icon-lifesaver,icon-clock,icon-aperture,icon-quote,icon-scope,icon-alarmclock,icon-refresh,icon-happy,icon-sad,icon-facebook,icon-twitter,icon-googleplus,icon-rss,icon-tumblr,icon-linkedin,icon-dribbble';

            echo '<h3>Elegant Theme\'s Line Icons <span class="search_count"></span></h3>
            <div class="gfsp_icons gfsp_elegantlinefont" style="display: none">
                <div data-iconset="et_line" class="all_icons">
                    <span><i class=""></i>No icon</span>';
            
            $icons = explode(',', $icon_list);
            foreach($icons as $icon){
                echo '<span><i class="et ' . $icon . '"></i>' . $icon . '</span>';
            }
            echo '</div></div>';
        }

        if ( in_array( 'icn_md' , $this->_iconsets) ) {
            $icon_list = '3d_rotation,ac_unit,access_alarm,access_alarms,access_time,accessibility,accessible,account_balance,account_balance_wallet,account_box,account_circle,adb,add,add_a_photo,add_alarm,add_alert,add_box,add_circle,add_circle_outline,add_location,add_shopping_cart,add_to_photos,add_to_queue,adjust,airline_seat_flat,airline_seat_flat_angled,airline_seat_individual_suite,airline_seat_legroom_extra,airline_seat_legroom_normal,airline_seat_legroom_reduced,airline_seat_recline_extra,airline_seat_recline_normal,airplanemode_active,airplanemode_inactive,airplay,airport_shuttle,alarm,alarm_add,alarm_off,alarm_on,album,all_inclusive,all_out,android,announcement,apps,archive,arrow_back,arrow_downward,arrow_drop_down,arrow_drop_down_circle,arrow_drop_up,arrow_forward,arrow_upward,art_track,aspect_ratio,assessment,assignment,assignment_ind,assignment_late,assignment_return,assignment_returned,assignment_turned_in,assistant,assistant_photo,attach_file,attach_money,attachment,audiotrack,autorenew,av_timer,backspace,backup,battery_alert,battery_charging_full,battery_full,battery_std,battery_unknown,beach_access,beenhere,block,bluetooth,bluetooth_audio,bluetooth_connected,bluetooth_disabled,bluetooth_searching,blur_circular,blur_linear,blur_off,blur_on,book,bookmark,bookmark_border,border_all,border_bottom,border_clear,border_color,border_horizontal,border_inner,border_left,border_outer,border_right,border_style,border_top,border_vertical,branding_watermark,brightness_1,brightness_2,brightness_3,brightness_4,brightness_5,brightness_6,brightness_7,brightness_auto,brightness_high,brightness_low,brightness_medium,broken_image,brush,bubble_chart,bug_report,build,burst_mode,business,business_center,cached,cake,call,call_end,call_made,call_merge,call_missed,call_missed_outgoing,call_received,call_split,call_to_action,camera,camera_alt,camera_enhance,camera_front,camera_rear,camera_roll,cancel,card_giftcard,card_membership,card_travel,casino,cast,cast_connected,center_focus_strong,center_focus_weak,change_history,chat,chat_bubble,chat_bubble_outline,check,check_box,check_box_outline_blank,check_circle,chevron_left,chevron_right,child_care,child_friendly,chrome_reader_mode,class,clear,clear_all,close,closed_caption,cloud,cloud_circle,cloud_done,cloud_download,cloud_off,cloud_queue,cloud_upload,code,collections,collections_bookmark,color_lens,colorize,comment,compare,compare_arrows,computer,confirmation_number,contact_mail,contact_phone,contacts,content_copy,content_cut,content_paste,control_point,control_point_duplicate,copyright,create,create_new_folder,credit_card,crop,crop_16_9,crop_3_2,crop_5_4,crop_7_5,crop_din,crop_free,crop_landscape,crop_original,crop_portrait,crop_rotate,crop_square,dashboard,data_usage,date_range,dehaze,delete,delete_forever,delete_sweep,description,desktop_mac,desktop_windows,details,developer_board,developer_mode,device_hub,devices,devices_other,dialer_sip,dialpad,directions,directions_bike,directions_boat,directions_bus,directions_car,directions_railway,directions_run,directions_subway,directions_transit,directions_walk,disc_full,dns,do_not_disturb,do_not_disturb_alt,do_not_disturb_off,do_not_disturb_on,dock,domain,done,done_all,donut_large,donut_small,drafts,drag_handle,drive_eta,dvr,edit,edit_location,eject,email,enhanced_encryption,equalizer,error,error_outline,euro_symbol,ev_station,event,event_available,event_busy,event_note,event_seat,exit_to_app,expand_less,expand_more,explicit,explore,exposure,exposure_neg_1,exposure_neg_2,exposure_plus_1,exposure_plus_2,exposure_zero,extension,face,fast_forward,fast_rewind,favorite,favorite_border,featured_play_list,featured_video,feedback,fiber_dvr,fiber_manual_record,fiber_new,fiber_pin,fiber_smart_record,file_download,file_upload,filter,filter_1,filter_2,filter_3,filter_4,filter_5,filter_6,filter_7,filter_8,filter_9,filter_9_plus,filter_b_and_w,filter_center_focus,filter_drama,filter_frames,filter_hdr,filter_list,filter_none,filter_tilt_shift,filter_vintage,find_in_page,find_replace,fingerprint,first_page,fitness_center,flag,flare,flash_auto,flash_off,flash_on,flight,flight_land,flight_takeoff,flip,flip_to_back,flip_to_front,folder,folder_open,folder_shared,folder_special,font_download,format_align_center,format_align_justify,format_align_left,format_align_right,format_bold,format_clear,format_color_fill,format_color_reset,format_color_text,format_indent_decrease,format_indent_increase,format_italic,format_line_spacing,format_list_bulleted,format_list_numbered,format_paint,format_quote,format_shapes,format_size,format_strikethrough,format_textdirection_l_to_r,format_textdirection_r_to_l,format_underlined,forum,forward,forward_10,forward_30,forward_5,free_breakfast,fullscreen,fullscreen_exit,functions,g_translate,gamepad,games,gavel,gesture,get_app,gif,golf_course,gps_fixed,gps_not_fixed,gps_off,grade,gradient,grain,graphic_eq,grid_off,grid_on,group,group_add,group_work,hd,hdr_off,hdr_on,hdr_strong,hdr_weak,headset,headset_mic,healing,hearing,help,help_outline,high_quality,highlight,highlight_off,history,home,hot_tub,hotel,hourglass_empty,hourglass_full,http,https,image,image_aspect_ratio,import_contacts,import_export,important_devices,inbox,indeterminate_check_box,info,info_outline,input,insert_chart,insert_comment,insert_drive_file,insert_emoticon,insert_invitation,insert_link,insert_photo,invert_colors,invert_colors_off,iso,keyboard,keyboard_arrow_down,keyboard_arrow_left,keyboard_arrow_right,keyboard_arrow_up,keyboard_backspace,keyboard_capslock,keyboard_hide,keyboard_return,keyboard_tab,keyboard_voice,kitchen,label,label_outline,landscape,language,laptop,laptop_chromebook,laptop_mac,laptop_windows,last_page,launch,layers,layers_clear,leak_add,leak_remove,lens,library_add,library_books,library_music,lightbulb_outline,line_style,line_weight,linear_scale,link,linked_camera,list,live_help,live_tv,local_activity,local_airport,local_atm,local_bar,local_cafe,local_car_wash,local_convenience_store,local_dining,local_drink,local_florist,local_gas_station,local_grocery_store,local_hospital,local_hotel,local_laundry_service,local_library,local_mall,local_movies,local_offer,local_parking,local_pharmacy,local_phone,local_pizza,local_play,local_post_office,local_printshop,local_see,local_shipping,local_taxi,location_city,location_disabled,location_off,location_on,location_searching,lock,lock_open,lock_outline,looks,looks_3,looks_4,looks_5,looks_6,looks_one,looks_two,loop,loupe,low_priority,loyalty,mail,mail_outline,map,markunread,markunread_mailbox,memory,menu,merge_type,message,mic,mic_none,mic_off,mms,mode_comment,mode_edit,monetization_on,money_off,monochrome_photos,mood,mood_bad,more,more_horiz,more_vert,motorcycle,mouse,move_to_inbox,movie,movie_creation,movie_filter,multiline_chart,music_note,music_video,my_location,nature,nature_people,navigate_before,navigate_next,navigation,near_me,network_cell,network_check,network_locked,network_wifi,new_releases,next_week,nfc,no_encryption,no_sim,not_interested,note,note_add,notifications,notifications_active,notifications_none,notifications_off,notifications_paused,offline_pin,ondemand_video,opacity,open_in_browser,open_in_new,open_with,pages,pageview,palette,pan_tool,panorama,panorama_fish_eye,panorama_horizontal,panorama_vertical,panorama_wide_angle,party_mode,pause,pause_circle_filled,pause_circle_outline,payment,people,people_outline,perm_camera_mic,perm_contact_calendar,perm_data_setting,perm_device_information,perm_identity,perm_media,perm_phone_msg,perm_scan_wifi,person,person_add,person_outline,person_pin,person_pin_circle,personal_video,pets,phone,phone_android,phone_bluetooth_speaker,phone_forwarded,phone_in_talk,phone_iphone,phone_locked,phone_missed,phone_paused,phonelink,phonelink_erase,phonelink_lock,phonelink_off,phonelink_ring,phonelink_setup,photo,photo_album,photo_camera,photo_filter,photo_library,photo_size_select_actual,photo_size_select_large,photo_size_select_small,picture_as_pdf,picture_in_picture,picture_in_picture_alt,pie_chart,pie_chart_outlined,pin_drop,place,play_arrow,play_circle_filled,play_circle_outline,play_for_work,playlist_add,playlist_add_check,playlist_play,plus_one,poll,polymer,pool,portable_wifi_off,portrait,power,power_input,power_settings_new,pregnant_woman,present_to_all,print,priority_high,public,publish,query_builder,question_answer,queue,queue_music,queue_play_next,radio,radio_button_checked,radio_button_unchecked,rate_review,receipt,recent_actors,record_voice_over,redeem,redo,refresh,remove,remove_circle,remove_circle_outline,remove_from_queue,remove_red_eye,remove_shopping_cart,reorder,repeat,repeat_one,replay,replay_10,replay_30,replay_5,reply,reply_all,report,report_problem,restaurant,restaurant_menu,restore,restore_page,ring_volume,room,room_service,rotate_90_degrees_ccw,rotate_left,rotate_right,rounded_corner,router,rowing,rss_feed,rv_hookup,satellite,save,scanner,schedule,school,screen_lock_landscape,screen_lock_portrait,screen_lock_rotation,screen_rotation,screen_share,sd_card,sd_storage,search,security,select_all,send,sentiment_dissatisfied,sentiment_neutral,sentiment_satisfied,sentiment_very_dissatisfied,sentiment_very_satisfied,settings,settings_applications,settings_backup_restore,settings_bluetooth,settings_brightness,settings_cell,settings_ethernet,settings_input_antenna,settings_input_component,settings_input_composite,settings_input_hdmi,settings_input_svideo,settings_overscan,settings_phone,settings_power,settings_remote,settings_system_daydream,settings_voice,share,shop,shop_two,shopping_basket,shopping_cart,short_text,show_chart,shuffle,signal_cellular_4_bar,signal_cellular_connected_no_internet_4_bar,signal_cellular_no_sim,signal_cellular_null,signal_cellular_off,signal_wifi_4_bar,signal_wifi_4_bar_lock,signal_wifi_off,sim_card,sim_card_alert,skip_next,skip_previous,slideshow,slow_motion_video,smartphone,smoke_free,smoking_rooms,sms,sms_failed,snooze,sort,sort_by_alpha,spa,space_bar,speaker,speaker_group,speaker_notes,speaker_notes_off,speaker_phone,spellcheck,star,star_border,star_half,stars,stay_current_landscape,stay_current_portrait,stay_primary_landscape,stay_primary_portrait,stop,stop_screen_share,storage,store,store_mall_directory,straighten,streetview,strikethrough_s,style,subdirectory_arrow_left,subdirectory_arrow_right,subject,subscriptions,subtitles,subway,supervisor_account,surround_sound,swap_calls,swap_horiz,swap_vert,swap_vertical_circle,switch_camera,switch_video,sync,sync_disabled,sync_problem,system_update,system_update_alt,tab,tab_unselected,tablet,tablet_android,tablet_mac,tag_faces,tap_and_play,terrain,text_fields,text_format,textsms,texture,theaters,thumb_down,thumb_up,thumbs_up_down,time_to_leave,timelapse,timeline,timer,timer_10,timer_3,timer_off,title,toc,today,toll,tonality,touch_app,toys,track_changes,traffic,train,tram,transfer_within_a_station,transform,translate,trending_down,trending_flat,trending_up,tune,turned_in,turned_in_not,tv,unarchive,undo,unfold_less,unfold_more,update,usb,verified_user,vertical_align_bottom,vertical_align_center,vertical_align_top,vibration,video_call,video_label,video_library,videocam,videocam_off,videogame_asset,view_agenda,view_array,view_carousel,view_column,view_comfy,view_compact,view_day,view_headline,view_list,view_module,view_quilt,view_stream,view_week,vignette,visibility,visibility_off,voice_chat,voicemail,volume_down,volume_mute,volume_off,volume_up,vpn_key,vpn_lock,wallpaper,warning,watch,watch_later,wb_auto,wb_cloudy,wb_incandescent,wb_iridescent,wb_sunny,wc,web,web_asset,weekend,whatshot,widgets,wifi,wifi_lock,wifi_tethering,work,wrap_text,youtube_searched_for,zoom_in,zoom_out,zoom_out_map';

            echo '<h3>Material Icons <span class="search_count"></span></h3>
            <div class="gfsp_icons gfsp_materialicons" style="display: none">
                <div data-iconset="md" class="all_icons">
                    <span><i class=""></i>No icon</span>';
            
            $icons = explode(',', $icon_list);
            foreach($icons as $icon){
                echo '<span><i class="md md-' . $icon . '"></i>' . $icon . '</span>';
            }
            echo '</div></div>';
        }
    }

    
    /**
     * Get handle and source to enque icons
     *
     * @param string $icon
     * 
     * @version 1.0.0
     */
	private static function get_icn_source( $icon ) {
        
        $icon_src['icn_fa'] = array(
            'handle' => 'font_awesome',
            'src' => 'fonts/font-awesome-4.7.0/css/font-awesome.min.css',
            'local' => 'fonts/font-awesome-4.7.0/css/font-awesome.css',
            'cdn' => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        );
        $icon_src['icn_md'] = array(
            'handle' => 'material_icons',
            'src' => 'fonts/material_icons/md-icons.css',
            'local' => 'fonts/material_icons/md-icons.css',
            'cdn' => 'https://fonts.googleapis.com/icon?family=Material+Icons',
        );
        $icon_src['icn_et'] = array(
            'handle' => 'elegenat_font_icons',
            'src' => 'fonts/elegant_font/style.min.css',
            'local' => 'fonts/elegant_font/style.css',
            'cdn' => false,
        );
        $icon_src['icn_et_line'] = array(
            'handle' => 'elegenat_line_icons',
            'src' => 'fonts/et_line_font/style.min.css',
            'local' => 'fonts/et_line_font/style.css',
            'cdn' => false,
        );

        return array( 'handle' => $icon_src[$icon]['handle'], 'src' => $icon_src[$icon]['src'] );
    }

    /**
     * Echoes img tag from icons folder
     *
     * @param [string] $filename
     * @return void
     */
    private static function icn_img( $filename ) {
        $gfsp_dir = plugin_dir_url( __FILE__ );
        echo "<img src=\"{$gfsp_dir}icons/{$filename}\">";
    }




    /*
    # ---  FORM SETTINGS ----------------------------------------------------------------------------
    */

    /*
    * Create Form Settings
        TODO: Not used; Remove
    */
    function gf_stylespro_theme_select( $settings, $form ) {
        
        // Return if v2 settings are found
        if ( is_array( $this->get_form_settings($form) ) ) {
            return $settings;
        }

        // v1
        $themes_data = array(
        'label' => 'Theme Name',
        'value' => 'Theme Slug'
        );

        foreach ( glob( plugin_dir_path( __FILE__ ) . "themes/*.css" ) as $file ) {
            $gfsp_themes = get_file_data( $file, $themes_data );
            $options[$gfsp_themes['value']] = $gfsp_themes['label'];
        }

        // Get currently selected theme form settings
        $sel_form = rgar( $form, 'gf_stylespro_theme' );
        
        // Create dropdown options with a loop including selected
        $opts = "<option value=''>No theme</option>";

        foreach ( $options as $value => $name ) {
            $opts = $opts . '<option value="' . htmlentities($value) . '"' . (($sel_form === $value) ? ' selected="selected"' : "") . '>' . htmlentities($name) . '</option>';
        }

        $url = admin_url( "admin.php?page=gf_edit_forms&view=settings&subview=gf_stylespro&id={$form['id']}" );

        $settings['Styles Pro']['gf_stylespro_theme'] = '
            <tr>
                <td colspan="2">Theme selection option has been moved to <strong>Form settings > <a href='. $url . '>Styles Pro</a></strong>.</td>
            </tr>
            <tr style="display: none;">
                <th> </th>
                <td><select name="gf_stylespro_theme" id="gf_stylespro_theme">' . $opts . ' </select> &nbsp; <a class="button" href="' . esc_url( admin_url( 'admin.php?page=gf_settings&subview=gf_stylespro' ) ) . '">Edit themes</a>
                </td>
            </tr>';


        return $settings;
    }



    /*
    * Save Form Settings
        TODO: Not used; Remove
    */
    function save_gf_stylespro_form_settings( $form ) {
        // Relic from v1
        $form['gf_stylespro_theme'] = rgpost( 'gf_stylespro_theme' );

        return $form;
    }

    /*
    * Check if form has Date field
    */
	private static function has_datepicker_field( $form ) {
		if ( is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {

				if ( RGFormsModel::get_input_type( $field ) == 'date' && $field->dateType == 'datepicker' ) {
					return true;
				}
			}
		}

		return false;
	}

    function get_styles_pro_theme( $form ) {
        $gfsp_plugin_settings = $this->get_plugin_settings();
        $gfsp_form_settings  = $this->get_form_settings($form);
        $default_theme = '';

        // Get default theme
        if ( is_array( $gfsp_plugin_settings ) && array_key_exists('default_theme', $gfsp_plugin_settings) )
            $default_theme = $gfsp_plugin_settings['default_theme'];

        // Get theme from Form settings
        if( ! is_array($gfsp_form_settings) ) {
            // Version 1.0
            $theme = !empty($form['gf_stylespro_theme']) ? $form['gf_stylespro_theme'] : '' ;
        } else {
            $theme = isset( $gfsp_form_settings['theme'] ) ? $gfsp_form_settings['theme'] : "";
        }

        // Fallback to default theme; if no theme selected
        if ( empty($theme) )
            $theme = $default_theme;

        return apply_filters( 'gf_stylespro_theme_filter', $theme, $form );
        // return $theme;
    }

    function get_styles_pro_theme_scripts( $theme ) {
        
        $gfsp_plugin_settings = $this->get_plugin_settings();
        
        if ( isset($gfsp_plugin_settings[$theme . '_scripts_load']) && $gfsp_plugin_settings[$theme . '_scripts_load'] == true ) {
            $theme_scripts = $gfsp_plugin_settings[$theme . '_scripts'];
            
            return $theme_scripts;
        } else {
            
            return false;
        }
    }

    function get_styles_pro_setting_remove_default_wrapper_class() {
        
        $gfsp_plugin_settings = $this->get_plugin_settings();
        
        if ( isset($gfsp_plugin_settings['remove_default_wrapper_class']) && $gfsp_plugin_settings['remove_default_wrapper_class'] == true ) {
            
            return true;
        
        } else {
            
            return false;
        }
    }



    /*
    # ---  FIELD SETTINGS -------------------------------------------
    */

    function gf_stylespro_appearance_settings( $position, $form_id ) {
        // Create settings on position 250 (right before Custom CSS Style Label)
        if ( $position == 250 ) {
            // $gfsp_dir = plugin_dir_url( __FILE__ );
            $form = GFAPI::get_form( $form_id );
            // $theme = ($form['gf_stylespro']['theme'] !='' ? $form['gf_stylespro']['theme'] : $form['gf_stylespro_theme'] );
            $theme = self::get_styles_pro_theme($form);

            $is_legacy = GFCommon::is_legacy_markup_enabled($form);
            
            include('inc/style-selector.php');
        }
    }



    function gf_stylespro_add_field_size_option( $choices ) {
        // Add option in Field Size dropdown for Full Width field size
        $choices[] = array( 'value' => 'large full', 'text' => 'Full width' );
 
        return $choices;
    }



    function gf_stylespro_appearance_settings_icon( $position, $form_id ) {
        // Create settings on position 250 (right before Custom CSS Style Label)
        if ( $position == 250 ) {
    ?>
            <li class="gfsp_icon field_setting">
                <input type="hidden" id="gf_stylespro_icon_value" value="" />
                <button id="add_gf_stylespro_icon" class="button" onclick="field_icon_tb_show()">Field Icon <span class="sp_field_icon_preview"></span></button>
            </li>
    <?php
        }
    }


    public function settings_adv_field_styles($field) {
        $theme = $field['theme_name'];
        $label = $field['label'];
        echo '<a class="show_adv_styles" onclick="jQuery(this).parent().toggleClass(\'expanded\');">'.$label.'</a>
        <div class="adv_field_options_wrapper">
            <h5>Advanced field styling</h5>
            <div class="adv_fields">';

        echo '<div class="adv_field inline_options icon_options"><span>'. esc_html__( 'Style', 'gf_stylespro' ) .'</span>';
        $this->settings_checkbox(
            array(
                "label"   => "Style",
                "class"   => "font_style",
                "name"    => $theme."_font_styles",
                "horizontal" => true,
                "choices" =>
                    array(
                        array(
                            "label" => " ",
                            "class" => "font_italic",
                            "name"  => $theme."_font_italic"
                        ),
                        array(
                            "label" => " ",
                            "class" =>  "font_underline",
                            "name"  => $theme."_font_underline"
                        )
                    )
            )
        );
        echo '</div>';
        
        echo '<div class="adv_field"><span>Weight</span>';
        $this->settings_select(
            array(
                'label'         => 'Weight',
                'name'          => $theme.'_field_font_weight',
                'default_value' => '',
                'class'         => 'adv_font_weight font_weight',
                'choices'       => array(
                    array(  'label' => 'Default',   'value' => '' ),
                    array(  'label' => 'Normal', 'value' => 'normal' ),
                    array(  'label' => 'Bold', 'value' => 'bold' ),
                    array(  'label' => '100',   'value' => '100' ),
                    array(  'label' => '200',   'value' => '200' ),
                    array(  'label' => '300',   'value' => '300' ),
                    array(  'label' => '400',   'value' => '400' ),
                    array(  'label' => '500',   'value' => '500' ),
                    array(  'label' => '600',   'value' => '600' ),
                    array(  'label' => '700',   'value' => '700' ),
                    array(  'label' => '800',   'value' => '800' ),
                    array(  'label' => '900',   'value' => '900' ),
                )
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color',
                'name'          => $theme.'_field_bg_color',
                'default_value' => '',
                'class'         => 'color adv_bg_color field_bg_color',            
            )
        );
        echo '</div>';

        echo '<div class="adv_field inline_options icon_options adv_text_align"><span>'. esc_html__( 'Align', 'gf_stylespro' ) .'</span>';
        $this->settings_radio(
            array(
                'name'          => "{$theme}_field_text_align",
                'default_value' => '',
                'class'         => 'adv_text_align field_text_align',
                'type'    => 'radio',
                'horizontal'    => true,
                'choices' => array(
                    array(
                        'label' => esc_html__( 'Auto', 'gf_stylespro' ),
                        'value'  => '',
                        'class'  => "radio_icon adv_text_align_auto",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'left',
                        'class'  => "radio_icon adv_text_align_left",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'center',
                        'class'  => "radio_icon adv_text_align_center",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'right',
                        'class'  => "radio_icon adv_text_align_right",
                    ),
                ),
            )
        );
        echo '</div>';
        
        echo '<div class="adv_field"><span>Height <i>(top & buttom padding)</i></span>';
        $this->settings_text(
            array(
                'label'         => 'Vertical Padding',
                'name'          => $theme.'_field_v_padding',
                'default_value' => '',
                'class'         => 'small adv_v_padding field_v_padding auto_px',
                'placeholder'   => 'ex. 5px',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Margin bottom</span>';
        $this->settings_text(
            array(
                "label"   => "Field margin <i>(bottom)</i>",
                'tooltip'    => esc_html__( 'A lower value makes the fields closer, usually handy on smaller forms when you need fields to be tighter together.', 'gf_stylespro' ),
                "name"          => $theme."_field_margin_bottom",
                'default_value' => '',
                'class'         => 'small field_margin_bottom auto_px',
            )
        );
        echo '</div>';

        echo '<hr><b>Border</b>';
        echo '<div class="adv_field"><span>Color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color',
                'name'          => $theme.'_field_border_color',
                'default_value' => '',
                'class'         => 'color adv_border_color field_border_color'
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Width</span>';
        $this->settings_text(
            array(
                'label'         => 'Border width',
                'name'          => $theme.'_field_border_width',
                'default_value' => '',
                'class'         => 'adv_border_width field_border_width auto_px',
                'placeholder'   => 'ex. 1px',
                'after_input'   => ' <i class="info_bottom">Multiple values for each side allowed</i> '
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Radius</span>';        
        $this->settings_text(
            array(
                'label'         => 'Radius',
                'name'          => $theme.'_field_border_radius',
                'default_value' => '',
                'class'         => 'small adv_border_radius field_border_radius auto_px',
            )
        );
        echo '</div>';
        

        echo '<div class="adv_field"><span>Style</span>';
        $this->settings_select(
            array(
                'label'         => 'Style',
                'name'          => $theme.'_field_border_style',
                'default_value' => '',
                'class'         => 'adv_border_style field_border_style',
                'choices'       => array(
                    array(  'label' => 'Default',   'value' => '' ),
                    array(  'label' => 'Solid',     'value' => 'solid' ),
                    array(  'label' => 'Dashed',    'value' => 'dashed' ),
                    array(  'label' => 'Dotted',    'value' => 'dotted' ),
                    array(  'label' => 'Double',    'value' => 'double' ),
                    array(  'label' => 'Ridge',     'value' => 'ridge' ),
                    array(  'label' => 'Inset',     'value' => 'inset' ),
                    array(  'label' => 'Outset',    'value' => 'outset' ),
                    array(  'label' => 'Groove',    'value' => 'groove' ),
                    array(  'label' => 'None',      'value' => 'none' ),
                )
            )
        );
        echo '</div>';

        echo '<hr><b>On Focus</b>';

        echo '<div class="adv_field"><span>Border color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color (focus)',
                'name'          => $theme.'_field_focus_border_color',
                'default_value' => '',
                'class'         => 'color adv_focus_border_color field_focus_border_color',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color (focus)',
                'name'          => $theme.'_field_focus_bg_color',
                'default_value' => '',
                'class'         => 'color adv_focus_bg_color field_focus_bg_color'
                
            )
        );
        echo '</div>';

        echo '<hr><b>Placeholder</b>';

        echo '<div class="adv_field"><span>Color</span>';
        $this->settings_text(
            array(
                'label'         => 'Color',
                'name'          => $theme.'_placeholder_color',
                'default_value' => '',
                'class'         => 'color adv_placeholder_color placeholder_color',
                'after_input'   => '<i>'.esc_html__('leave empty to automatically calculate it based on field color', 'gf_stylespro').'</i>',
            )
        );
        echo '</div>';


        echo '</div>
        </div>';
    }


    public function settings_adv_orn_styles($field) {
        $theme = $field['theme_name'];
        $label = $field['label'];
        echo '<a class="show_adv_styles" onclick="jQuery(this).parent().toggleClass(\'expanded\');">'.$label.'</a>
        <div class="adv_field_options_wrapper">
            <div class="adv_fields">';

        echo '<div class="adv_field"><span>Field icon color (Default)</span>';
        $this->settings_text(
            array(
                'tooltip'    => esc_html__( 'Default color for field icons <i>(does not affect radio/checkbox icons)</i>.<br>Icons for individial fields can be changed from field settings.', 'gf_stylespro' ),
                "label"   => "Field icon color",
                "type"    => "text",
                "class"   => "field_icon_color color",
                "name"    => $theme."_field_icon_color"
            )
        );
        echo '</div>';

        echo '<br>';

        echo '<div class="adv_field"><span>Advanced Radio/checkbox styles color</span>';
        $this->settings_text(
            array(
                "label"   => "Advanced Radio/checkbox styles color",
                'tooltip'    => esc_html__( 'Changes the colors for Radio/checkbox replacement styles; like, Toggle, iOS, Tick etc.', 'gf_stylespro' ),
                "type"    => "text",
                "class"   => "choice_style_color color",
                "name"    => $theme."_choice_style_color"
            )
        );
        echo '</div>';

        echo '<br>';

        echo '<div class="adv_field"><span>List highlight background color</span>';
        $this->settings_text(
            array(
                "label"   => "List highlight background color",
                'tooltip'    => esc_html__( 'Highlight color for selected items when using Frame and List styles on Checkboxes and Radio feilds.<br>Use a nice contrasting color with white, which is the default text color', 'gf_stylespro' ),
                "type"    => "text",
                "class"   => "o_custom_bg color",
                "after_input" => "<i class='info_bottom'> (only Frame and List styles are supported)</i>",
                "name"    => $theme."_o_custom_bg"
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>List highlight text color</span>';
        $this->settings_text(
            array(
                "label"   => "List highlight text color",
                'tooltip'    => esc_html__( 'Text color for selected items when using Frame and List styles on Checkboxes and Radio feilds.<br>Use a nice contrasting color with white, which is the default text color', 'gf_stylespro' ),
                "type"    => "text",
                "class"   => "o_custom_bg_text color",
                "after_input" => "<i class='info_bottom'> (only Frame and List styles are supported)</i>",
                "name"    => $theme."_o_custom_bg_text"
            )
        );
        echo '</div>';

        echo '</div>
        </div>';

    }


    public function settings_adv_btn_styles($field) {
        $theme = $field['theme_name'];
        $label = $field['label'];
        echo '<a class="show_adv_styles" onclick="jQuery(this).parent().toggleClass(\'expanded\');">'.$label.'</a>
        <div class="adv_field_options_wrapper">
            <h5>Advanced button styling</h5>
            <div class="adv_fields">
                <b>Size</b>';

        echo '<div class="adv_field"><span>Text Size</span>';
        $this->settings_text(
            /*
            Button background color
            */
            array(
                "label"   => "Font size",
                "class"   => "small adv_btn_font_size btn_font_size auto_px",
                'tooltip'    => esc_html__( 'Customize text size for form buttons like Next, Previous, Submit. Leave blank to use default.', 'gf_stylespro' ),
                "name"    => $theme."_btn_font_size",
                'placeholder'   => 'ex. 15px',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Padding</span>';
        $this->settings_text(
            array(
                'label'         => 'Padding',
                'name'          => $theme.'_btn_padding',
                'default_value' => '',
                'class'         => 'adv_btn_padding btn_padding auto_px',
                'placeholder'   => 'ex. 5px',
            )
        );
        echo '</div>';

        echo '<hr><b>Style</b>';
        echo '<div class="adv_field"><span>Text Color</span>';
        $this->settings_text(
            /*
            Button background color
            */
            array(
                "label"   => "Button text color",
                "class"   => "btn_color color",
                'tooltip'    => esc_html__( 'Customize text color for form buttons like Next, Previous, Submit. Leave blank to use default.', 'gf_stylespro' ),
                "name"    => $theme."_btn_color"
            )
        );
        echo '</div>';
        
        echo '<div class="adv_field"><span>Button background</span>';
        $this->settings_text(
            /*
            Button font color
            */
            array(
                "label"   => "Button background",
                "class"   => "btn_bg_color color",
                'tooltip'    => esc_html__( 'Customize background color for form buttons like Next, Previous, Submit. Leave blank to use default.', 'gf_stylespro' ),
                "name"    => $theme."_btn_bg_color"
            )

        );
        echo '</div>';


        echo '<hr><b>Border</b>';
        echo '<div class="adv_field"><span>Color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color',
                'name'          => $theme.'_btn_border_color',
                'default_value' => '',
                'class'         => 'color adv_btn_border_color btn_border_color'
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Width</span>';
        $this->settings_text(
            array(
                'label'         => 'Border width',
                'name'          => $theme.'_btn_border_width',
                'default_value' => '',
                'class'         => 'small adv_btn_border_width btn_border_width auto_px',
                'placeholder'   => 'ex. 1',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Radius</span>';        
        $this->settings_text(
            array(
                'label'         => 'Radius',
                'name'          => $theme.'_btn_border_radius',
                'default_value' => '',
                'class'         => 'small adv_btn_border_radius btn_border_radius auto_px',
            )
        );
        echo '</div>';
        
        echo '<div class="adv_field"><span>Style</span>';
        $this->settings_select(
            array(
                'label'         => 'Style',
                'name'          => $theme.'_btn_border_style',
                'default_value' => '',
                'class'         => 'adv_btn_border_style btn_border_style',
                'choices'       => array(
                    array(  'label' => 'Default',   'value' => '' ),
                    array(  'label' => 'Solid',     'value' => 'solid' ),
                    array(  'label' => 'Dashed',    'value' => 'dashed' ),
                    array(  'label' => 'Dotted',    'value' => 'dotted' ),
                    array(  'label' => 'Double',    'value' => 'double' ),
                    array(  'label' => 'Ridge',     'value' => 'ridge' ),
                    array(  'label' => 'Inset',     'value' => 'inset' ),
                    array(  'label' => 'Outset',    'value' => 'outset' ),
                    array(  'label' => 'Groove',    'value' => 'groove' ),
                    array(  'label' => 'None',      'value' => 'none' ),
                )
            )
        );
        echo '</div>';

        echo '<hr><b>On Hover</b>';
        echo '<div class="adv_field"><span>Text color</span>';
        $this->settings_text(
            array(
                'label'         => 'Text color (hover)',
                'name'          => $theme.'_btn_hover_color',
                'default_value' => '',
                'class'         => 'color adv_btn_hover_color btn_hover_color'
                
            )
        );
        echo '</div>';
    
        echo '<div class="adv_field"><span>Border color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color (hover)',
                'name'          => $theme.'_btn_hover_border_color',
                'default_value' => '',
                'class'         => 'color adv_btn_hover_border_color btn_hover_border_color',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color (hover)',
                'name'          => $theme.'_btn_hover_bg_color',
                'default_value' => '',
                'class'         => 'color adv_btn_hover_bg_color btn_hover_bg_color'
                
            )
        );
        echo '</div>';
        echo '</div>
        </div>';

    }

    public function settings_adv_btn_sbt_styles($field) {
        $theme = $field['theme_name'];
        $label = $field['label'];
        echo '<a class="show_adv_styles" onclick="jQuery(this).parent().toggleClass(\'expanded\');">'.$label.'</a>
        <div class="adv_field_options_wrapper">
            <h5>Customize next/submit button</h5>
            <div class="adv_fields">
            <p><i>If these settings are left empty, the main submit (Submit/Next page) button will look the same as the rest of the buttons on the form.</i></p>
            <hr>
                <b>Style</b>';

        echo '<div class="adv_field"><span>Text color</span>';
        $this->settings_text(
            array(
                'label'         => 'Text color',
                'name'          => $theme.'_btn_sbt_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_color btn_sbt_color'
                
            )
        );
        echo '</div>';
    
        echo '<div class="adv_field"><span>Border color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color',
                'name'          => $theme.'_btn_sbt_border_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_border_color btn_sbt_border_color',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color',
                'name'          => $theme.'_btn_sbt_bg_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_bg_color btn_sbt_bg_color'
                
            )
        );
        echo '</div>';

        echo '<hr><b>On Hover</b>';
        echo '<div class="adv_field"><span>Text color</span>';
        $this->settings_text(
            array(
                'label'         => 'Text color (hover)',
                'name'          => $theme.'_btn_sbt_hover_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_hover_color btn_sbt_hover_color'
                
            )
        );
        echo '</div>';
    
        echo '<div class="adv_field"><span>Border color</span>';
        $this->settings_text(
            array(
                'label'         => 'Border color (hover)',
                'name'          => $theme.'_btn_sbt_hover_border_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_hover_border_color btn_sbt_hover_border_color',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color (hover)',
                'name'          => $theme.'_btn_sbt_hover_bg_color',
                'default_value' => '',
                'class'         => 'color adv_btn_sbt_hover_bg_color btn_sbt_hover_bg_color'
                
            )
        );
        echo '</div>';

        echo '</div>
        </div>';

    }


    public function settings_adv_font_styles($field) {
        $theme = $field['theme_name'];
        $for  = $field['styles_for'];
        $label = $field['label'];

        echo '<a class="show_adv_styles" onclick="jQuery(this).parent().toggleClass(\'expanded\');">'.$label.'</a>
        <div class="adv_field_options_wrapper adv_font_'.$for.'">
            <h5>'. esc_html__( 'Advanced font styling', 'gf_stylespro' ) .'</h5>
            <div class="adv_fields">';

        if ( $for !== "label") {

            echo '<div class="adv_field"><span>'. esc_html__( 'Font', 'gf_stylespro' ) .'</span>';
            $this->settings_select(
                array(
                    'name'          => "{$theme}_{$for}_font",
                    'default_value' => '',
                    'class'         => "adv_font {$for}_font",
                    'choices'       => array(
                        array(  'label' => esc_html__( 'Default', 'gf_stylespro' ),   'value' => '' ),
                        array(  'label' => esc_html__( 'Primary', 'gf_stylespro' ),   'value' => 'preset-font_pri' ),
                        array(  'label' => esc_html__( 'Secondary', 'gf_stylespro' ), 'value' => 'preset-font_sec' ),
                    )
                )
            );
            echo '</div>';

        }

        echo '<div class="adv_field"><span>'. esc_html__( 'Font size', 'gf_stylespro' ) .'</span>';
        $this->settings_text(
            array(
                'name'          => "{$theme}_{$for}_font_size",
                'default_value' => '',
                'class'         => "small adv_font_size {$for}_font_size auto_px",
                'placeholder'   => 'ex. 15px',
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>'. esc_html__( 'Color', 'gf_stylespro' ) .'</span>';
        $this->settings_text(
            array(
                'name'          => "{$theme}_{$for}_color",
                'default_value' => '',
                'class'         => "color_presets adv_font_color {$for}_color font_color"
            )
        );
        echo '</div>';

        echo '<div class="adv_field inline_options icon_options"><span>'. esc_html__( 'Styles', 'gf_stylespro' ) .'</span>';
        $this->settings_checkbox(
            array(
                'name'          => "{$theme}_{$for}_font_styles",
                'default_value' => '',
                'class'         => 'adv_font_styles',
                'type'    => 'checkbox',
                'horizontal'    => true,
                'choices' => array(
                    array(
                        'label' => " ",
                        'name'  => "{$theme}_{$for}_font_italic",
                        'class'  => "adv_font_italic {$for}_font_italic",
                    ),
                    array(
                        'label' => " ",
                        'name'  => "{$theme}_{$for}_font_underline",
                        'class'  => "adv_font_underline {$for}_font_underline",
                    ),
                ),
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>'. esc_html__( 'Weight', 'gf_stylespro' ) .'</span>';
        $this->settings_select(
            array(
                'name'          => "{$theme}_{$for}_font_weight",
                'default_value' => '',
                'class'         => "adv_font_weight {$for}_font_weight",
                'choices'       => array(
                    array(  'label' => 'Default',   'value' => '' ),
                    array(  'label' => 'Normal', 'value' => 'normal' ),
                    array(  'label' => 'Bold', 'value' => 'bold' ),
                    array(  'label' => '100',   'value' => '100' ),
                    array(  'label' => '200',   'value' => '200' ),
                    array(  'label' => '300',   'value' => '300' ),
                    array(  'label' => '400',   'value' => '400' ),
                    array(  'label' => '500',   'value' => '500' ),
                    array(  'label' => '600',   'value' => '600' ),
                    array(  'label' => '700',   'value' => '700' ),
                    array(  'label' => '800',   'value' => '800' ),
                    array(  'label' => '900',   'value' => '900' ),
                )
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>Background color</span>';
        $this->settings_text(
            array(
                'label'         => 'Background color',
                'name'          => "{$theme}_{$for}_bg_color",
                'default_value' => '',
                'class'         => "color adv_bg_color {$for}_bg_color",            
            )
        );
        echo '</div>';

        echo '<div class="adv_field inline_options icon_options adv_text_align"><span>'. esc_html__( 'Align', 'gf_stylespro' ) .'</span>';
        $this->settings_radio(
            array(
                'name'          => "{$theme}_{$for}_text_align",
                'default_value' => '',
                'class'         => "adv_text_align {$for}_text_align",
                'type'    => 'radio',
                'horizontal'    => true,
                'choices' => array(
                    array(
                        'label' => esc_html__( 'Auto', 'gf_stylespro' ),
                        'value'  => '',
                        'class'  => "radio_icon adv_text_align_auto",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'left',
                        'class'  => "radio_icon adv_text_align_left",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'center',
                        'class'  => "radio_icon adv_text_align_center",
                    ),
                    array(
                        'label' => '',
                        'value'  => 'right',
                        'class'  => "radio_icon adv_text_align_right",
                    ),
                ),
            )
        );
        echo '</div>';
        
        echo '<div class="adv_field"><span>'.esc_html__( 'Padding', 'gf_stylespro' ).'</span>';
        $this->settings_text(
            array(
                'name'          => "{$theme}_{$for}_padding",
                'default_value' => '',
                'placeholder'   => '5px   (or)   5px 5px...',
                'class'         => "adv_padding {$for}_padding auto_px",
            )
        );
        echo '</div>';

        echo '<div class="adv_field"><span>'.esc_html__( 'Margin bottom', 'gf_stylespro' ).'</span>';
        $this->settings_text(
            array(
                'name'          => "{$theme}_{$for}_margin_bottom",
                'default_value' => '',
                'class'         => "small adv_margin_bottom {$for}_margin_bottom auto_px",
            )
        );
        echo '</div>';

        echo '</div>
        </div>';
    }

    /* Adds support for CSS classes as used by Gravity PDF by converting Styles Pro classes into the older CSS Ready Classes */
    function stylespro_gfpdf_add_field_css_classes( $data, $entry, $settings, $form ) {

        // Do nothing, if no theme is selected
        $theme = self::get_styles_pro_theme( $form );
        if ( empty($theme) || $theme == "none" ){
            return $data;
        }

        $prev_class = "";
        foreach ($data['form']['fields'] as $key => $field) {
            
            $add_class = "";

            if ( isset( $field['gfStylesPro'] ) ) {

                $sp_classes = explode( " ", $field['gfStylesPro'] );

                /* Halves */
                if ( in_array("gf_half", $sp_classes) ) {
                    $add_class = 'gf_left_half';
                    
                    /* Override based on previously added class */
                    if ( $prev_class == "gf_left_half" || in_array("gf_right", $sp_classes) )
                        $add_class = "gf_right_half";
                
                }

                /* Thirds */
                if ( in_array("gf_third", $sp_classes) ) {
                    $add_class = 'gf_left_third';

                    /* Override based on previously added class */
                    if ( $prev_class == "gf_left_third" )
                        $add_class = "gf_middle_third";
                    
                    if ( $prev_class == "gf_middle_third" || in_array("gf_right", $sp_classes) )
                        $add_class = "gf_right_third";
                
                }

                /* Quarters */
                if ( in_array("gf_quarter", $sp_classes) ) {
                    $add_class = 'gf_first_quarter';

                    /* Override based on previously added class */
                    if ( $prev_class == "gf_first_quarter" )
                        $add_class = "gf_second_quarter";
                
                    if ( $prev_class == "gf_second_quarter" )
                        $add_class = "gf_third_quarter";
                
                    if ( $prev_class == "gf_third_quarter" || in_array("gf_right", $sp_classes) )
                        $add_class = "gf_fourth_quarter";
                
                }

            }

            $sp_classes[] = $add_class;

            $data['form']['fields'][$key]['cssClass'] = $field['cssClass'] . " " . implode(" ", $sp_classes);
            
            $prev_class = $add_class;
        } // Fields loop

        return $data;
    }


    /* Handles Gravity Flow */
    function maybe_gravityflow_unlink_styles_pro() {

        $gflow_setting = $this->get_plugin_setting('gravity_flow_form_style');
        
        if ( $gflow_setting == 'none') {
            
            add_filter( 'gf_stylespro_theme_filter', function() { return "none"; }  );
            
            return;

        }

        add_action( 'gform_register_init_scripts', function ( $form ) use ( $gflow_setting ) {
            
            if ( class_exists('Gravity_Flow_Entry_Editor') ) {
                
                $theme = self::get_styles_pro_theme($form);
        
                if ( ! empty($theme) && $theme != "none" ) {
                    if ($gflow_setting == 'full')
                        $script = 'jQuery("#gform_wrapper_'. $form['id'] .'").closest("form").addClass("gf_stylespro '. $theme .'");';
                    else
                        $script = 'jQuery("#gform_wrapper_'. $form['id'] .'").parent().addClass("gf_stylespro '. $theme .'");';

                    GFFormDisplay::add_init_script( $form['id'], 'gfsp_add_classes_to_gflow', GFFormDisplay::ON_PAGE_RENDER, $script );    
                }
                
            }
    
        }, 10, 1);
        
    
    }

    
    function jetpack_lazyload_exclude_label_img_class( $classes ) {
        $classes[] = 'gfsp_label_img';
        return $classes;
    }
    

    /**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_menu_icon() {

		return $this->get_base_url() . "/icons/stylesproicon.svg";

    }
    

    /*
    * Create, Read and Handle options for all themes
    */
    private static function get_all_theme_options() {

        // Get CSS file headers
        $gfsp_themes = GFSPCommon::get_themes();

        $all_themes_options[] =
        array(
            "id" => "theme_select",
            "fields" =>
                array(
                    array(
                    "label"   => "Select a theme to customize",
                    "type"    => "select",
                    "onchange" => "toggleTheme( jQuery( this ).val() )",
                    "name"    => "theme",
                    "class"     => "toggle_theme",
                    "choices" => $gfsp_themes
                    )
                )
        );


        if ( is_array( $gfsp_themes ) || is_object( $gfsp_themes ) )
        {
            
            foreach ( $gfsp_themes as $key => $theme ) {

                $theme_name = $theme['value'];
                
                $font = explode( "/", $theme['font'] );
                $font_label = explode( "/", $theme['font_label'] );

                $font_size = isset( $font[0] ) ? $font[0] : '';
                $font_name = isset( $font[1] ) ? $font[1] : '';
                $font_color = isset( $font[2] ) ? $font[2] : '';

                $font_label_size = isset( $font_label[0] ) ? $font_label[0] : '';
                $font_label_name = isset( $font_label[1] ) ? $font_label[1] : '';
                $font_label_color = isset( $font_label[2] ) ? $font_label[2] : '';
                            
                $fields_array =

                array(
                    /*
                    *   Primary Font (Label Font)
                    */
                    array(
                        'label'         => esc_html__( 'Label Font (base)', 'gf_stylespro' ),
                        'type'          => 'select',
                        "name"          => $theme_name."_label_font",
                        "class"         => 'label_font font_pri',
                        'default_value' => $font_label_name,
                        'data-default'  => $font_label_name,
                        'choices'       => GFSPCommon::get_font_choices(),
                    ),

                    array(
                        "label"   => "Size",
                        "type"    => "text",
                        "tooltip" => "Base font size determines several relative design elements on the form, like icons size, spacing etc.",
                        "class"   => "base_font_size auto_px",
                        "name"    => $theme_name."_base_font_size",
                        "default_value" => $font_label_size,
                        "data-default" => $font_label_size,
                    ),

                    array(
                        "label"   => "Color",
                        "type"    => "text",
                        "class"   => "label_font_color color",
                        "name"    => $theme_name."_label_font_color",
                        "default_value" => $font_label_color,
                        "data-default" => $font_label_color,
                    ),

                    array(
                        "label"   => "Custom Label Font",
                        "type"    => "text",
                        "placeholder"    => "'My webkit font', Fallback font",
                        "class"   => "label_font_custom medium font_pri_custom",
                        "name"    => $theme_name."_label_font_custom",
                    ),

                    array(
                        "type"    => "checkbox",
                        "class"   => "google_font",
                        "name"  => $theme_name."_label_font_load_cb",
                        "choices" =>
                            array(
                                array(
                                    "label" => "Don't load the font",
                                    "tooltip" => "Useful only when the selected Google font is already loaded on the page. If in doubt, leave unchecked.",
                                    "class" =>  "label_font_load",
                                    "name"  => $theme_name."_label_font_load"
                                )
                            )
                    ),


                                    
                    /*
                    *   Secondary Font (Field Font)
                    */
                    array(
                        'label'         => esc_html__( 'Field Font', 'gf_stylespro' ),
                        'type'          => 'select',
                        "name"    => $theme_name."_font",
                        'default_value' => $font_name,
                        'data-default' => $font_name,
                        'class'         => 'font font_sec',
                        'choices'       => GFSPCommon::get_font_choices(),
                    ),

                    array(
                        "label"   => "Size",
                        "type"    => "text",
                        "class"   => "font_size auto_px",
                        "name"    => $theme_name."_font_size",
                        "default_value" => $font_size,
                        "data-default" => $font_size,
                    ),
                    
                    array(
                        "label"   => "Color",
                        "type"    => "text",
                        "class"   => "font_color color",
                        "default_value" => $font_color,
                        "data-default" => $font_color,
                        "name"    => $theme_name."_font_color"
                    ),

                    array(
                        "label"   => "Custom Field Font",
                        "type"    => "text",
                        "placeholder"    => "'My webkit font', Fallback font",
                        "class"   => "font_custom medium font_sec_custom",
                        "name"    => $theme_name."_font_custom",
                    ),
                    
                    array(
                        "type"    => "checkbox",
                        "class"   => "google_font",
                        "name"  => $theme_name."_font_load_cb",
                        "choices" =>
                            array(
                                array(
                                    "label" => "Don't load the font",
                                    "tooltip" => "Useful only when the selected Google font is already loaded on the page. If in doubt, leave unchecked.",
                                    "class" =>  "font_load",
                                    "name"  => $theme_name."_font_load"
                                )
                            )
                    ),



                    /* 
                    Advanced Label Styling
                    */
                    array(
                        'label' => 'Label styling options',
                        'type'  => 'adv_font_styles',
                        'name'  => $theme_name.'_adv_label_styles',
                        'theme_name' => $theme_name,
                        'styles_for' => 'label',
                    ),

                    /* 
                    Advanced Field Styling
                    */
                    array(
                        'label' => 'Advanced field styling',
                        'type'  => 'adv_field_styles',
                        'name'  => $theme_name.'_adv_field_styles',
                        'theme_name' => $theme_name
                    ),

                    /* 
                    Advanced Description Styling
                    */
                    array(
                        'label' => 'Description styling options',
                        'type'  => 'adv_font_styles',
                        'name'  => $theme_name.'_adv_desc_styles',
                        'theme_name' => $theme_name,
                        'styles_for' => 'desc',
                    ),

                    /* 
                    Advanced Lists and Icons Styling
                    */
                    array(
                        'label' => 'Advanced lists and icons styling',
                        'type'  => 'adv_orn_styles',
                        'name'  => $theme_name.'_adv_orn_styles',
                        'theme_name' => $theme_name
                    ),

                    /* 
                    Advanced Button Styling
                    */
                    array(
                        'label' => 'Advanced button styling',
                        'type'  => 'adv_btn_styles',
                        'name'  => $theme_name.'_adv_btn_styles',
                        'theme_name' => $theme_name
                    ),

                    array(
                        'label' => 'Main button styling',
                        'type'  => 'adv_btn_sbt_styles',
                        'name'  => $theme_name.'_adv_btn_sbt_styles',
                        'theme_name' => $theme_name
                    ),

                    
                    array(
                        "label"   => "Validation color",
                        "type"    => "text",
                        "class"   => "validation_color color",
                        "name"    => $theme_name."_validation_color",
                    ),


                    array(
                        "label"   => "Validation background",
                        "type"    => "text",
                        "class"   => "validation_bg_color color",
                        "name"    => $theme_name."_validation_bg_color",
                    ),

                    array(
                        "label"   => esc_html__( 'Form Padding', 'gf_stylespro' ),
                        "placeholder"   => "50px  (or)  50px 60px...",
                        "type"    => "text",
                        "class"   => "wr_padding auto_px",
                        "name"    => $theme_name."_wr_padding",
                    ),

                    /*
                    Background
                    */
                    array(
                        'label'      => esc_html__( 'Form Background', 'gf_stylespro' ),
                        'type'       => 'radio',
                        'horizontal' => true,
                        'name'       => $theme_name.'_background',
                        'class'      => 'background',
                        'data-theme' => $theme_name,
                        'tooltip'    => esc_html__( 'Form background settings.', 'gf_stylespro' ),
                        'default_value' => 'default',
                        'onchange'   => 'toggleBgOption("'.$theme_name.'", jQuery(this).val() )',
                        'choices'    =>
                            array(
                                array(
                                    'tooltip' => esc_html__( 'Some themes may include backgrounds, which would be overridden by other options. You may use the default theme background by using this option', 'gf_stylespro' ),
                                    'label' => esc_html__( 'Default', 'gf_stylespro' ),
                                    'value' => 'default'
                                ),
                                array(
                                    'label' => esc_html__( 'None/transparent', 'gf_stylespro' ),
                                    'value' => 'none'
                                ),
                                array(
                                    'label' => esc_html__( 'Color', 'gf_stylespro' ),
                                    'value' => 'color'
                                ),
                                array(
                                    'label' => esc_html__( 'Image', 'gf_stylespro' ),
                                    'value' => 'image'
                                )
                            )
                    ),

                    array(
                        "label"   => "Background Color",
                        "type"    => "text",
                        "class"   => "bg_color color",
                        "name"    => $theme_name."_bg_color",
                        "default_value" => "#fff",
                        "data-default" => "#fff"
                    ),
                    
                    array(
                        "label"   => "Background Image",
                        "type"    => "text",
                        "class"   => "bg_image",
                        "name"    => $theme_name."_bg_image",
                        "after_input" => '<button class="media-button button">Select image</button>',
                    ),

                    array(
                        "label"   => "Background size",
                        "type"    => "select",
                        "class"   => "bg_size",
                        "name"    => $theme_name."_bg_size",
                        'choices'       => array(
                            array(  'label' => 'Auto',   'value' => '' ),
                            array(  'label' => 'Contain', 'value' => 'contain' ),
                            array(  'label' => 'Cover', 'value' => 'cover' ),
                        )
                    ),

                    array(
                        "type"    => "textarea",
                        "class"   => "save_css",
                        "style"   =>  "display:none!important",
                        "name"    => $theme_name."_theme_css"
                    ),

                );

                $desc_script = '';
                if ( $theme['scripts'] ) {

                    $scripts_option = array(
                    array(
                        "type"    => "checkbox",
                        "name"  => $theme_name."_scripts_load",
                        "choices" =>
                            array(
                                array(
                                    "label" => "Enable additional sctipts",
                                    "tooltip" => "UI/UX enhancements",
                                    "name"  => $theme_name."_scripts_load"
                                )
                            )
                    ),
                    array(
                        "type"    => "hidden",
                        "name"  => $theme_name."_scripts",
                        "default_value" => preg_replace("/[^a-z-_]/i", "", $theme['scripts']), // Keep it simple and secure
                        "class" => 'additional_scripts',
                        /**
                         * The value is only fetched once, once the settings are saved. We need to reset it via JS on page load, in case it's changed
                         * */ 
                        "data-value" => preg_replace("/[^a-z-_]/i", "", $theme['scripts']) //
                        )
                    );

                    $fields_array = array_merge($scripts_option, $fields_array);
                    $desc_script = "<br><br><div class='info enhanced_features'><img class='enhanced_features_icon' src='".plugin_dir_url( __FILE__ )."icons/enhanced-features.png'>This theme includes additional scripts <em><strong>({$theme['scripts_desc']})</strong></em> that can be enabled to enhance the form experience. Please learn more about this feature <a target='_blank' href='https://gravitystylespro.com/docs/enhanced-theme-features/'>here</a>, before using it.</div>";
                }

                $all_themes_options[] =
                array(
                    "title" => $theme['label'],
                    "class" => "thm " . $theme_name,
                    "id"    =>  $theme_name,
                    "description"   => $theme['desc'] . ' <span class="theme_additional">theme slug: '.$theme_name.' | <a href="#" onclick="resetToDefaults(\'' . $theme_name . '\',\'' . $theme['label'] . '\')">reset theme changes</a></span> <br><br>You may use any Google Font from the lists (<a target="_blank" href="https://fonts.google.com/">preview Google Fonts</a>). Please note that GF Styles Pro will only load fonts on pages which use a form with this theme, ensuring best practices for speed.'. $desc_script,
                    "fields" => $fields_array 
                );


            } // foreach $gfsp_themes

            // Add None option to the theme list for Default Theme select options
            array_unshift ($gfsp_themes, array("label" => "None", "value" => "") );

            $all_themes_options[] =
            array(
                "title"  => "Universal Settings",
                "fields" =>
                array(
                    array(
                        "label"   => "Default theme",
                        "type"    => "select",
                        "name"    => "default_theme",
                        "tooltip" => "All forms will use this theme by default, unless overridden from Form Settings",
                        "choices" => $gfsp_themes
                    ),
                    array(
                        "label"   => "Custom CSS",
                        "tooltip" => "Add your custom CSS overrides here",
                        "type"    => "toggle",
                        "name"    => "enable_css",
                        "choices" =>
                            array(
                                array(
                                    "label" => "Enabled",
                                )
                            )
                    ),
                    array(
                        "type"    => "textarea",
                        "name"    => "gfsp_custom_css",
                        "class"   => "medium merge-tag-support mt-position-right"
                    ),
                    array(
                        "label"   => "Reinforce customization",
                        "type"    => "toggle",
                        "name"    => "reinforce_styles",
                        "id"    => "reinforce_styles",
                        "choices" =>
                        array(
                            array(
                                    "tooltip" => "Sometimes useful for avoiding styling conflicts with themes and other plugins. Leaving it unchecked allows for writing easier custom CSS.",
                                    "label" => "Reinforce styles",
                                )
                            )
                    ),
                    array(
                        "label"   => "CSS compatibility (Legacy only)",
                        "tooltip" => "Useful for avoiding stubborn styling conflicts in some themes.",
                        "type"    => "toggle",
                        "name"    => "remove_default_wrapper_class",
                        "choices" =>
                        array(
                            array(
                                    "label" => "Remove default Gravity Forms wrapper class",
                                )
                            )
                    ),
                    array(
                        "label"   => "Gravity Flow forms (edit)",
                        "type"    => "select",
                        "name"    => "gravity_flow_form_style",
                        "class"   => (!class_exists('Gravity_Flow'))?"hide_field":"",
                        "default_value" => "entry",
                        "tooltip" => "<h6>Apply styles to the following extent</h6>Due to the nature of Gravity Flow view/edit entry form, support for styles will not be the same as regular Gravity Forms. If you want to turn off Styles Pro for those forms, you can do so here.",
                        "choices" => array(
                            array("label" => "None",                 "value" => "none"),
                            array("label" => "Entry only",           "value" => "entry"),
                            array("label" => "Entry &amp; Sidebar",  "value" => "full"),
                        )
                    ),
                    array(
                        "type"    => "hidden",
                        "value"     => GF_STYLES_PRO_ADDON_VERSION,
                        "name"    => "last_saved_ver"
                    ),
                    array(
                        "type"    => "save",
                        "value"   => "Update All Settings"
                    )
                )
            );

            return $all_themes_options;

        }
    }



    /**
     * Removes fields with empty values before saving settings
     * @since 2.7
     */
    function pre_save_clean_empty_options( $new_values, $old_values ) {

        $clean_values = array();

        if ( is_array($new_values) ) {
            foreach ($new_values as $key => $value) {
                if ( $value !== "" && $value !== "0" ) {
                    $clean_values[$key] = $value;
                }
            }
        } else {
            $clean_values = $new_values;
        }

        return $clean_values;
    
    }



    /**
     * Adds script to the settings page that maps older values to new values
     * @since 2.7
     */
    function settings_page_content() {

        // Do nothing if last saved setings are compatible
        if ( $this->get_plugin_setting("last_saved_ver") !== null ) {
            return;
        }

        $themes = GFSPCommon::get_themes();

        $map_vals = [];
        if ( is_array($themes) || is_object($themes) ) {
            foreach ( $themes as $key => $theme_arr ) {

                $theme = $theme_arr['value'];

                $theme_base_font_size_old = $this->get_plugin_setting("{$theme}_label_font_size");
                $theme_base_font_size_new = $this->get_plugin_setting("{$theme}_base_font_size");

                if ($theme_base_font_size_new === null && $theme_base_font_size_old) {
                    $map_vals["{$theme}_base_font_size"] = $theme_base_font_size_old;
                }

                $theme_padding_v_old = $this->get_plugin_setting("{$theme}_btn_v_padding");
                $theme_padding_h_old = $this->get_plugin_setting("{$theme}_btn_h_padding");
                $theme_padding_new = $this->get_plugin_setting("{$theme}_btn_padding");

                if ($theme_padding_new === null &&  ($theme_padding_v_old && $theme_padding_h_old) ) {
                    $map_vals["{$theme}_btn_padding"] = "{$theme_padding_v_old}px {$theme_padding_v_old}px";
                }

                $btn_bg_old = $this->get_plugin_setting("{$theme}_btn_bg");
                $btn_bg_new = $this->get_plugin_setting("{$theme}_btn_bg_color");

                if ($btn_bg_new === null && $btn_bg_old ) {
                    $map_vals["{$theme}_btn_bg_color"] = $btn_bg_old;
                }
                
                $theme_bold_old = $this->get_plugin_setting("{$theme}_font_bold");
                $theme_weight_new = $this->get_plugin_setting("{$theme}_field_font_weight");

                if ($theme_weight_new === null && $theme_bold_old) {
                    $map_vals["{$theme}_field_font_weight"] = $theme_bold_old ? "bold" : "";
                }

                
                $theme_lable_bold_old = $this->get_plugin_setting("{$theme}_label_font_bold");
                $theme_lable_weight_new = $this->get_plugin_setting("{$theme}_label_font_weight");

                if ($theme_lable_weight_new === null && $theme_lable_bold_old) {
                    $map_vals["{$theme}_label_font_weight"] = $theme_lable_bold_old ? "bold" : "";
                }
            }

            ?>
            <script type="text/javascript">

                var gfspMapVals = <?php echo json_encode($map_vals); ?>;

                for (var id in gfspMapVals) {
                    var value = gfspMapVals[id];
                    var field = document.getElementById(id);
                    if ( field ) {
                        field.value = value;
                    }
                }

                </script>
            <?php
        }
    }


}   // class StylesPro








/*
# --- FUNCTIONS FOR CLASS -------------------------------------------
*/


add_filter( 'gform_tooltips', 'gf_stylespro_tooltips' );

function gf_stylespro_tooltips( $tooltips ) {
	/*
	* Register tooltip for Field Settings
	*/
	$tooltips['gf_stylespro_value'] = "<h6>" . __( "Advanced Styles", "gf_stylespro" ) ."</h6>" . __( "Choose advanced styling options for this field.", "gf_stylespro");
	$tooltips['gf_stylespro_custom_icon'] = "<h6>" . __( "Custom Icons", "gf_stylespro" )  ."</h6>". __( "You can use additional icons by adding class names here. Make sure the icons stylesheet and fonts are loaded. <a href='https://gravitystylespro.com/docs/custom-icon-fonts/' target='_blank'>Learn more</a><br>If you want to use an image as a custom icon, instead of a font character, use the option below. ", "gf_stylespro");
	$tooltips['gf_stylespro_e_nullborder'] = "<h6>" . __( "Null border", "gf_stylespro" )  ."</h6>". __( "Creates an invisible border around the label. When checked, this space is filled with the background color, making it a frame", "gf_stylespro");
    
    return $tooltips;
}



/*
* Add a wrapper for GravityView plugin
*/
add_action('gravityview/edit-entry/render/before', 'gfsp_gravityview_wrapper', 10, 1);
function gfsp_gravityview_wrapper( $instance ) {

    $default_theme = '';
    $theme = '';

    if ( class_exists('StylesPro') ) {
        $StylesPro = StylesPro::get_instance();
        $gfsp_plugin_settings = $StylesPro->get_plugin_settings();

        // Get default theme
        if ( array_key_exists('default_theme', $gfsp_plugin_settings) )
            $default_theme = $gfsp_plugin_settings['default_theme'];
    }
    
    $theme = $default_theme;

    if ( ! empty ( $instance->form['gf_stylespro']['theme'] ) )
        $theme = $instance->form['gf_stylespro']['theme'];
    
    else if ( ! empty ( $instance->form['gf_stylespro_theme'] ) )
        $theme = $instance->form['gf_stylespro_theme'];
    
    if ( ! empty($theme) && $theme != "none" ) {
        echo '<div class="gf_stylespro ' . $theme . '">';

        add_action('gravityview/edit-entry/render/after', function() {
            echo '</div>';
        });

    }
}

/* Adds support for Merge Tag modifier: image.size */
add_filter( 'gform_merge_tag_filter', 'gf_stylespro_merge_tags', 10, 5 );

function gf_stylespro_merge_tags( $output, $merge_tag, $modifier, $field, $raw_value ) {

    // Do nothing if modifier not present.
    if ( $merge_tag == 'all_fields' || strpos($modifier, "image") === false || ( $modifier != "image" && strpos($modifier, "image.") != 0 ) ) {
        return $output;
    }

    $output_style_icon = "";
    $output_style_image = "";
    $output_class = "";

    // Explode in case size is present. Eg. image.size
    $modifier_expand = explode(".", $modifier);

    $modifier_size = isset($modifier_expand[1]) ? $modifier_expand[1] : false;
    $modifier_size_int = 0;

    // If known class names for size aren't recognized, take the value as integer
    if ($modifier_size != "small" || $modifier_size != "medium" || $modifier_size != "large") {
        $modifier_size_int = (int) $modifier_size;
    }

    if ($modifier_size && $modifier_size_int) {
        $output_style_image = "style=\"width: auto; height: auto; max-width: {$modifier_size_int}px; max-height: {$modifier_size_int}px;\" height=\"$modifier_size_int\"";
        $output_style_icon  = "font-size: {$modifier_size_int}px;";
    } else if ( $modifier_size && !$modifier_size_int) {
        $output_class = " ornament_size_$modifier_size"; // add space behind
    }

    $choices_selected = array();

    $field_type = "";

    if ($field->type == 'radio'             ||
        $field->type == 'product'           && $field->inputType == 'radio' ||
        $field->type == 'option'            && $field->inputType == 'radio' ||
        $field->type == 'poll'              && $field->inputType == 'radio' ||
        $field->type == 'post_tags'         && $field->inputType == 'radio' ||
        $field->type == 'post_custom_field' && $field->inputType == 'radio' ) {
            $field_type = 'radio';
        }
    else
    if ($field->type == 'checkbox'          ||
        $field->type == 'option'            && $field->inputType == 'checkbox' ||
        $field->type == 'poll'              && $field->inputType == 'checkbox' ||
        $field->type == 'post_tags'         && $field->inputType == 'checkbox' ||
        $field->type == 'post_custom_field' && $field->inputType == 'checkbox' ) {
            $field_type = 'checkbox';
        }

    if ( $field_type == 'checkbox' ) {
        

        // When Merge Tag for a specific option is used
        if ( strpos($merge_tag, '.') > -1 ) {
            // Gets array position for choices. Take 3 from 2.3 (Field 2, Input 3)
            // Subtract by 1 because choices array index starts at 0
            $i = array();
            $i = explode(".", $merge_tag);
            $key = (int) $i[1] - 1;

            // Confirm that the output exists
            if ( $field->choices[$key]['text'] == $output && $field->choices[$key]['value'] == $raw_value[$merge_tag] ) {
                $choices_selected[] = $key;
            }
        }
        else {
            foreach ( $field->inputs as $input ) {
                $input_value = rgar( $raw_value, $input['id'] );
                if ( $input_value ) {
                    // Gets array position for choices. Take 3 from 2.3 (Field 2, Input 3)
                    // Subtract by 1 because choices array index starts at 0 
                    $choices_selected[] = (int) explode(".", $input['id'])[1] - 1;
                }
            }
        }

    } elseif ( $field_type == 'radio' ) {

        $raw_value = explode("|", $raw_value)[0];
        foreach ($field->choices as $key => $choice) {
            // Confirm that the output exists and it's the right option
            if ( $choice['text'] == $output && $choice['value'] == $raw_value ) {
                $choices_selected[] = (int) $key;
                break;
            }
        }
    }

    
    $new_output = '';

    foreach ($choices_selected as $i) {
        $gf_ornament = rgar($field->choices[$i], 'spOrnament');
        /* Array indices:
        * 0    Type
        * 1    Data
        * 2    Iconset
        * 3    Color
        */

        if ( $gf_ornament ) {
            $gf_icn_img = explode('|', $gf_ornament);
            
            // if image
            if ( $gf_icn_img[0] == 'img' ) {
                $ornamant = "<span class='gf_stylespro_ornament sp_image{$output_class}'><img {$output_style_image} src='$gf_icn_img[1]'></span>";
            }

            // if icon
            if ( $gf_icn_img[0] == "icn" ) {
                // if has color
                $color = '';
                if ( isset($gf_icn_img[3]) )
                    $color = "color:" . $gf_icn_img[3] . ";";
                
                $ornamant = "<span class='gf_stylespro_ornament sp_icon{$output_class}'><i style='{$color}{$output_style_icon}' class='" . $gf_icn_img[1] . "'></i></span>";
            }

            $new_output .= " " . $ornamant;
        }

    }
 
    return $new_output ? $new_output : $output;
}
