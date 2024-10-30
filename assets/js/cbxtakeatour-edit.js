(function ($) {
    'use strict';


    /**
     * Convert hidden field to mini toggle     *
     *
     * @param $
     * @param $element
     */
    function cbxtakeatour_miniToggle($, $element){

        var current_value_toggle = Number($element.val());


        $('<div style="width: 55px;cursor: pointer" class="minitoggle_trigger_display"></div>').insertBefore($element);

        var $element_prev = $element.prev('.minitoggle_trigger_display');

        $element_prev.minitoggle({
            on: 1 === current_value_toggle
        });

        if (1 === current_value_toggle) {
            $element_prev.find('.toggle-handle').attr('style', 'transform: translate3d(27px, 0px, 0px)');
        }

        $element_prev.on('toggle', function (e) {
            if (e.isActive)
                $element.val(1);
            else
                $element.val(0);
        });
    }//end method cbxtakeatour_miniToggle

    function cbxtakeatour_copyStringToClipboard (str) {
        // Create new element
        var el = document.createElement('textarea');
        // Set value (string to be copied)
        el.value = str;
        // Set non-editable to avoid focus and move outside of view
        el.setAttribute('readonly', '');
        el.style = {position: 'absolute', left: '-9999px'};
        document.body.appendChild(el);
        // Select text inside element
        el.select();
        // Copy text to clipboard
        document.execCommand('copy');
        // Remove temporary element
        document.body.removeChild(el);
    }


    jQuery(document).ready(function ($) {
        //console.log('cbx tours add edit');

        var awn_options = {
            labels: {
                tip          : cbxtakeatour_edit.awn_options.tip,
                info         : cbxtakeatour_edit.awn_options.info,
                success      : cbxtakeatour_edit.awn_options.success,
                warning      : cbxtakeatour_edit.awn_options.warning,
                alert        : cbxtakeatour_edit.awn_options.alert,
                async        : cbxtakeatour_edit.awn_options.async,
                confirm      : cbxtakeatour_edit.awn_options.confirm,
                confirmOk    : cbxtakeatour_edit.awn_options.confirmOk,
                confirmCancel: cbxtakeatour_edit.awn_options.confirmCancel
            }
        };

        $.extend($.validator.messages, {
            required   : cbxtakeatour_edit.validation.required,
            remote     : cbxtakeatour_edit.validation.remote,
            email      : cbxtakeatour_edit.validation.email,
            url        : cbxtakeatour_edit.validation.url,
            date       : cbxtakeatour_edit.validation.date,
            dateISO    : cbxtakeatour_edit.validation.dateISO,
            number     : cbxtakeatour_edit.validation.number,
            digits     : cbxtakeatour_edit.validation.digits,
            creditcard : cbxtakeatour_edit.validation.creditcard,
            equalTo    : cbxtakeatour_edit.validation.equalTo,
            maxlength  : $.validator.format(cbxtakeatour_edit.validation.maxlength),
            minlength  : $.validator.format(cbxtakeatour_edit.validation.minlength),
            rangelength: $.validator.format(cbxtakeatour_edit.validation.rangelength),
            range      : $.validator.format(cbxtakeatour_edit.validation.range),
            max        : $.validator.format(cbxtakeatour_edit.validation.max),
            min        : $.validator.format(cbxtakeatour_edit.validation.min)
        });

        if (typeof tinyMCE !== 'undefined') {
            tinymce.init(cbxtakeatour_edit.teeny_setting);
        }

        //remove all step at once
        $('#cbxtakeatour_steps').on('click', '#cbxtourmetaboxremstep', function (e) {
            e.preventDefault();

            var $this = $(this);
            var $parent_wrapper = $this.closest('#cbxtakeatour_steps');

            var notifier = new AWN(awn_options);

            var onCancel = () => {

            };

            var onOk = () => {
                $parent_wrapper.find('#cbxtourmetabox_entries').empty();
            };

            notifier.confirm(
                cbxtakeatour_edit.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: cbxtakeatour_edit.are_you_sure_global
                    }
                }
            );

        });
        //end remove all steps at once

        //add new step
        $('#cbxtourmetaboxaddstep').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            var $counter = $this.data('counter');

            var $newEntry = elementHTMLStep.replace(/COUNTERPLUS/g, (Number($counter) + 1)).replace(/COUNTER/g, Number($counter));

            $('#cbxtourmetabox_entries').append($newEntry);

            var $content_id = 'cbxtourmetabox_fields_content_' + $counter;

            if (typeof tinyMCE !== 'undefined') {
                tinymce.execCommand('mceRemoveEditor', false, $content_id);
                tinymce.execCommand('mceAddEditor', false, $content_id);
            }

            cbxtakeatour_miniToggle($, $('#cbxtourmetabox_fields_state_'+$counter));

            $this.data('counter', ++$counter);

            $('#cbxtourmetaboxremstep').css({
                'display': 'inline-block'
            });


        });
        //end add new step

        //show hide step fields on click
        $('#cbxtourmetabox_entries').on('click', '.cbxtourmetabox_step_heading', function (e) {
            e.preventDefault();

            var $this = $(this);
            $this.next('.cbxtourmetabox_wrap').toggle();
        });

        //hide all field box except the first one
        $('.cbxtourmetabox_wrap').hide();

        //single step delete
        $('#cbxtourmetabox_entries').on('click', 'a.delete-step', function (e) {
            e.preventDefault();

            var $this = $(this);
            var notifier = new AWN(awn_options);

            var onCancel = () => {

            };

            var onOk = () => {
                $this.closest('.cbxtourmetabox_entry').remove();
            };

            notifier.confirm(
                cbxtakeatour_edit.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: cbxtakeatour_edit.are_you_sure_global
                    }
                }
            );

        });

        // sort steps by dragging and drop
        $('#cbxtourmetabox_entries').sortable({
            handle: 'span',
            cancel: '',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true
        });

        var $setting_page = $('#tour_postbox');
        var $setting_nav = $setting_page.find('.setting-tabs-nav');
        var activetab = '';
        if (typeof (localStorage) !== 'undefined') {
            activetab = localStorage.getItem('cbxtakeatouractivetabadmin');
        }

        if (activetab !== '' && $(activetab).length && $(activetab).hasClass('global_setting_group')) {
            $('.global_setting_group').hide();
            $(activetab).fadeIn();
        }

        if (activetab !== '' && $(activetab + '-tab').length) {
            $setting_nav.find('a').removeClass('active');
            $(activetab + '-tab').addClass('active');
        }

        $setting_nav.on('click', 'a',function(e) {
            e.preventDefault();

            var $this = $(this);

            $setting_nav.find('a').removeClass('active');
            $this.addClass('active');



            var clicked_group = $(this).attr('href');

            if (typeof(localStorage) !== 'undefined') {
                localStorage.setItem('cbxtakeatouractivetabadmin', $(this).attr('href'));
            }
            $('.global_setting_group').hide();
            $(clicked_group).fadeIn();
        });


        $('.setting-color-picker-wrapper').each(function (index, element){
            var $color_field_wrap = $(element);
            var $color_field = $color_field_wrap.find('.setting-color-picker');
            var $color_field_fire = $color_field_wrap.find('.setting-color-picker-fire');

            var $current_color = $color_field_fire.data('current-color');

            // Simple example, see optional options for more configuration.
            const pickr = Pickr.create({
                el: $color_field_fire[0],
                theme: 'classic', // or 'monolith', or 'nano'
                default: $current_color,

                swatches: [
                    'rgba(244, 67, 54, 1)',
                    'rgba(233, 30, 99, 0.95)',
                    'rgba(156, 39, 176, 0.9)',
                    'rgba(103, 58, 183, 0.85)',
                    'rgba(63, 81, 181, 0.8)',
                    'rgba(33, 150, 243, 0.75)',
                    'rgba(3, 169, 244, 0.7)',
                    'rgba(0, 188, 212, 0.7)',
                    'rgba(0, 150, 136, 0.75)',
                    'rgba(76, 175, 80, 0.8)',
                    'rgba(139, 195, 74, 0.85)',
                    'rgba(205, 220, 57, 0.9)',
                    'rgba(255, 235, 59, 0.95)',
                    'rgba(255, 193, 7, 1)'
                ],

                components: {

                    // Main components
                    preview: true,
                    opacity: true,
                    hue: true,

                    // Input / output Options
                    interaction: {
                        hex: true,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: true,
                        save: true
                    }
                },
                i18n: cbxtakeatour_edit.pickr_i18n
            });

            pickr.on('init', instance => {
                //console.log('Event: "init"', instance);
            }).on('hide', instance => {
                //console.log('Event: "hide"', instance);
            }).on('show', (color, instance) => {
                //console.log('Event: "show"', color, instance);
            }).on('save', (color, instance) => {
                //console.log(color.toHEXA().toString());
                //console.log(color);

                if(color !== null){
                    $color_field_fire.data('current-color', color.toHEXA().toString());
                    $color_field.val(color.toHEXA().toString());
                }
                else{
                    $color_field_fire.data('current-color', '');
                    $color_field.val('');
                }


                //console.log(instance);
                //console.log(color.toHEXA());
                //console.log(color.toHEX);
            }).on('clear', instance => {
                //console.log('Event: "clear"', instance);



            }).on('change', (color, source, instance) => {
                //console.log('Event: "change"', color, source, instance);

            }).on('changestop', (source, instance) => {
                //console.log('Event: "changestop"', source, instance);
            }).on('cancel', instance => {
                //console.log('Event: "cancel"', instance);
            }).on('swatchselect', (color, instance) => {
                //console.log('Event: "swatchselect"', color, instance);
            });

        });

        $('.minitoggle_trigger').each(function (index, element) {
            cbxtakeatour_miniToggle($, $(element));

            /*var $element = $(element);

            var current_value_toggle = Number($element.val());


            $('<div style="width: 55px;cursor: pointer" class="minitoggle_trigger_display"></div>').insertBefore($element);

            var $element_prev = $element.prev('.minitoggle_trigger_display');

            $element_prev.minitoggle({
                on: 1 === current_value_toggle
            });

            if (1 === current_value_toggle) {
                $element_prev.find('.toggle-handle').attr('style', 'transform: translate3d(27px, 0px, 0px)');
            }

            $element_prev.on('toggle', function (e) {
                if (e.isActive)
                    $element.val(1);
                else
                    $element.val(0);
            });*/
        });


        /*Add color picker */
        /*var myOptions = {
            // you can declare a default color here,
            // or in the data-default-color attribute on the input
            defaultColor: false,
            // a callback to fire whenever the color changes to a valid color
            change: function (event, ui) {
            },
            // a callback to fire when the input is emptied or an invalid color
            clear: function () {
            },
            // hide the color picker controls on load
            hide: true,
            // show a group of common colors beneath the square
            // or, supply an array of colors to customize further
            palettes: true
        };

        $('.cbxtakeatour-color-field').wpColorPicker(myOptions);*/

        //select all text on click of shortcode text
        /*$('.cbxtakeatourshortcode').on('click', function (e) {
            var text = $(this).text();
            var $this = $(this);
            var $input = $('<input class="cbxtakeatourshortcode-text" type="text">');
            $input.prop('value', text);
            $input.insertAfter($(this));
            $input.focus();
            $input.select();
            $this.hide();

            try {
                document.execCommand('copy');
            } catch (err) {

            }

            $input.focusout(function () {
                $this.show();
                $input.remove();
            });
        });*/

        



        //$('.wrap').addClass('cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-addedit-wrapper');
        $('#screen-meta').addClass('cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-addedit-wrapper');
        //$('#search-submit').addClass('button primary');
        //$('#post-query-submit').addClass('button primary');
        //$('.button.action').addClass('button outline primary');
        //$('.page-title-action').addClass('button primary');
        //$('#save-post').addClass('button primary');
        //$('#publish').addClass('button primary');

        //$(cbxtakeatour_edit.global_setting_link_html).insertAfter('.page-title-action');

        //submit form and validation

        var $tour_form = $('#cbxtakeatour_add');
        var $tour_form_validator = $tour_form.validate({
           /* errorPlacement: function (error, element) {
                error.appendTo(element.closest('.cbxpetition-signform-field'));
            },*/
            errorElement  : 'p',
            rules         : {
                /*'cbxpetition-fname': {
                    required : true,
                    minlength: 2
                },
                'cbxpetition-lname': {
                    required : true,
                    minlength: 2
                },
                'cbxpetition-email': {
                    required: true,
                    email   : true
                },*/
            },
            messages      : {}
        });

        $tour_form.on('submit', function (e) {

            var $form = $(this);


            if ($tour_form_validator.valid()) {
                    e.preventDefault();

                    $tour_form.data('busy', 0);

                    //$tour_form.data('busy', 1);
                    //$tour_form.find('.cbxpetition_ajax_icon').show();
                    $tour_form.find('#cbxtakeatour_submit').prop('disabled', true);

                    //$form_wrapper.find('.cbxpetition-error-messages').empty();
                    //$form_wrapper.find('.cbxpetition-error-messages').empty();


                    var form_data = $form.serialize();

                    // process the form
                    var request = $.ajax({
                        type    : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                        url     : cbxtakeatour_edit.ajaxurl, // the url where we want to POST
                        data    : form_data, // our data object
                        security: cbxtakeatour_edit.nonce,
                        dataType: 'json' // what type of data do we expect back from the server
                    });

                    request.done(function (data) {

                        //console.log(data.validation_errors);

                        if(Object.keys(data.validation_errors).length > 0){
                            //console.log(data.validation_errors);
                            var val_errors = data.validation_errors;
                            $tour_form_validator.showErrors(val_errors);


                        }
                        else if(data.success){
                            new AWN(awn_options).success(data.message);
                        }
                        else{
                            new AWN(awn_options).alert(data.message);
                        }


                        $tour_form.data('busy', 0);
                        $tour_form.find('#cbxtakeatour_submit').prop('disabled', false);
                        // $tour_form.find('.cbxpetition_ajax_icon').hide();
                    });

                    request.fail(function (jqXHR, textStatus) {
                        $tour_form.data('busy', 0);
                        $tour_form.find('#cbxtakeatour_submit').prop('disabled', false);
                        // $tour_form.find('.cbxpetition_ajax_icon').hide();


                        //awn notification
                        new AWN(awn_options).alert(cbxtakeatour_edit.ajax_fail);
                    });
                //}//end if ajax and not busy
            }
        });

        //click to copy shortcode
        $('.cbxballon_ctp').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            cbxtakeatour_copyStringToClipboard($this.prev('.cbxshortcode').text());

            $this.attr('aria-label', cbxtakeatour_edit.copycmds.copied_tip);

            window.setTimeout(function () {
                $this.attr('aria-label', cbxtakeatour_edit.copycmds.copy_tip);
            }, 1000);
        });

        //move the tour to trash on click
        $('#cbxtakeatour_trashit').on('click', function (e){
            e.preventDefault();

            var $this = $(this);
            var $post_id = parseInt($this.data('post-id'));

            var notifier = new AWN(awn_options);

            var onCancel = () => { };

            var onOk = () => {

                $this.prop('disabled', true);

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: cbxtakeatour_edit.ajaxurl,
                    data: {
                        action: 'cbxtakeatour_move_to_trash',
                        security: cbxtakeatour_edit.nonce,
                        post_id: $post_id
                    },
                    success: function (data, textStatus, XMLHttpRequest) {
                        $this.hide();

                        if(data.success){
                            new AWN(awn_options).success(data.message);

                            window.location.href = data.url;
                        }
                        else{
                            new AWN(awn_options).alert(data.message);
                        }

                    }//end of success
                });//end of ajax
            };

            notifier.confirm(
                cbxtakeatour_edit.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: cbxtakeatour_edit.are_you_sure_global
                    }
                }
            );
            

        });//end move the tour to trash on click


    });//dom ready
})(jQuery);
