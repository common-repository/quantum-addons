import "../../css/advance-slider/advance-slider.css";

class advanceSlider extends elementorModules.frontend.handlers.SwiperBase  {
   constructor( e )  {
      super( e );
      this.outerContainerSelector = "";
   }

   getDefaultSettings()  {
      return {
         selectors: {
            outerContainer: ".el-quantum-advance-slider-outer-container",
            container: ".el-quantum-advance-slider-container",
         },
      }
   }

   getDefaultElements()  {
      const selectors = this.getSettings( "selectors" );

      return {
         $container: this.$element.find( selectors.outerContainer + " " + selectors.container ),
      }
   }

   onInit()  {
      this.outerContainerSelector = "." + Array.from( this.$element[0].classList ).join( "." );
      this.initSwiper( this.getDefaultElements().$container );
   }

   async initSwiper( el )  {
      let swiperConfig = this.getSwiperConfig();

      /// Swiper elementor internal library
      const Swiper = elementorFrontend.utils.swiper;
      const newSwiperInstance = await new Swiper( el, swiperConfig );

      wp.hooks.doAction( "quantum_adslider_swiper_instance", newSwiperInstance, this.$element[0] );

      ///adding swiper instance to container with Jquery data function
      ///P.S i don't know why anyone will need this, but just leaving it right here for now
      this.getDefaultElements().$container.data( "swiper", newSwiperInstance );
   }

   getSwiperConfig()  {
      ///all the elements settings
      const settings = this.getElementSettings();
      const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;
      const desktopSlideToShow = +settings['slide_per_view'];
      const desktopCenterSlides = +settings['center_slide'] ? true : false;
      const desktopSpaceBetween = +settings['space_between'];
      const desktopSlidePerGroup = +settings['slide_per_group'];
      const isPaginationClickable = settings['is_pagination_clickable'] === 'yes' ? true : false;
      const paginationType = settings['pagination_type'];
      const isScrollbarDraggable = settings['is_scrollbar_draggable'] === 'yes' ? true : false;
      const isAutoPlayOn = settings['slide_opt_autoplay'] ? true : false;

      let customNextBtn = settings['custom_navigation_next_button_selector'];
      let customPrevBtn = settings['custom_navigation_prev_button_selector'];
      let customPagination = settings['custom_pagination_selector'];
      let customScrollbar = settings['custom_scrollbar_selector'];

      if( typeof customNextBtn === 'string' )  customNextBtn = customNextBtn.trim();

      if( typeof customPrevBtn === 'string' )  customPrevBtn = customPrevBtn.trim();

      if( typeof customPagination === 'string' )  customPagination = customPagination.trim();

      if( typeof customScrollbar === 'string' )  customScrollbar = customScrollbar.trim();

      ///add swiper scrollbar css class to custom scrollbar
      if( customScrollbar )  {
         const scrollbarEl = document.querySelector( customScrollbar );
         if( scrollbarEl )  scrollbarEl.classList.add( 'swiper-scrollbar' );
      }

      ///basic swiper config
      const swiperConfig = {
         direction: "horizontal",
         loop: settings["loop"] ? true : false,
         slidesPerView: desktopSlideToShow,
         centeredSlides: desktopCenterSlides,
         spaceBetween: desktopSpaceBetween,
         slidesPerGroup: desktopSlidePerGroup,
         pagination: {
            el: customPagination ? customPagination : ".swiper-pagination",
            clickable: isPaginationClickable,
            type: paginationType,
         },
         navigation: {
            nextEl: customNextBtn ? customNextBtn : this.outerContainerSelector + " " + ".el-quantum-next-btn",
            prevEl: customPrevBtn ? customPrevBtn : this.outerContainerSelector + " " + ".el-quantum-prev-btn",
         },
         scrollbar: {
            el: customScrollbar ? customScrollbar : ".el-quantum-slider-scrollbar",
            draggable: isScrollbarDraggable,
         },
         breakpoints: {},
         ///i saw this code in Elementor source code and this seems to
         ///"correct" the breakpoints according to swiper breakpoints
         handleElementorBreakpoints: true,
         on: {
            init: ( swiper ) => {
               wp.hooks.doAction( "quantum_adslider_initiated", swiper, this.$element[0] );
            }
         }
      }

      if( isAutoPlayOn ) {
         // default options will be applied when empty object given
         swiperConfig.autoplay = {};
         const autoplayDelay = settings['slide_opt_autoplay_delay'];
         const disableOnInteraction = settings['slide_opt_autoplay_disable_on_interaction'];
         const pauseOnMouseEnter = settings['slide_opt_autoplay_pause_on_mouseover'];

         if( autoplayDelay ) {
            swiperConfig.autoplay.delay = autoplayDelay;
         }

         if( typeof disableOnInteraction !== "undefined" ) {
            swiperConfig.autoplay.disableOnInteraction = disableOnInteraction ? true : false;
         }

         if( typeof pauseOnMouseEnter !== "undefined" ) {
            swiperConfig.autoplay.pauseOnMouseEnter = pauseOnMouseEnter ? true : false;
         }
      }

      ////add breakpoints values to swiper config
      Object.keys( elementorBreakpoints ).reverse().forEach( ( breakpointName ) =>  {
         const autoplayOpt = {};

         if( isAutoPlayOn ) {
            const autoplayDelay = settings['slide_opt_autoplay_delay_' + breakpointName];
            const disableOnInteraction = settings['slide_opt_autoplay_disable_on_interaction_' + breakpointName];
            const pauseOnMouseEnter = settings['slide_opt_autoplay_pause_on_mouseover_' + breakpointName];

            if( typeof autoplayDelay !== 'undefined' ) {
               autoplayOpt.delay = autoplayDelay;
            }

            if( typeof disableOnInteraction !== 'undefined' ) {
               autoplayOpt.disableOnInteraction = disableOnInteraction ? true : false;
            }

            if( typeof pauseOnMouseEnter !== 'undefined' ) {
               autoplayOpt.pauseOnMouseEnter = pauseOnMouseEnter ? true : false;
            }
         }

         const breakpointPixel = elementorBreakpoints[breakpointName].value;

			swiperConfig.breakpoints[breakpointPixel] = {
				slidesPerView: +settings['slide_per_view_' + breakpointName],
            centeredSlides: +settings['center_slide_' + breakpointName] ? true : false,
				spaceBetween: +settings['space_between_' + breakpointName],
				slidesPerGroup: +settings['slide_per_group_' + breakpointName],
			}

         if( Object.keys( autoplayOpt ).length > 0 ) {
            swiperConfig.breakpoints[elementorBreakpoints[ breakpointName ].value].autoplay = autoplayOpt;
         }
		});

      return wp.hooks.applyFilters( "quantum_adslider_after_init_swiper_config", swiperConfig, this.$element[0] );
   }
}

jQuery( window ).on( "elementor/frontend/init", () =>  {
   const addHandler = ( $element ) => {
      elementorFrontend.elementsHandler.addHandler( advanceSlider, { $element } );
   }

   elementorFrontend.hooks.addAction(
      "frontend/element_ready/Advance_slider.default",
      addHandler
   );
});
