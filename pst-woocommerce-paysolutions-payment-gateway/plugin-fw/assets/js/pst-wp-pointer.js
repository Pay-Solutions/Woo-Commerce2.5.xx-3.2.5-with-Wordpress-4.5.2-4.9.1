jQuery(document).ready( function($) {

    var pointers    = custom_pointer.pointers[0],
        options     = pointers.options,
        target      = $(pointers.target),
        pointer_id  = pointers.pointer_id;

    $(target).find('.wp-submenu li a').each(function () {

            var t = $(this),
                href = t.attr('href');

            href = href.replace('admin.php?page=', '');

            if( href == pointer_id ){

                var selected_plugin_row = t.add( target ),
                    top_level_menu      = target.find( pointers.target.replace( '#', '.' ) );

                target.toggleClass('wp-no-current-submenu wp-menu-open wp-has-current-submenu');

                t.pointer({
                    pointerClass: 'pst-wp-pointer',
                    content : options.content,
                    position: options.position,
                    open    : function () { console.log( 'here' );
                        selected_plugin_row.toggleClass( 'pst-pointer-selected-row' );
                        top_level_menu.addClass( 'pst-pointer' );
                    },


                    close   : function () {
                        target.toggleClass('wp-no-current-submenu wp-menu-open wp-has-current-submenu');
                        selected_plugin_row.toggleClass( 'pst-pointer-selected-row' );
                        top_level_menu.removeClass( 'pst-pointer' );

                        $.ajax({
                            type   : 'POST',
                            url    : ajaxurl,
                            data   : {
                                "action" : "dismiss-wp-pointer",
                                "pointer": pointer_id
                            },
                            success: function (response) {
                            }
                        });

                    }
                }).pointer('open');
            } else if( 'payh_default_pointer' == pointer_id ) {

                 var selected_plugin_row = t.add( target ),
                     top_level_menu      = target.find( pointers.target.replace( '#', '.' )),
                     pst_plugins         = $( pointers.target );

                pst_plugins.addClass('wp-has-current-submenu');

                top_level_menu.pointer({
                    pointerClass: 'pst-wp-pointer',
                    content : options.content,
                    position: options.position,

                    open    : function () {
                        pst_plugins.addClass( 'pst-pointer-selected-row' );
                    },

                    close   : function () {
                        pst_plugins.removeClass( 'pst-pointer-selected-row wp-has-current-submenu' );

                        $.ajax({
                            type   : 'POST',
                            url    : ajaxurl,
                            data   : {
                                "action" : "dismiss-wp-pointer",
                                "pointer": pointer_id
                            },
                            success: function (response) {
                            }
                        });
                    }
                }).pointer('open');
            }
        });
});