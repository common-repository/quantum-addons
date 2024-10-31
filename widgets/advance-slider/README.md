# Advance Slider

**Note: This document is for advance topics only, it doesn't show everything that this addon has to offer.**

## Creating Template

**Make sure you are using [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) before creating folder or adding files.**

**Note: Template can only use for changing/alter structure of the slides.**

First [download](https://github.com/abhy12/quantum-addons/blob/master/templates/advance-slider/default.html) the ``default.html`` file and place this file in the root of the active theme location ``/quantum-addons/advance-slider/default.html`` you can rename the ``default.html`` with anything you want just make sure it has ``.html`` extension.

In that template file you have this HTML structure
```html
<div>
   <img class="el-quantum-slider-image" src="{{Image.url}}" alt="{{Image.alt}}">
   <div class="el-quantum-content-container">
      <h3 class="el-quantum-title">{{Title}}</h3>
      <p class="el-quantum-para">{{Paragraph}}</p>
      <p class="el-quantum-add-content">{{Additional_content}}</p>
   </div>
</div>
```

### Template Tags

Above in the template there are some double curly brackets``{{...}}`` character we call them **"template tag"**. Think of them as a placeholder of the content that you will be puting in the slides.

### Available Template Tags:

- ``{{Image.url}}`` for slide image URL
- ``{{Image.alt}}`` for slide image alternative text
- ``{{Title}}`` for slide heading
- ``{{Paragraph}}`` for slide paragraph
- ``{{Additional_content}}`` for slide additional content

### Custom Structure

After understanding the template tags you can create your own custom template structure just note that **you can remove any css class in the above example template but they are important if you want elementor style to be applied to those HTML element.**

## Custom Navigation Buttons

First enable the custom Navigation Buttons

![how-enable-navigation-buttons](https://user-images.githubusercontent.com/98876719/246651950-273cc61a-2483-49a0-9c95-518ffc86bac4.png)

after enabled you can see there are two input boxes, you can input your button's CSS selector into those boxes respectively.
For example you can create your custom buttons with the use of Elementor's Button element.

Just add unique ID to the buttons like this:

![elementor-button-add-id](https://user-images.githubusercontent.com/98876719/246652784-e7ab2c99-4cbf-458e-ad38-c89ce6106914.png)

And add buttons IDs to those input boxes

![adding-css-selector-to-inputs](https://user-images.githubusercontent.com/98876719/246653491-682c7e73-7b69-4550-bc24-b805297dd815.png)

I have created same buttons with above steps here is final result:

[![finised-example-of-custom-buttons](https://user-images.githubusercontent.com/98876719/246653957-d7a9603c-748f-436b-b42b-97b5dd3d60b0.png)](https://user-images.githubusercontent.com/98876719/246654370-455c1e0f-f9b7-4533-9e18-8f7b294b6c3e.mp4)

## Hooks

**Reference links**
- [Wordpress JavaScript Hooks guide](https://nebula.gearside.com/how-to-use-the-javascript-wordpress-hooks-api-without-any-extra-libraries/) (**must read**)
- [Wordpress JavaScript hooks](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-hooks/)
- [Wordpress Hooks](https://developer.wordpress.org/plugins/hooks/)

### Where to write JavaScript hooks
If you are writing this hooks on theme file make sure it's dependency array has ``wp-hooks`` value or you can use [Simple Custom CSS and JS](https://wordpress.org/plugins/custom-css-js/) or similar plugin for writing this hooks.

### JavaScript Hooks for Swiper

#### Intro
This slider has built with [Swiper](https://swiperjs.com/) slider library, it has so many options to customize your slider, but those options can't possibly be fit into this addon so we have created some hooks so you can alter the configuration depending on your needs.

#### ```quantum_adslider_after_init_swiper_config```

**Filter Hook:** Fires after swiper configuration has been initialized.

``wp.hooks.addFilter( 'hookName', 'namespace', callback: ( config, container ) => config, priority )``

##### Example:

```javascript
wp.hooks.addFilter( "quantum_adslider_after_init_swiper_config", "quantum_addons/advance_slider", changeSwiperConfig, 10 );

function changeSwiperConfig( swiperConfig, container )  {
   /* to only apply on some of the element(s) you can add css class to those widget
      https://elementor.com/help/css-classes-in-elementor/
   */
   if( container.classList.contains( "el-custom-slider" ) )  {
      swiperConfig.slidesPerView = 3;
      console.log( "this is the one", container );
   }

   // have to return the config for slider to work correctly
   return swiperConfig;
}
```

Source: [/widgets/advance-slider/js/advance-slider](https://github.com/abhy12/quantum-addons/blob/master/widgets/advance-slider/js/advance-slider.js#L113)


#### ```quantum_adslider_initiated```

**Action Hook:** Fires after slider has been initiated.

``wp.hooks.addAction( 'quantum_adslider_initiated', 'quantum_addons/advance_slider', callback: ( instance, container ) => {} )``

##### Example:
```javascript
wp.hooks.addAction( 'quantum_adslider_initiated', 'quantum_addons/advance_slider',
( instance, container ) => {
   if( container.classList.contains( "eg-slider") ) {
      // do something ...
      instance.update();
   }
});
```

#### ```quantum_adslider_swiper_instance```

**Action Hook (kind of):** Fires after swiper instance has been initialized. It will get every swiper instance on the page but not add any action on the script.

``wp.hooks.addAction( 'hookName', 'namespace', callback: ( instance, container ) => {}, priority )``


##### Example:
At the time of writing this documentation, this addon doesn't have autoplay options so we will use this example with the help this hook to implement [autoplay](https://swiperjs.com/swiper-api#autoplay).

```javascript
wp.hooks.addAction( "quantum_adslider_swiper_instance", "quantum_addons/advance_slider", getSwiperInstance, 10 );

function getSwiperInstance( instance, element )  {
   /* to only get some of the element(s) you can add css class to those widget
      https://elementor.com/help/css-classes-in-elementor/
   */
   if( element.classList.contains( "el-custom-slider") )  {
      instance.params.autoplay = {
         delay: 3000
      }

      // call update method after you change something
      instance.update();

      instance.autoplay.start();

      console.log( instance );
   }
}
```

Source: [/widgets/advance-slider/js/advance-slider](https://github.com/abhy12/quantum-addons/blob/master/widgets/advance-slider/js/advance-slider.js#L38)
