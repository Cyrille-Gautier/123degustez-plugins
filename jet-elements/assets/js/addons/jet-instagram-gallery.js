(function ($) {

    "use strict";

    function widgetInstagramGallery( $scope ) {

        var $target         = $scope.find( '.jet-instagram-gallery__instance' ),
        instance        = null,
        defaultSettings = {},
        settings        = {};

        if ( ! $target.length ) {
            return;
        }

        settings = $target.data( 'settings' ),

        /*
        * Default Settings
        */
        defaultSettings = {
            layoutType: 'masonry',
        }
        /**
         * Checking options, settings and options merging
         */
        $.extend( defaultSettings, settings );

        if ( 'masonry' === settings.layoutType ) {
            salvattore.init();

            $( window ).on( 'resize orientationchange', function() {
                salvattore.rescanMediaQueries();
            } )
        }
    }

    window.widgetInstagramGallery = widgetInstagramGallery;

})( jQuery );