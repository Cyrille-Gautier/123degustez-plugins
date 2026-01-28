(function ( $ ) 
{

    "use strict";

    function widgetScrollNavigation( $scope ) 
    {
        var $target = $scope.find( '.jet-scroll-navigation' ),
            $settings = $target.data( 'settings' ) || {};

        if ( ! $target.length ) 
            return;

        var $instance = new JetScrollNavigation( $scope, $target, $settings );
        $instance.init();
    }

    class JetScrollNavigation 
    {

        constructor( $scope, $selector, $settings ) 
        {
            this.$scope = $scope;
            this.$instance = $selector;
            this.$itemsList = $( '.jet-scroll-navigation__item', $selector );

            this.settings = $.extend({
                speed: 500,
                offset: 0,
                sectionSwitch: false,
                sectionSwitchOnMobile: true,
                scroll_threshold: 50,
            }, $settings );

            this.sections = {};
            this.currentSection = null;
            this.isScrolling = false;
            this.$window = $( window );
        }

        init() 
        {
            this.setSectionsData();

            if ( this.currentSection )
                this.activateSection( this.currentSection );

            this.bindEvents();
            this.initObservers();
        }

        bindEvents() 
        {
            const self = this;

            this.$itemsList.on( 'click.jetScrollNavigation', function () 
            {
                const $sectionId = $( this ).data( 'anchor' );
                self.scrollToSection( $sectionId );
            });

            this.$window.on(
                'resize.jetScrollNavigation',
                JetElementsTools.debounce( 50, () => self.setSectionsData() )
            );

            if ( this.settings.sectionSwitch ) 
                this.enableWheelControl();
        }

        setSectionsData() 
        {
            const self = this;
            this.sections = {};

            this.$itemsList.each( function () 
            {
                const $item = $( this );
                const $sectionId = $item.data( 'anchor' );
                const $invert = $item.data( 'invert' ) === 'yes';
                const $section = $( '#' + $sectionId );
                if ( !$section.length ) 
                    return;

                self.sections[ $sectionId ] = 
                {
                    selector: $section,
                    offset: Math.round( $section.offset().top ),
                    height: $section.outerHeight(),
                    invert: $invert
                };
            });

            if ( ! this.currentSection ) 
                this.currentSection = Object.keys( this.sections )[0] || null;
        }

        initObservers() 
        {
            const self = this;
            const $sortedSections = Object.keys( this.sections ).sort( ( a, b ) => 
            {
                return self.sections[ a ].offset - self.sections[ b ].offset;
            });

            $( window ).on( 'scroll.jetScrollNavigation', () => 
            {
                if ( this.isScrolling ) 
                    return;

                const $scrollTop = $( window ).scrollTop();
                const $windowHeight = $( window ).height();

                for ( let i = 0; i < $sortedSections.length; i++ ) 
                {
                    const $sectionId = $sortedSections[i];
                    const $section = self.sections[ $sectionId ];
                    const $sectionTop = $section.offset;
                    const $sectionBottom = $section.offset + $section.height;

                    if ( $scrollTop + 1 >= $sectionTop - 1 && $scrollTop < $sectionBottom ) 
                    {
                        self.activateSection( $sectionId );
                        break;
                    }
                }

                const $maxScroll = $( document ).height() - $windowHeight;
                if ( $scrollTop <= 0 ) 
                    self.activateSection( $sortedSections[0] );
                if ( $scrollTop >= $maxScroll ) 
                    self.activateSection( $sortedSections[ $sortedSections.length - 1 ] );
            });
        }

        activateSection( $sectionId ) 
        {
            if ( ! this.sections[ $sectionId ] ) 
                return;
        
            this.currentSection = $sectionId;
            this.$itemsList.removeClass( 'active invert' );
            $( '[data-anchor="' + $sectionId + '"]', this.$instance ).addClass( 'active' );
        
            if ( this.sections[ $sectionId ].invert ) 
                this.$itemsList.addClass( 'invert' );
        
            if ( ! this.settings.sectionIdVisibility ) 
            {
                if ( history.replaceState ) 
                {
                    history.replaceState( null, null, '#' + $sectionId );
                } 
                else 
                {
                    location.hash = $sectionId;
                }
            }
        }

        scrollToSection( $sectionId ) 
        {
            if ( ! this.sections[ $sectionId ] || this.isScrolling ) 
                return;

            const $offset = this.sections[ $sectionId ].offset - this.settings.offset;
            this.isScrolling = true;
            this.activateSection( $sectionId );

            window.scrollTo(
            {
                top: $offset,
                behavior: 'smooth'
            });

            const $check = setInterval( () => 
            {
                const $scrollTop = window.scrollY || window.pageYOffset;
                const $maxScroll = document.body.scrollHeight - window.innerHeight;

                if ( Math.abs( $scrollTop - $offset ) < 2 || $scrollTop >= $maxScroll || $scrollTop <= 0 ) 
                {
                    clearInterval( $check );
                    this.isScrolling = false;
                }
            }, 50 );
        }

        enableWheelControl() 
        {
            const self = this;
            const $isMobile = JetElementsTools.mobileAndTabletcheck();

            if ( !$isMobile ) 
            {
                window.addEventListener( 'wheel', function ( $event ) 
                {
                    self.onWheel( $event );
                }, { passive: false });
            }

            if ( $isMobile && this.settings.sectionSwitchOnMobile ) 
            {
                let $touchStart = 0;

                window.addEventListener( 'touchstart', function ( $e ) 
                {
                    $touchStart = $e.changedTouches[0].clientY;
                }, { passive: true });

                window.addEventListener( 'touchend', function ( $e ) 
                {
                    const $delta = $e.changedTouches[0].clientY - $touchStart;
                    if ( Math.abs( $delta ) < self.settings.scroll_threshold ) 
                        return;

                    const $direction = $delta > 0 ? 'up' : 'down';
                    self.switchDirection( $direction );
                }, { passive: true });
            }
        }

        onWheel( $event ) 
        {
            if ( ! this.$scope.is( ':visible' ) || this.isScrolling ) 
                return;

            $event.preventDefault();
            const $direction = $event.deltaY < 0 ? 'up' : 'down';
            this.switchDirection( $direction );
        }

        switchDirection( $direction ) 
        {
            if ( ! this.currentSection ) 
                return;

            const $current = $( '[data-anchor="' + this.currentSection + '"]', this.$instance );
            const $target = $direction === 'up' ? $current.prev() : $current.next();

            if ( $target.length ) 
            {
                $target.trigger( 'click.jetScrollNavigation' );
            } 
            else 
            {
                const $scrollTop = window.scrollY || window.pageYOffset;
                const $maxScroll = document.body.scrollHeight - window.innerHeight;

                if ( $direction === 'up' && $scrollTop > 0 ) 
                {
                    window.scrollTo( { top: 0, behavior: 'smooth' } );
                    const $firstSection = Object.keys( this.sections )[0];
                    this.activateSection( $firstSection );
                }

                if ( $direction === 'down' && $scrollTop < $maxScroll ) 
                {
                    window.scrollTo( { top: $maxScroll, behavior: 'smooth' } );
                    const $lastSection = Object.keys( this.sections ).pop();
                    this.activateSection( $lastSection );
                }

                this.isScrolling = false;
            }
        }
    }

    window.widgetScrollNavigation = widgetScrollNavigation;

})( jQuery );
