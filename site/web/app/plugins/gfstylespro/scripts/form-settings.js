(function($) {
    "use strict";
        // Insert Warning  messages
    $('#gform_setting_theme').after('<div class="no_theme alert notice-warning"><p>No theme is selected, default Gravity Forms styles will be used. <span class="to_enable" style="display: none;">Select a theme to enable all options</span></p></div>');
    $('#gform_setting_theme').after('<div class="info default alert info"><p>Default Theme is set to <i><b id="default_theme_name"></b> Theme</i>. This can be changed from Styles Pro Settings. <span class="to_enable" style="display: none;">Select a theme to enable all options</span></p></div>');
    $('<div class="warning icons notice-warning"><p><i class="fa fa-exclamation-triangle"></i> Consider page load time when enabling more than one icon-sets.</p></div>').appendTo('#gform_setting_iconsets');

    // Warning for No Theme
    $('select#theme').change( function(){
        var theme = $(this).val();
        var default_theme = $(this).attr('default_theme');
        if ( theme == '' ) {
            $('.info.default').show();
        } else {
            $('.info.default').hide();
        }

        if ( theme == 'none' ) {
            $('.alert.no_theme').show();
        } else {
            $('.alert.no_theme').hide();
        }
        
        $('.themes_descriptions > *').slideUp();
        
        if ( theme == 'none'   ||   theme == ''   &&   ( default_theme == '' || default_theme == undefined ) ) {
            $('.themes_descriptions').slideUp();
            $('span.to_enable').show();
            $('#gform_setting_v_popup, #gform_setting_v_scroll, #gform_setting_v_message, #gform_setting_iconsets, #gform_setting_footer_style').addClass('disable');
        } else {
            $('span.to_enable').hide();
            $('#gform_setting_v_popup, #gform_setting_v_scroll, #gform_setting_v_message, #gform_setting_iconsets, #gform_setting_footer_style').removeClass('disable');
            $('.themes_descriptions #desc_' + theme).slideDown();
            $('.themes_descriptions').slideDown();
        }

    });

    // Warning for more than one icon-sets
    $('#gform_setting_iconsets input[type=checkbox]').on('change', function() {
        if ( $('#gform_setting_iconsets input[type=checkbox]:checked').length > 1 ){
            $('.warning.icons').show();
        }
        else {
            $('.warning.icons').hide();
        }
    });

    // For Defaults
    $('select#theme').change();
    $('#gform_setting_iconsets input[type=checkbox]').change();

    if ( document.getElementById('_gform_setting_v_popup').checked ){
        $('#gform_setting_v_message').show();
        }
    else {
        $('#gform_setting_v_message').hide();
    }

    // Default theme name
    var default_theme_name = 'No';
    var default_theme_val = $("#theme").attr('default_theme');
    if ( default_theme_val != '' && default_theme_val != undefined )
        default_theme_name = $("#theme option[value='" + default_theme_val +"']").text()
    
    $("#default_theme_name").text(default_theme_name);

    $('#gform-settings').submit(function() {
        // Disable empty fields to exclude from saving
        $('#gform-settings').find('input, select').each( function() {
            if ($(this).val() == '') {
                $(this).attr('disabled', 'disabled')
            }
        });
    });

})(jQuery);