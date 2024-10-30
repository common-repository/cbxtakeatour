(function ($) {
    'use strict';

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
        var awn_options = {
            labels: {
                tip          : cbxtakeatour_listing.awn_options.tip,
                info         : cbxtakeatour_listing.awn_options.info,
                success      : cbxtakeatour_listing.awn_options.success,
                warning      : cbxtakeatour_listing.awn_options.warning,
                alert        : cbxtakeatour_listing.awn_options.alert,
                async        : cbxtakeatour_listing.awn_options.async,
                confirm      : cbxtakeatour_listing.awn_options.confirm,
                confirmOk    : cbxtakeatour_listing.awn_options.confirmOk,
                confirmCancel: cbxtakeatour_listing.awn_options.confirmCancel
            }
        };


        //$('.wrap').addClass('cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-listing-wrapper');
        $('#screen-meta').addClass('cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-listing-wrapper');
        //$('.page-title-action').addClass('button primary');
        //$('#search-submit').addClass('button primary');
        //$('#post-query-submit').addClass('button primary');
       // $('.button.action').addClass('button outline primary');
        //$('#save-post').addClass('button primary');
        //$('#publish').addClass('button primary');

        //$(cbxtakeatour_listing.global_setting_link_html).insertAfter('.page-title-action');
        $('#cbxtourlistsearch-search-input').attr('placeholder', cbxtakeatour_listing.search_placeholder);

        //on click create tour, create draft and load
        $('#create-new-tour').on('click', function (e){
            e.preventDefault();

            var $this = $(this);

            $this.prop('disabled', true);
            $this.hide();

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: cbxtakeatour_listing.ajaxurl,
                data: {
                    action: 'cbxtakeatour_create_auto_drafts',
                    security: cbxtakeatour_listing.editnonce
                },
                success: function (data, textStatus, XMLHttpRequest) {
                    if(data.success){
                        new AWN(awn_options).success(data.message);

                        window.location.href = data.url;
                    }
                    else{
                        new AWN(awn_options).alert(data.message);
                    }

                }//end of success
            });//end of ajax
        });//end on click create tour, create draft and load

        //on click clean auto drafts button
        $('#clean-auto-drafts').on('click', function (e){
           e.preventDefault();

            var $this = $(this);


            var notifier = new AWN(awn_options);

            var onCancel = () => { };

            var onOk = () => {
                $this.prop('disabled', true);

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: cbxtakeatour_listing.ajaxurl,
                    data: {
                        action: 'cbxtakeatour_delete_auto_drafts',
                        security: cbxtakeatour_listing.editnonce
                    },
                    success: function (data, textStatus, XMLHttpRequest) {
                        $this.prop('disabled', false);

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
                cbxtakeatour_listing.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: cbxtakeatour_listing.are_you_sure_global
                    }
                }
            );
        }); //on click clean auto drafts button

        //click to copy shortcode
        $('.cbxballon_ctp').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            cbxtakeatour_copyStringToClipboard($this.prev('.cbxshortcode').text());

            $this.attr('aria-label', cbxtakeatour_listing.copycmds.copied_tip);

            window.setTimeout(function () {
                $this.attr('aria-label', cbxtakeatour_listing.copycmds.copy_tip);
            }, 1000);
        });



    });//end dom ready

})(jQuery);