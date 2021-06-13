
// Get color with added transparency
function getColorOpacity( color,  opacity ) {
    if ( typeof color === "undefined" || color.length === 0 || color == 'inherit' ) {
        return "";
    }

    if ( typeof colorCalculator === "undefined" ) {
        window.colorCalculator = new Colors();
    }

    colorCalculator.setColor( color );
	var rgb = colorCalculator.colors.RND.rgb;
    var alpha = colorCalculator.colors.alpha;
    
	return 'rgba(' + rgb['r'] + ', ' + rgb['g'] + ', ' + rgb['b'] + ', ' + (alpha*opacity).toFixed(2) + ')';
}



function Theme( slug ) {
    this.slug = slug,
    this.reinforce = jQuery('#_gform_setting_reinforce_styles').is(':checked') ? ' !important;' : ';';
    this.css = "",
    this.$el = jQuery('#' + this.slug),

    this.presets = {
        /*  Dual Ternary operator:
                Get Custom font if available.
                Remove /Native from string if native font
                Add quotes if GFonts
        */
        font_pri:   (this.$el.find('.font_pri').val() == 'custom/Native')
                    ? this.$el.find('.font_pri_custom').val()
                    : (this.$el.find('.font_pri').val().indexOf("/Native") < 0)
                        ? '"' + this.$el.find('.font_pri').val() + '"'
                        : this.$el.find('.font_pri').val().split("/Native")[0],


        font_sec:   (this.$el.find('.font_sec').val() == 'custom/Native')? this.$el.find('.font_sec_custom').val() : (this.$el.find('.font_sec').val().indexOf("/Native") < 0)? '"' + this.$el.find('.font_sec').val() + '"' : this.$el.find('.font_sec').val().split("/Native")[0],
    },

    this.getStyles = function( selector, styles ) {
        if ( styles.length > 0 ) {
            return selector.replace(/theme_slug/g, this.slug) + "{" + styles + "}\n";
        }    
        // Else
        return "";
    },

    this.getCss = function() {

        for (const selector in this.cssSet) {
            if (this.cssSet.hasOwnProperty(selector)) {

                const fields = this.cssSet[selector];

                this.css += this.getStyles ( selector, this.getDeclaractionBlock( fields ) );

            }
        }
        
        this.css += this.getChoiceStyles();


        // Add Button Shade
        // var shade_name = this.$el.find('.adv_shadow_box_wrapper input:checked').val();
        // this.css += this.getButtonShade( shade_name );

        return this.css;
    },

    this.getCss = function() {

        for (const selector in this.cssSet) {
            if (this.cssSet.hasOwnProperty(selector)) {

                const fields = this.cssSet[selector];

                this.css += this.getStyles ( selector, this.getDeclaractionBlock( fields ) );

            }
        }

        var css = this.getDeclaractionBlock( this.cssVars );
        
        this.css += "."+this.slug+"_wrapper{"+css+"}";
        // this.css += this.getChoiceStyles();


        // Add Button Shade
        // var shade_name = this.$el.find('.adv_shadow_box_wrapper input:checked').val();
        // this.css += this.getButtonShade( shade_name );

        return this.css;
    },

    /**
     * 
     * @param {string} settingName  CSS classname for the setting field
     * @param {string} property     Name of the CSS property to create rule with
     * @param {string} suffix       Unit for the value, or value itself in case of a checkbox
     * 
     * @returns {string} CSS delaration
     */
    this.getDeclaration = function( property, settingName ) {

        var $settingEl = "";
        var value = "";
        var calc = "";
        var preset_key = "";

        // TODO: Retire
        if ( settingName.indexOf("static-") === 0 ) {
            value = settingName.split("static-")[1];
        }

        // TODO: Could be more efficient
        if ( settingName.indexOf("var-") === 0 ) {
            var varName = settingName.split("var-")[1];
            if ( this.getDeclaractionBlock( this.cssVars ).indexOf(varName + ":") > -1 ) {
                value = "var("+varName+", inherit)";
            }
        }

        else if ( settingName.indexOf("calc-") === 0 ) {
            calc = settingName.split("calc-")[1];
            
            if ( calc == "placeholder") {
                
                value = this.$el.find('#' + this.slug + "_placeholder_color" ).val();

                if ( value == "" ) {
                    // Calculate based on the field color
                    value = getColorOpacity(this.$el.find('#' + this.slug + "_font_color" ).val(), .3);
                }
            
            } else if ( calc == "choice_style_color_muted") {
                value = getMutedColor( this.$el.find('#' + this.slug + "_choice_style_color" ).val() );
            }

        }

        else if ( settingName.indexOf('preset-') === 0 ) {
            preset_key = settingName.split('preset-')[1]; // Ex preset value: "preset-font_pri"
            value = this.presets[preset_key];
        }

        else {
            $settingEl = this.$el.find('#' + this.slug + "_" + settingName );

            
            // In case of a radio
            if ($settingEl.length == 0) {
                $settingEl = jQuery("[name=_gform_setting_" + this.slug + "_" + settingName + "]:checked");
            }


            if ($settingEl.length == 0 && settingName.indexOf("preset-") === -1 && settingName.indexOf("static-") === -1 ) {
                console.log("Setting " + settingName + " not found;");
                return '';
            }
            
            // if checkbox is selected
            if ( $settingEl.attr('type') == "checkbox" ) {
    
                if ( $settingEl.is(':checked') == false ) {
                    return "";
                } else {
                    // if checked
                    return "\n" + property + this.reinforce;
                }
    
    
            // if not a checkbox, get value from the field
            } else {
                value = $settingEl.val();
            }
        }

        // Check if the returned value is a preset
        if ( value.indexOf('preset-') === 0 ) {
            preset_key = value.split('preset-')[1]; // Ex preset value: "preset-font_pri"
            value = this.presets[preset_key];
        }
        


        if ( value != '') {

            // Depricated
            if ( property == "background-image" ) {
                value = "url('" +value+ "')";
            }
            // Since 1.7.1
            if ( property == "--gfsp--bg-img" ) {
                value = "url('" +value+ "')";
            }

            return "\n" + property + ":" + value + this.reinforce;
        }

        // Default
        return '';

    },


    this.getDeclaractionBlock = function( fields ) {

        var block = "";
        for (const prop in fields) {
            if (fields.hasOwnProperty(prop)) {
                const settingName = fields[prop];
                block += this.getDeclaration( prop, settingName );
            }
        }

        return block;
    },

    this.cssVars = {
        "--gfsp--bg-color": "bg_color",
        "--gfsp--bg-img": "bg_image",
        "--gfsp--bg-size": "bg_size",
        "--gfsp--wr-padding": "wr_padding",
    
        // "--gfsp--field-font-family": "font",
        // "--gfsp--field-font": "preset-font_sec",
        // "--gfsp--field-color": "font_color",
        "--gfsp--field-font-size": "font_size",
        // "--gfsp--field-font-weight": "field_font_weight",
        // "--gfsp--field-font-style": "font_italic",
        // "--gfsp--field-text-decoration": "font_underline",
    
        "--gfsp--placeholder-color": "calc-placeholder",
    
        /* Base */
        // "--gfsp--font": "label_font",
        "--gfsp--font": "preset-font_pri",
        "--gfsp--base-color": "label_color",
        "--gfsp--base-font-size": "base_font_size",
        
        "--gfsp--label-font-color": "label_font_color",
        "--gfsp--label-font-size": "label_font_size",
        "--gfsp--label-font-weight": "label_font_weight",
        "--gfsp--label-font-style: italic": "label_font_italic",
        "--gfsp--label-text-decoration: underline": "label_font_underline",
    
        "--gfsp--label-bg-color": "label_bg_color",
        "--gfsp--label-align": "label_text_align",
        "--gfsp--label-padding": "label_padding",
        "--gfsp--label-margin-bottom": "label_margin_bottom",
    
        "--gfsp--field-margin-bottom": "field_margin_bottom",
        "--gfsp--field-icon-color": "field_icon_color",
    
        "--gfsp--o-bg-color": "o_custom_bg",
        "--gfsp--o-bg-text-color": "o_custom_bg_text",
        
        // "--gfsp--field-bg-color": "field_bg_color",
        // "--gfsp--field-align": "field_text_align",
        "--gfsp--field-v-padding": "field_v_padding",
        "--gfsp--field-border-width": "field_border_width",
        // "--gfsp--field-border-radius": "field_border_radius",
        // "--gfsp--field-border-style": "field_border_style",
        // "--gfsp--field-border-color": "field_border_color",
    
        // "--gfsp--field-bg-color-focus": "field_focus_bg_color",
        // "--gfsp--field-border-color-focus": "field_focus_border_color",
    
        "--gfsp--warning-color": "validation_color",
        "--gfsp--warning-bg-color": "validation_bg_color",
    
        // "--gfsp--btn-bg-color": "btn_bg_color",
        // "--gfsp--btn-color": "btn_color",
        // "--gfsp--btn-font-size": "btn_font_size",
        // "--gfsp--btn-padding": "btn_padding",
        // "--gfsp--btn-border-width": "btn_border_width",
        // "--gfsp--btn-border-radius": "btn_border_radius",
        // "--gfsp--btn-border-style": "btn_border_style",
        // "--gfsp--btn-border-color": "btn_border_color",
    
        // "--gfsp--btn-color-hover": "btn_hover_color",
        // "--gfsp--btn-border-color-hover": "btn_hover_border_color",
        // "--gfsp--btn-bg-color-hover": "btn_hover_bg_color",
    
        // "--gfsp--btn-sbt-color": "btn_sbt_color",
        // "--gfsp--btn-sbt-border-color": "btn_sbt_border_color",
        // "--gfsp--btn-sbt-bg-color": "btn_sbt_bg_color",
    
        // "--gfsp--btn-sbt-color-hover": "btn_sbt_hover_color",
        // "--gfsp--btn-sbt-border-color-hover": "btn_sbt_hover_border_color",
        // "--gfsp--btn-sbt-bg-color-hover": "btn_sbt_hover_bg_color",
    
        "--gfsp--desc-font": "desc_font",
        "--gfsp--desc-font-size": "desc_font_size",
        "--gfsp--desc-color": "desc_color",
        "--gfsp--desc-align": "desc_text_align",
        "--gfsp--desc-padding": "desc_padding",
        "--gfsp--desc-margin-bottom": "desc_margin_bottom",
        "--gfsp--desc-bg-color": "desc_bg_color",
        "--gfsp--desc-font-weight": "desc_font_weight",
        "--gfsp--desc-font-style: italic": "desc_font_italic",
        "--gfsp--desc-text-decoration: underline": "desc_font_underline",
    
        // "--gfsp-choice-style-color": "choice_style_color",
        // "--gfsp-choice-style-color-muted": "calc-choice_style_color_muted",
    };


    this.cssSet = {

        '.gf_stylespro.theme_slug .gfield':{
            "--gfsp-choice-style-color"         : "choice_style_color",
            "--gfsp-choice-style-color-muted"   : "calc-choice_style_color_muted",
        },
        '.gf_stylespro.theme_slug input,\
        .gf_stylespro.theme_slug select,\
        .gf_stylespro.theme_slug textarea,\
        .gf_stylespro.theme_slug .ginput_total,\
        .gf_stylespro.theme_slug .ginput_product_price,\
        .gf_stylespro.theme_slug .ginput_shipping_price,\
        .theme_slug .gfsp_icon,\
        .gf_stylespro.theme_slug input[type=checkbox]:not(old) + label,\
        .gf_stylespro.theme_slug input[type=radio   ]:not(old) + label,\
        .gf_stylespro.theme_slug .ginput_container' : {
            'font-family'               : 'preset-font_sec',
            'color'                     : 'font_color',
            'font-size'                 : 'font_size',
            'font-weight'               : 'field_font_weight',
            'font-style: italic'        : 'font_italic',
            'text-decoration: underline': 'font_underline',
        },

        ".theme_slug_wrapper .gf_stylespro" : {
            'font-family'                   : 'var---gfsp--font',
            'color'                         : 'var---gfsp--base-color',
        },
        ".theme_slug_wrapper .gf_stylespro .gfield_label" : {
            'font-family'                   : 'var---gfsp--font',
            'color'                         : 'var---gfsp--label-font-color',
        },
        
        '.theme_slug_wrapper.gform_wrapper input:not([type="radio"]):not([type="checkbox"]):not([type=button]):not([type=submit]),\
        .theme_slug_wrapper.gform_wrapper select,\
        .theme_slug_wrapper.gform_wrapper textarea,\
        .theme_slug_wrapper.gform_wrapper input[type="text"],\
        .theme_slug_wrapper.gform_wrapper input[type="tel"],\
        .theme_slug_wrapper.gform_wrapper input[type="email"],\
        .theme_slug_wrapper.gform_wrapper input[type="url"],\
        .theme_slug_wrapper.gform_wrapper input[type="password"],\
        .theme_slug_wrapper.gform_wrapper input[type="search"],\
        .theme_slug_wrapper.gform_wrapper input[type="number"],\
        .theme_slug_wrapper.gform_wrapper .chosen-choices': {
            'background-color'          : 'field_bg_color',
            'font-weight'               : 'field_font_weight',
            'text-align'                : 'field_text_align',
            'border-radius'             : 'field_border_radius',
            'border-style'              : 'field_border_style',
            'border-color'              : 'field_border_color',
        },
        '.theme_slug .gfsp_icon': {
            'padding-top'               : 'field_v_padding',
            'padding-bottom'            : 'field_v_padding',
            'border-width'              : 'field_border_width',
            'border-color'              : 'field_border_color',
        },
        '.theme_slug_wrapper.gform_wrapper input:not([type="radio"]):not([type="checkbox"]):not([type=button]):not([type=submit]):focus,\
        .theme_slug_wrapper.gform_wrapper select:focus,\
        .theme_slug_wrapper.gform_wrapper textarea:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="text"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="tel"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="email"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="url"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="password"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="search"]:focus,\
        .theme_slug_wrapper.gform_wrapper input[type="number"]:focus': {
            'background-color'          : 'field_focus_bg_color',
            'border-color'              : 'field_focus_border_color',
        },

        '.gf_stylespro.theme_slug .button': {
            'background-color'          : 'btn_bg_color',
            'color '                    : 'var---gfsp--label-font-color',
            'color'                     : 'btn_color',
            'font-size'                 : 'btn_font_size',
            'padding'                   : 'btn_padding',
            'border-width'              : 'btn_border_width',
            'border-radius'             : 'btn_border_radius',
            'border-style'              : 'btn_border_style',
            'border-color'              : 'btn_border_color',
        },
        '.theme_slug .button:hover,\
        .theme_slug input[type=button]:hover,\
        .theme_slug input[type=submit]:hover' :{
            'color'                     : 'btn_hover_color',
            'border-color'              : 'btn_hover_border_color',
            'background-color'          : 'btn_hover_bg_color',
        },
        '.gf_stylespro.theme_slug .gform_next_button,\
        .gf_stylespro.theme_slug input[type=submit]': {
            'color'                     : 'btn_sbt_color',
            'border-color'              : 'btn_sbt_border_color',
            'background-color'          : 'btn_sbt_bg_color',
        },
        '.theme_slug .gform_next_button:hover,\
        .theme_slug input[type=button].gform_next_button:hover,\
        .theme_slug input[type=submit]:hover': {
            'color'                     : 'btn_sbt_hover_color',
            'border-color'              : 'btn_sbt_hover_border_color',
            'background-color'          : 'btn_sbt_hover_bg_color',
        },
    }


    this.getChoiceStyles = function() {

        var choice_style_color = this.$el.find('#' + this.slug + "_choice_style_color" ).val();

        if ( ! choice_style_color ) {
            return "";
        }

        // Choice Styles
        var choice_style_color_muted = getMutedColor(choice_style_color);
        
        var choiceStyleDeclaration = `.theme_slug .gfsp_toggle input[type]:not(old):checked + label:after,
        .theme_slug .gfsp_ios input[type]:not(old):checked + label:before,
        .theme_slug .gfsp_flip input[type]:not(old) + label:after{
            background-color:choiceColor
        }
        .theme_slug .gfsp_toggle input[type]:not(old):checked + label:before { background-color: choiceMuted }
        .theme_slug .gfsp_draw input[type]:not(old) + label:after { color:choiceColor }
        .theme_slug .gfsp_dot input[type]:not(old) + label:before {
            box-shadow: 0 0 0px 10px inset, 0 0 0px 15px choiceColor inset;
            border-color:choiceColor;
        }
        .theme_slug .gfsp_dot input[type]:not(old):checked + label:before {
            box-shadow: 0 0 0px 4px inset, 0 0 0px 15px choiceColor inset;
            border-color:choiceColor;
        }`;

    // Remove empty spaces
        choiceStyleDeclaration = choiceStyleDeclaration.replace(/    /g, "");
        choiceStyleDeclaration = choiceStyleDeclaration.replace(/theme_slug/g, this.slug);
        choiceStyleDeclaration = choiceStyleDeclaration.replace(/choiceColor/g, choice_style_color);
        choiceStyleDeclaration = choiceStyleDeclaration.replace(/choiceMuted/g, choice_style_color_muted);

        return choiceStyleDeclaration;
    }


    this.saveCss = function() {
        this.$el.find("#" + this.slug + "_theme_css").val( this.getCss() );
    }
    
}













// Reset Theme Settings
function resetToDefaults (theme, label) {
  var res = confirm('Reset ' + label + ' Theme to default settings?');
    if (res == true) {
    jQuery('.thm.' + theme + ' input[type=text], .thm.' + theme + ' select').each( function () { jQuery(this).val( jQuery(this).data('default')) } );
    jQuery('.thm.' + theme + ' input[type="checkbox"]').each( function () { if (   jQuery(this).prop('checked') == true ) { jQuery(this).trigger('click') } });
    jQuery('.thm.' + theme + ' .background').each( function () { if (jQuery(this).val() == 'default' ) { jQuery(this).trigger('click') } });

    // Advanced Settings

    jQuery('.thm.' + theme + ' .adv_field_options_wrapper').find('input, select').trigger('blur').css('background-color', '')
    
    // Fix for Color Picker
    if (typeof lastColorPicker !== 'undefined') {
        jQuery(lastColorPicker).trigger('change');
    }
  }
}


/* Hide all theme and Show @param: showTheme */
function toggleTheme (showTheme) {
  jQuery('.thm').slideUp();
  jQuery('.' + showTheme).slideDown();
}


/* Show and hide background options */
function toggleBgOption(theme, bg_type) {
    jQuery('#gform_setting_' + theme + '_bg_color, #gform_setting_' + theme + '_bg_image, #gform_setting_' + theme + '_bg_size').hide();

    if (bg_type == 'color') {
        jQuery('#gform_setting_' + theme + '_bg_color').slideDown();
    }

    if (bg_type == 'image') {
        jQuery('#gform_setting_' + theme + '_bg_image, #gform_setting_' + theme + '_bg_size').slideDown();
    }
}


// Media uploader
var gk_media_init = function(selector, button_selector)  {
    var clicked_button = false;

    jQuery(selector).each(function (i, input) {
        var button = jQuery(input).closest("div").find(button_selector);
        button.click(function (event) {
            event.preventDefault();
            var selected_img;
            clicked_button = jQuery(this);

            // check for media manager instance
            if(wp.media.frames.gk_frame) {
                wp.media.frames.gk_frame.open();
                return;
            }
            // configuration of the media manager new instance
            wp.media.frames.gk_frame = wp.media({
                title: 'Select image',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use selected image'
                }
            });

            // Function used for the image selection and media manager closing
            var gk_media_set_image = function() {
                var selection = wp.media.frames.gk_frame.state().get('selection');

                // no selection
                if (!selection) {
                    return;
                }

                // iterate through selected elements
                selection.each(function(attachment) {
                    var url = attachment.attributes.url;
                    clicked_button.closest("div").find(selector).val(url);
                    
                });
            };

            // closing event for media manger
            wp.media.frames.gk_frame.on('close', gk_media_set_image);
            // image selection event
            wp.media.frames.gk_frame.on('select', gk_media_set_image);
            // showing media manager
            wp.media.frames.gk_frame.open();
        });
});
};


function maybeShowFontOptions( e ) {

	var el = jQuery(e.target);
	var thm = el.closest('.thm').attr('id');

    var maybe_label = el.hasClass('label_font') ? "_label" : "" ;

    var font = el.val().split("/");
	var $theme_options = el.closest('fieldset');
    
    if ( font[0] === "" ) {
        font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_custom"]').fadeOut();
        font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_load_cb"]').fadeOut();
    }
	else if ( font.length > 1 && font[1] == "Native" ) {
	
	    if ( font[0] == "custom" ) {
			// Custom Font. Show only Custon Font option
            font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_custom"]').fadeIn();
            font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_load_cb"]').fadeOut();
        } else {
			// Native Font. Hide both options
            font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_custom"]').fadeOut();
            font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_load_cb"]').fadeOut();
			
		}
	}

	else {
        // Google Font. Show only Dont Load G Font option
        font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_custom"]').fadeOut();
        font_custom = $theme_options.find('div[id*="'+thm+maybe_label+'_font_load_cb"]').fadeIn();
	}
}




/**
 * 
 * @param {string} settingName  CSS classname for the setting field
 * @param {string} property     Name of the CSS property to create rule with
 * @param {string} suffix       Unit for the value, or value itself in case of a checkbox
 * 
 * @returns {string} CSS delaration
 */
function getDeclaration(settingName, property, suffix='' ) {

    $settingEl = $el.find('.' + settingName );

    if ($settingEl.length == 0) {
        console.log("Setting " + settingName + " not found;");
        return '';
    }
    
    // if checkbox is selected
	if ( $settingEl.attr('type') == "checkbox" ) {

        if ( $settingEl.is(':checked') == false ) {
            return "";
        } else {
            // if checked
            return property + ":" + suffix + reinforce;
        }


    // if not a checkbox, get value from the field
    } else {
        value = $settingEl.val();
    }

    if ( value != '') {
        return property + ":" + value + suffix + reinforce;
    }

    // Default
    return '';

}


function getMutedColor ( color ) {
    
    if ( typeof color !== "undefined" && color.length == 0) {
        return "";
    }

    if ( typeof colorCalculator === "undefined" ) {
        window.colorCalculator = new Colors();
    }

    colorCalculator.setColor( color );

    var hsl = colorCalculator.colors.RND.hsl;
    var newHsl = "";

    if ( colorCalculator.colors.RND.hsl.l < 5 ) {
        newHsl = "hsl("+ hsl.h + "," + (hsl.s * 0.5) + "," + ((hsl.l + 5) * 5) + ")";
    }
    else if ( colorCalculator.colors.RND.hsl.l < 10 ) {
        newHsl = "hsl("+ hsl.h + "," + (hsl.s * 0.5) + "," + ((hsl.l) * 4) + ")";
    }
    else if ( colorCalculator.colors.RND.hsl.l < 20 ) {
        newHsl = "hsl("+ hsl.h + "," + (hsl.s * 0.5) + "," + ((hsl.l) * 2.5) + ")";
    }
    else if (colorCalculator.colors.RND.hsl.l > 70) {
        newHsl = "hsl("+ hsl.h + "," + (hsl.s * 0.5) + "," + (hsl.l * .85) + ")";
    } else {
        newHsl = "hsl("+ hsl.h + "," + (hsl.s * 0.5) + "," + (hsl.l * 1.3) + ")";
    }
 
    colorCalculator.setColor(newHsl);

    var newColor = colorCalculator.colors.RND.rgb;
    
    return "rgb("+ newColor.r + "," + newColor.g + "," + newColor.b + ")";
}


saveExpandedFields = function() {
    var theme_id = jQuery(".toggle_theme").val();
    var expanded_adv = jQuery("fieldset#"+theme_id+" .gform-settings-field.expanded");
    var expanded_adv_arr = [];
    
    expanded_adv.each( function() {
        var adv_id = this.id.split("gform_setting_"+theme_id+"_")[1]
        expanded_adv_arr.push(adv_id);
    });

    localStorage.setItem("spAdvFieldsExpand", expanded_adv_arr)
}



jQuery(document).ready(function() {

    jQuery(".thm").css('display', 'none');
    jQuery("." + jQuery('#theme').val() ).slideDown();

    jQuery('body').addClass('loading_completed');

    // Resets Additonal Script values in case they've been changed
    jQuery('.additional_scripts').each( function() {
        jQuery(this).val( jQuery(this).data('value') );
    });


    // Changes Font names in select box labels
    jQuery('.font_pri').on('change', function(){
        var txt = jQuery(this).find(':selected').text();
        var thm = jQuery(this).closest('.thm');
        thm.find(".adv_font option[value='preset-font_pri']").text(txt + " (Field)");

        if (txt == "Custom font") {
            thm.find('.font_pri_custom').change();
        }
    });
    jQuery('.font_pri_custom').on('change', function(){
        var txt = jQuery(this).val();
        var thm = jQuery(this).closest('.thm');
        thm.find(".adv_font option[value='preset-font_pri']").text(txt + " (Field)");
    });
    // Same as above. Duplication is simpler
    jQuery('.font_sec').on('change', function(){
        var txt = jQuery(this).find(':selected').text();
        var thm = jQuery(this).closest('.thm');
        thm.find(".adv_font option[value='preset-font_sec']").text(txt + " (Label)");

        if (txt == "Custom font") {
            thm.find('.font_sec_custom').change();
        }
    });
    jQuery('.font_sec_custom').on('change', function(){
        var txt = jQuery(this).val();
        var thm = jQuery(this).closest('.thm');
        thm.find(".adv_font option[value='preset-font_sec']").text(txt + " (Label)");
    });


    // Bind font options to font select boxes
    // immediately trigger after
    jQuery('.font, .label_font').on('change init', maybeShowFontOptions.bind(this) )
    .trigger('init');




    /* Generate CSS from settings and submit form */
    jQuery('#gform-settings').submit(function(e) {

        // Add px unit to values which are left with numbers only
        jQuery(".auto_px").each(function() {
            var vl = jQuery(this).val();
            var vl_ar  = vl.split(" ");
    
            vl_ar.forEach( (val, ind) => {
                if ( val == parseInt(val) )
                    vl_ar[ind] = val + "px";
                }
            );
    
            jQuery(this).val( vl_ar.join(" ") );
        });
        
        // e.preventDefault();
        jQuery('.thm').each(function () {
            // var theme_name = jQuery(this).attr('id').split('_')[1];
            var theme_name = jQuery(this).attr('id');

            var this_theme = new Theme( theme_name );

            // Clear BG color in case it is not selected
            var bg = jQuery('#gform_setting_' + theme_name + '_background :checked').val();
            if ( bg == "none" || bg === "default" ) {
                jQuery('#' + theme_name + '_bg_color').val("");
            }

            this_theme.saveCss();

        });

        jQuery('.no_submit').attr('disabled', 'disabled');

        saveExpandedFields();


    });

    /* Initiate color picker */
    myColorPicker = jQuery(".color").colorPicker({
        opacity: true,

        convertCallback: function(colors, type) {
            rgb = colors.RND.rgb;
            placeholder_color = 'rgba(' + rgb['r'] + ', ' + rgb['g'] + ', ' + rgb['b'] + ', ' + (colors.alpha*0.47).toFixed(2) + ')';
        },
        renderCallback: function($elm, toggled) {
            if (typeof placeholder_color !== undefined && placeholder_color != '' && placeholder_color != null) {
                $elm.attr('data-ph-color', placeholder_color);
            }
            window.lastColorPicker = $elm;
        }
    });

    /* Calculate placeholder colors and save to the field's data-ph-color */
    jQuery(".font_color").each( function() {

        rgb = jQuery(this).colorPicker().colorPicker.color.colors.RND.rgb;
        alpha = jQuery(this).colorPicker().colorPicker.color.colors.alpha;
        placeholder_color = 'rgba(' + rgb['r'] + ', ' + rgb['g'] + ', ' + rgb['b'] + ', ' + (alpha*0.47).toFixed(2) + ')';

        jQuery(this).attr('data-ph-color', placeholder_color);
    });


    /* Apply Media Uploader to upload background image fields */
    gk_media_init('.bg_image', '.media-button');


    /* Set background options per saved or default settings */
    jQuery('.background:checked').each ( function() {
        toggleBgOption(jQuery( this ).data('theme'), jQuery( this ).val())
    });

    /* Empty Fields Styles */
    
    jQuery("input, select").each( function() {
        if (jQuery(this).val() == '')
            jQuery(this).css('opacity', '.7');
    });
    
    jQuery("input, select").on('blur', function() {
        if (jQuery(this).val() == '')
            jQuery(this).css('opacity', '.7');
    });
    
    jQuery("input, select").on('focus', function() {
            jQuery(this).css('opacity', '1');
    });


    // Expand last expanded fields as set in localStorage
    var theme_id = jQuery(".toggle_theme").val();
    var expanded_adv = localStorage.getItem("spAdvFieldsExpand")

    if ( expanded_adv ) {
        var adv_expanded_arr = expanded_adv.split(",");

        jQuery.each(adv_expanded_arr,  function(i, adv_field) {
            jQuery("#gform_setting_"+theme_id+"_"+adv_field).addClass("expanded");
        })
    }


    // Hide Gravity Flow setting if Gravity Flow is not available
    $gflow_field_wrapper = jQuery('#gform_setting_gravity_flow_form_style');
    if ( $gflow_field_wrapper.find('select').hasClass('hide_field') ) {
        $gflow_field_wrapper.css('display','none')
    }
}); // Document ready ends
