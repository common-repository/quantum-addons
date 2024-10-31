<?php
namespace Quantum_addons\Widget;

use Elementor\Icons_Manager;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors as Global_Colors;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography as Global_Typography;

if( !defined( 'ABSPATH' ) )  {
	exit; // Exit if accessed directly.
}

require_once( QUANTUM_DIR . "functions.php" );

class Advance_slider extends \Elementor\Widget_Base  {
   public $slides_templates = [], $slides_template_names = [];
   ///not initializing here because it giving me some error
   public $template_paths = [];

   public function __construct( $data = [], $args = null )  {
      parent::__construct( $data, $args );
      wp_register_script( 'advance-slider-script', QUANTUM_URL . 'assets/js/advance-slider/minified/advance-slider.min.js', ['jquery', 'elementor-frontend', 'wp-hooks'], QUANTUM_ADDONS_VERSION, true );
      wp_register_style( 'advance-slider-styles', QUANTUM_URL . 'assets/css/advance-slider/minified/advance-slider.min.css', [], QUANTUM_ADDONS_VERSION );

      $this->template_paths = [QUANTUM_DIR . "templates/advance-slider", get_stylesheet_directory() . "/quantum-addons/advance-slider"];
      $this->init_templates();
   }

   public function init_templates()  {
      $this->slides_templates = quantum_addons_get_templates( $this->template_paths, "html" );

      foreach( $this->slides_templates as $template_key_name => $_ )  {
         $template_name = $template_key_name;

         $underscore_position = strpos( $template_name, "_" );
         if( $underscore_position )  {
            ///remove words with and after underscore from template name which was added if the current
            ///active theme has same template name as one of the plugin's default template name,
            ///the key name will remains same
            $template_name = substr( $template_name, 0, $underscore_position );
         }

         $template_name = ucfirst( preg_replace( '/[-]/', " ", $template_name ) );
         $this->slides_template_names[$template_key_name] = $template_name;
      }
   }

   private function get_template_tags_regex()  {
      return [
         'title'       => 'Title',
         'paragraph'   => 'Paragraph',
         'additional_text' => 'Additional_content',
         'image=>url' => 'Image\.url',
         'image=>alt'  => 'Image\.alt',
         'slide_link=>url' => 'Link'
      ];
   }

   public function get_name()  {
      return 'Advance_slider';
   }

   public function get_title()  {
      return esc_html__( 'Advance Slider', 'quantum-addons' );
   }

   public function get_icon()  {
      return 'eicon-post-slider';
   }

   public function get_custom_help_url() {
      return 'https://github.com/abhy12/quantum-addons/blob/master/widgets/advance-slider/README.md';
   }

   public function get_categories()  {
      return ['basic'];
   }

   public function get_keywords()  {
      return ['slider, carousel'];
   }

   public function get_script_depends()  {
      return ['advance-slider-script'];
   }

   public function get_style_depends()  {
      return ['advance-slider-styles'];
   }

   protected function register_controls()  {
      $this->start_controls_section(
         'section_template',
         [
            'label'  => esc_html__( 'Template', 'quantum-addons'),
            'tab'    => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_control(
         'select_template',
         [
            'label'       => esc_html__( 'Select Template', 'quantum-addons' ),
            'type'        => \Elementor\Controls_Manager::SELECT,
            'options'     => $this->slides_template_names,
            'default'     => 'default',
            'condition'   => [
               'is_custom_inline_template' => '',
            ]
         ],
      );

      $this->add_control(
         'is_custom_inline_template',
      [
            'label'        => esc_html__( 'Custom Inline Template', 'quantum-addons' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
				'default'      => '',
         ]
      );

      $this->add_control(
         'custom_inline_template',
         [
            'label'      => esc_html__( 'Custom Template', 'quantum-addons' ),
            'show_label' => false,
            'type'       => \Elementor\Controls_Manager::CODE,
            'language'   => 'html',
            'separator'  => 'after',
            'default'    => trim( quantum_addons_remove_html_comments( $this->slides_templates['default'] ) ),
            'condition' => [
               'is_custom_inline_template' => 'yes',
            ]
         ]
      );

      $this->add_control(
         'create_custom_template_alert',
         [
            'type'  => \Elementor\Controls_Manager::ALERT,
            'alert_type' => 'info',
            'content' => 'See how to ' . '<a target="_blank" href="https://github.com/abhy12/quantum-addons/blob/master/widgets/advance-slider/README.md#creating-template">' . esc_html__( 'Create' ) . '</a>' . ' or '. '<a target="_blank" href="https://github.com/abhy12/quantum-addons/blob/master/widgets/advance-slider/README.md#template-tags">' . esc_html__( 'Write inline' ) . '</a> template.',
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'section_container',
         [
            'label' => esc_html__( 'Content', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $repeater = new \Elementor\Repeater();

      $repeater->add_control(
         'image',
         [
            'label'   => esc_html__( 'Image', 'quantum-addons' ),
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [
               'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
         ]
      );

      $repeater->add_control(
         'title',
         [
            'label'       => esc_html__( 'Title', 'quantum-addons' ),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'placeholder' =>  esc_html__( 'Write someting...', 'quantum-addons' ),
         ]
      );

      $repeater->add_control(
         'paragraph',
         [
            'label'       => esc_html__( 'Paragraph', 'quantum-addons' ),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'placeholder' => esc_html__( 'Write someting...', 'quantum-addons' ),
            'default'     => esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry"s standard dummy text'),
         ]
      );

      $repeater->add_control(
         'additional_text',
         [
            'label'   => esc_html__( 'Additional Content', 'quantum-addons' ),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'default' => esc_html__( 'additional content...', 'quantum-addons' ),
         ]
      );

      $repeater->add_control(
         'slide_link',
         [
            'label'   => esc_html__( 'Link', 'quantum-addons' ),
            'type'    => \Elementor\Controls_Manager::URL,
            'options' => [ 'url', 'is_external', 'nofollow' ],
         ]
      );

      $this->add_control(
         'slides',
         [
            'label'   => esc_html__( 'Slides', 'quantum-addons' ),
            'type'    => \Elementor\Controls_Manager::REPEATER,
            'title_field' => '{{{title}}}',
            'fields'  => $repeater->get_controls(),
            'default' => [
               [
                  'title'   => 'Slide #1',
                  'content' => "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Delectus earum fuga nam.",
               ],
               [
                  'title'   => 'Slide #2',
                  'content' => "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Delectus earum fuga nam.",
               ],
               [
                  'title'   => 'Slide #3',
                  'content' => "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Delectus earum fuga nam.",
               ],
            ]
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'slider-options',
         [
            'label' => esc_html__( 'Slider Options', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_responsive_control(
         'slide_per_view',
         [
            'label'              => esc_html__( 'Show Slides', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::NUMBER,
            'description'        => "You can also use decimal value",
            'frontend_available' => true,
            'desktop_default'    => 3,
            'tablet_default'     => 2,
            'mobile_default'     => 1,
         ]
      );

      $this->add_responsive_control(
         'slide_per_group',
         [
            'label'             => esc_html__( 'Slides Per Swipe', 'quantum-addons' ),
            'type'              => \Elementor\Controls_Manager::SELECT,
            'description'       => "Which slides will show next, when you swipe or navigate through buttons.<br>If value is 1 then next slide will show and if value is 2 then next's next slide will show and so forth and so on.",
            'separator'         => "before",
            'options' => [
               '1' => '1',
               '2' => '2',
               '3' => '3',
               '4' => '4',
               '5' => '5',
               '6' => '6',
               '7' => '7',
               '8' => '8',
               '9' => '9',
            ],
            'desktop_default'    => '1',
            'tablet_default'     => '1',
            'mobile_default'     => '1',
            'frontend_available' => true,
         ]
      );

      $this->add_responsive_control(
         'center_slide',
         [
            'label'   => esc_html__( 'Slides Centered', 'quantum-addons' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
               '1' => 'Yes',
               '0' => 'No',
            ],
            'desktop_default'    => '1',
            'tablet_default'     => '1',
            'mobile_default'     => '1',
            'default'            => '1',
            'frontend_available' => true,
            'separator'          => "before",
         ]
      );

      $this->add_responsive_control(
         'space_between',
         [
            'label'              => esc_html__( 'Space Between Slides', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::NUMBER,
            'desktop_default'    => 30,
            'tablet_default'     => 20,
            'mobile_default'     => 10,
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'loop',
         [
            'label'              => esc_html__( 'Infinite Slides', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::SWITCHER,
            'label_on'           => esc_html__( 'Yes', 'quantum-addons' ),
            'label_off'          => esc_html__( 'No', 'quantum-addons' ),
            'return_value'       => '1',
            'default'            => '1',
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'slide_opt_autoplay',
         [
            'label'              => esc_html__( 'Autoplay', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::SWITCHER,
            'label_on'           => esc_html__( 'Yes', 'quantum-addons' ),
            'label_off'          => esc_html__( 'No', 'quantum-addons' ),
            'separator'          => 'before',
            'return_value'       => '1',
            'default'            => '1',
            'frontend_available' => true,
         ]
      );

      $this->add_responsive_control(
         'slide_opt_autoplay_delay',
         [
            'label'              => esc_html__( 'Autoplay Timing (ms)', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::NUMBER,
            'min'                => 500,
            'step'               => 100,
            'default'            => 3000,
            'frontend_available' => true,
            'condition'          => [
               'slide_opt_autoplay' => '1',
            ]
         ]
      );

      $this->add_responsive_control(
         'slide_opt_autoplay_disable_on_interaction',
         [
            'label'              => esc_html__( 'Disable Autoplay On Interaction', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::SWITCHER,
            'return_value'       => '1',
            'default'            => '',
            'frontend_available' => true,
            'condition'          => [
               'slide_opt_autoplay' => '1',
            ]
         ]
      );

      $this->add_responsive_control(
         'slide_opt_autoplay_pause_on_mouseover',
         [
            'label'              => esc_html__( 'Pause Autoplay On Hover', 'quantum-addons' ),
            'type'               => \Elementor\Controls_Manager::SWITCHER,
            'return_value'       => '1',
            'default'            => '',
            'frontend_available' => true,
            'condition'          => [
               'slide_opt_autoplay' => '1',
            ]
         ]
      );

      $this->add_control(
         'extend_slider_with_hooks_alert',
         [
            'type'  => \Elementor\Controls_Manager::ALERT,
            'alert_type' => 'info',
            'content' => "Want more options? <a href='https://github.com/abhy12/quantum-addons/tree/master/widgets/advance-slider#hooks' target='_blank'><strong>Learn More</strong></a>",
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'navigation_buttons_options',
         [
            'label' => esc_html__( 'Navigation Buttons', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_responsive_control(
         'show_navigation_next_prev_buttons',
         [
            'label'           => esc_html__( 'Show/Hide Buttons', 'quantum-addons' ),
            'type'            => \ELEMENTOR\Controls_Manager::SELECT,
            'default'         => 'block',
            'options'         => [
               'block'     => esc_html__( 'Show', 'quantum-addons' ),
               'none' => esc_html__( 'Hide', 'quantum-addons' ),
            ],
            'selectors'       => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'display: {{VALUE}};',
            ],
            'condition'       => [
               'is_custom_navigation_buttons' => ''
            ],
         ]
      );

      $this->add_responsive_control(
         'position_navigation_next_prev_buttons',
         [
            'label'           => esc_html__( 'Buttons Position', 'quantum-addons' ),
            'type'            => \ELEMENTOR\Controls_Manager::SELECT,
            'default'         => '0px',
            'options'         => [
               '0px'  => esc_html__( 'Inside', 'quantum-addons' ),
               '100%' => esc_html__( 'Outside', 'quantum-addons' ),
            ],
            'selectors'       => [
               '{{WRAPPER}} .el-quantum-slider-btn' => '--el-qunt-buttons-position: {{VALUE}};',
            ],
            'condition'  => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_responsive_control(
         "navigation_vertical_align",
         [
            'label'      => esc_html__( "Vertical Align", "quantum-addons" ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ["%", "px", "rem", "em"],
            'default'    => ['unit' => '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-btn' => "top: {{SIZE}}{{UNIT}}",
            ],
            'range'      => [
               "%" => [
                  "min" => -100,
                  "max" => 100
               ]
            ],
            'condition'  => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_responsive_control(
         "navigation_horizontal_align",
         [
            'label'      => esc_html__( "Horizontal Align", "quantum-addons" ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ["%", "px", "rem", "em"],
            'default'    => ['unit' => '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-btn.el-quantum-prev-btn' => "left: {{SIZE}}{{UNIT}}",
               '{{WRAPPER}} .el-quantum-slider-btn.el-quantum-next-btn' => "right: {{SIZE}}{{UNIT}}",
            ],
            'range'      => [
               "%" => [
                  "min" => -100,
                  "max" => 100
               ]
            ],
            'condition'  => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_control(
         'navigation_prev_button_icon',
         [
            'label'     => esc_html__( 'Previous Button Icon', 'quantum-addons' ),
            'type'      => \ELEMENTOR\Controls_Manager::ICONS,
            'skin'      => 'media',
            'default'   => [
               'value'   => 'fas fa-chevron-left',
               'library' => 'fa-solid',
            ],
            'condition' => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_control(
         'navigation_next_button_icon',
         [
            'label'     => esc_html__( 'Next Button Icon', 'quantum-addons' ),
            'type'      => \ELEMENTOR\Controls_Manager::ICONS,
            'skin'      => 'media',
            'default'   => [
               'value'   => 'fas fa-chevron-right',
               'library' => 'fa-solid',
            ],
            'condition' => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_control(
         'is_custom_navigation_buttons',
         [
            'label'     => esc_html__( 'Custom Navigation Buttons', 'quantum-addons' ),
            'type'      => \ELEMENTOR\Controls_Manager::SWITCHER,
            'label_on'  => 'Custom',
            'label_off' => 'Default',
            'default'   => '',
         ]
      );

      $this->add_control(
         'custom_navigation_prev_button_selector',
         [
            'label'              => esc_html__( 'Previous Button Selector', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::TEXT,
            'description'        => esc_html__( 'Input CSS selector eg: .custom-prev-btn or #custom-prev-btn', 'quantum-addons' ),
            'placeholder'        => esc_html__( '.custom-prev-btn', 'quantum-addons' ),
            'label_block'        => true,
            'condition'          => [
               'is_custom_navigation_buttons' => 'yes'
            ],
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'custom_navigation_next_button_selector',
         [
            'label'              => esc_html__( 'Next Button Selector', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::TEXT,
            'description'        => esc_html__( 'Input CSS selector eg: .custom-next-btn or #custom-next-btn', 'quantum-addons' ),
            'placeholder'        => esc_html__( '.custom-next-btn', 'quantum-addons' ),
            'label_block'        => true,
            'condition'          => [
               'is_custom_navigation_buttons' => 'yes'
            ],
            'frontend_available' => true,
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'pagination_options',
         [
            'label' => esc_html__( 'Pagination Dots', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_responsive_control(
         'show_pagination',
         [
            'label'           => esc_html__( 'Show/Hide Pagination', 'quantum-addons' ),
            'type'            => \ELEMENTOR\Controls_Manager::SELECT,
            'default'         => 'block',
            'options'         => [
               'block'     => esc_html__( 'Show', 'quantum-addons' ),
               'none' => esc_html__( 'Hide', 'quantum-addons' ),
            ],
            'selectors'       => [
               '{{WRAPPER}} .el-quantum-advance-slider-container .el-quantum-slider-pagination' => 'display: {{VALUE}};',
            ],
            'condition'       => [
               'is_custom_pagination' => ''
            ]
         ]
      );

      $this->add_control(
         'is_custom_pagination',
         [
            'label'     => esc_html__( 'Custom Pagination', 'quantum-addons' ),
            'type'      => \ELEMENTOR\Controls_Manager::SWITCHER,
            'label_on'  => 'Custom',
            'label_off' => 'Default',
            'default'   => '',
         ]
      );

      $this->add_control(
         'custom_pagination_selector',
         [
            'label'              => esc_html__( 'Custom Pagination Selector', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::TEXT,
            'description'        => esc_html__( 'Input CSS selector eg: .custom-pagination or #custom-pagination', 'quantum-addons' ),
            'placeholder'        => esc_html__( '.custom-pagination', 'quantum-addons' ),
            'label_block'        => true,
            'condition'          => [
               'is_custom_pagination' => 'yes'
            ],
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'is_pagination_clickable',
         [
            'label'              => esc_html__( 'Clickable', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::SWITCHER,
            'separator'          => 'before',
            'label_on'           => esc_html__( 'Yes', 'quantum-addons' ),
            'label_off'          => esc_html__( 'No', 'quantum-addons' ),
            'default'            => 'yes',
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'pagination_type',
         [
            'label'              => esc_html__( 'Pagination type', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::SELECT,
            'default'            => 'bullets',
            'options'            => [
               'bullets'     => esc_html__( 'Dots', 'quantum-addons' ),
               'fraction'    => esc_html__( 'Numbers', 'quantum-addons' ),
               'progressbar' => esc_html__( 'Progressbar', 'quantum-addons' ),
            ],
            'frontend_available' => true,
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'pagination_scrollbar_options',
         [
            'label' => esc_html__( 'Scrollbar', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_responsive_control(
         'show_pagination_scrollbar',
         [
            'label'           => esc_html__( 'Show/hide Scrollbar', 'quantum-addons' ),
            'type'            => \ELEMENTOR\Controls_Manager::SELECT,
            'default'         => 'block',
            'options'         => [
               'block'     => esc_html__( 'Show', 'quantum-addons' ),
               'none' => esc_html__( 'Hide', 'quantum-addons' ),
            ],
            'selectors'       => [
               '{{WRAPPER}} .el-quantum-advance-slider-container .el-quantum-slider-scrollbar' => 'display: {{VALUE}};',
            ],
            'condition'       => [
               'is_custom_scrollbar' => ''
            ]
         ]
      );

      $this->add_control(
         'is_custom_scrollbar',
         [
            'label'     => esc_html__( 'Custom Scrollbar', 'quantum-addons' ),
            'type'      => \ELEMENTOR\Controls_Manager::SWITCHER,
            'label_on'  => 'Custom',
            'label_off' => 'Default',
            'default'   => '',
         ]
      );

      $this->add_control(
         'custom_scrollbar_selector',
         [
            'label'              => esc_html__( 'Custom Scrollbar Selector', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::TEXT,
            'description'        => esc_html__( 'Input CSS selector eg: .custom-scrollbar or #custom-scrollbar', 'quantum-addons' ),
            'placeholder'        => esc_html__( '.custom-scrollbar', 'quantum-addons' ),
            'label_block'        => true,
            'condition'          => [
               'is_custom_scrollbar' => 'yes'
            ],
            'frontend_available' => true,
         ]
      );

      $this->add_control(
         'is_scrollbar_draggable',
         [
            'label'              => esc_html__( 'Draggable', 'quantum-addons' ),
            'type'               => \ELEMENTOR\Controls_Manager::SWITCHER,
            'separator'          => 'before',
            'label_on'           => esc_html__( 'Yes', 'quantum-addons' ),
            'label_off'          => esc_html__( 'No', 'quantum-addons' ),
            'default'            => 'yes',
            'frontend_available' => true,
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'global_link_options',
         [
            'label' => esc_html__( 'Link', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
         ]
      );

      $this->add_control(
         'global_wrap_link_to_slide',
         [
            'label'        => esc_html__( 'Wrap to Whole Slide', 'quantum-addons' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => '',
            'return_value' => 'yes',
         ]
      );

      $this->add_control(
         'global_wrap_link_warning',
         [
            'type' => \Elementor\Controls_Manager::ALERT,
            'alert_type' => 'warning',
            'content'  => esc_html__( 'Don\'t add any ancher tags into the template if you selecting it to "yes". If you do it will be removed.', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'global_link_open_to_new_window',
         [
            'label'        => esc_html__( 'Open to New Window', 'quantum-addons' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => '',
            'return_value' => 'yes',
         ]
      );

      $this->add_control(
         'global_link_nofollow',
         [
            'label'        => esc_html__( 'No Follow', 'quantum-addons' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => '',
            'return_value' => 'yes',
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'container_style',
         [
            'label' => esc_html__( 'Slides Container', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_control(
         'container_overflow',
         [
            'label'     => esc_html__( 'Overflow', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'default'   => 'hidden',
            'options'   => [
               'hidden'  => 'Hidden',
               'visible' => 'Visible',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-advance-slider-container' => 'overflow: {{VALUE}}'
            ],
         ]
      );

      $this->add_responsive_control(
         'container_padding',
         [
            'label'       => esc_html__( 'Padding', 'quantum-addons' ),
            'type'        => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units'  => ['px', 'rem' ,'em', '%'],
            'description' => esc_html__( 'Note: if you change padding you may want to reload the browser (if slider not working properly)', 'quantum-addons' ),
            'selectors'   => [
               '{{WRAPPER}} .el-quantum-advance-slider-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'container_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-advance-slider-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'slide_style',
         [
            'label' => esc_html__( 'Slide', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Border::get_type(),
         [
            'name'     => 'border',
            'label'    => esc_html__( 'Border', 'quantum-addons' ),
            'selector' => '{{WRAPPER}} .el-quantum-slide',
         ]
      );

      $this->add_responsive_control(
         'slide_style_border_radius',
         [
            'label'      => esc_html__( 'Border Radius', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Box_Shadow::get_type(),
         [
            'name'     => 'box_shadow',
            'label'    => esc_html__( 'Box Shadow', 'quantum-addons' ),
            'selector' => '{{WRAPPER}} .el-quantum-slide',
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Background::get_type(),
         [
            'name'     => 'background',
            'label'    => esc_html__( 'Background', 'quantum-addons' ),
            'types'    => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .el-quantum-slide',
         ]
      );

      $this->add_responsive_control(
         'slide_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'image_style',
         [
            'label' => 'Image',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_control(
         'image_width',
         [
            'label'      => 'Image Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'rem', 'em'],
            'default'    => [
               'unit' => '%',
               'size' => '100'
            ],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-image' => 'width: {{SIZE}}{{UNIT}};'
            ]
         ]
      );

      $this->add_control(
         'image_height',
         [
            'label'     => esc_html__( 'Image Height', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'rem', 'em'],
            'default'    => [
               'unit' => 'px',
               'size' => '250'
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-image' => 'height: {{SIZE}}{{UNIT}};'
            ],
         ]
      );

      $this->add_control(
         'object-fit',
         [
            'label'     => esc_html__( 'Object Fit', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [
               'cover'      => 'Cover',
               'contain'    => 'Contain',
               'fill'       => 'Fill',
               'none'       => 'None',
               'scale-down' => 'Scale down'
            ],
            'default'   => 'cover',
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-image'  => 'object-fit: {{VALUE}}'
            ],
            'condition' => [
               'image_height!' => '',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'content_section',
         [
            'label' => esc_html__( 'Slide Content Container', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Background::get_type(),
         [
            'name'     => 'content_background',
            'label'    => esc_html__( 'Background', 'quantum-addons' ),
            'types'    => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .el-quantum-content-container',
         ]
      );

      $this->add_responsive_control(
         'content_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'content_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-content-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Border::get_type(),
         [
            'name'     => 'content-cotainer-border',
            'label'    => esc_html__( 'Border', 'quantum-addons' ),
            'selector' => '{{WRAPPER}} .el-quantum-content-container',
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'heading_style',
         [
            'label' => esc_html__( 'Title', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Typography::get_type(),
         [
            'label'   => esc_html__( 'Typography', 'quantum-addons' ),
            'name'     => 'content_typography',
            'selector' => '{{WRAPPER}} .el-quantum-title',
            'global' => [
               'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
         ]
      );

      $this->add_responsive_control(
         'heading_text_align',
			[
				'label' => esc_html__( 'Alignment', 'quantum-addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'quantum-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'quantum-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'quantum-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .el-quantum-title' => 'text-align: {{VALUE}};',
				],
			]
      );

      $this->start_controls_tabs(
         'heading_styles'
      );

      $this->start_controls_tab(
         'heading_style_normal',
         [
            'label' => esc_html__( 'Normal', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'heading_normal_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-title' => 'color: {{VALUE}}',
            ],
            'global' => [
               'default' => Global_Colors::COLOR_PRIMARY,
            ]
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'heading_style_hover',
         [
            'label' => esc_html__( 'Hover', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'heading_hover_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-title:hover' => 'color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->end_controls_tabs();

      $this->add_responsive_control(
         'title_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'rem' ,'em', '%' ],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'title_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'paragraph_style',
         [
            'label' => esc_html__( 'Paragraph', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_responsive_control(
         'paragraph_text_align',
			[
				'label' => esc_html__( 'Alignment', 'quantum-addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'quantum-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'quantum-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'quantum-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .el-quantum-para' => 'text-align: {{VALUE}};',
				],
			]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Typography::get_type(),
         [
            'label'    => esc_html__( 'Typography', 'quantum-addons' ),
            'name'     => 'paragraph_typography',
            'selector' => '{{WRAPPER}} .el-quantum-para',
            'global' => [
               'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
            ],
         ]
      );

      $this->start_controls_tabs(
         'paragraph_styles'
      );

      $this->start_controls_tab(
         'paragraph_style_normal',
         [
            'label' => esc_html__( 'Normal', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'paragraph_normal_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-para' => 'color: {{VALUE}}',
            ],
            'global' => [
               'default' => Global_Colors::COLOR_SECONDARY,
            ],
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'paragraph_style_hover',
         [
            'label' => esc_html__( 'Hover', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'paragraph_hover_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-para:hover' => 'color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->end_controls_tabs();

      $this->add_responsive_control(
         'paragraph_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-para' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'paragraph_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-para' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      ///Additional Content Style Tab

      $this->start_controls_section(
         'additional_style',
         [
            'label' => esc_html__( 'Additional Content', 'quantum-addons' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
         ]
      );

      $this->add_responsive_control(
         'additional_content_text_align',
			[
				'label' => esc_html__( 'Alignment', 'quantum-addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'quantum-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'quantum-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'quantum-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .el-quantum-add-content' => 'text-align: {{VALUE}};',
				],
			]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Typography::get_type(),
         [
            'label'    => esc_html__( 'Typography', 'quantum-addons' ),
            'name'     => 'additional_typography',
            'selector' => '{{WRAPPER}} .el-quantum-add-content',
            'global' => [
               'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
         ]
      );

      $this->start_controls_tabs(
         'additional_styles'
      );

      $this->start_controls_tab(
         'additional_style_normal',
         [
            'label' => esc_html__( 'Normal', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'additional_normal_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-add-content' => 'color: {{VALUE}}',
            ],
            'global' => [
               'default' => Global_Colors::COLOR_TEXT,
            ],
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'additional_style_hover',
         [
            'label' => esc_html__( 'Hover', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'additional_hover_color',
         [
            'label'     => esc_html__( 'Text Color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-add-content:hover' => 'color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->end_controls_tabs();

      $this->add_responsive_control(
         'additional_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-add-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'additional_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-add-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'style_navigation_controls',
         [
            'label' => esc_html__( 'Navigation Buttons', 'quantum-addons' ),
            'tab'   => \ELEMENTOR\Controls_Manager::TAB_STYLE
         ]
      );

      $this->add_responsive_control(
         'navigation_buttons_icon_size',
         [
            'label'      => 'Icon Size',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'rem', 'em'],
            'default'    => [
               'unit' => 'rem',
               'size' => '1.2'
            ],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'font-size: {{SIZE}}{{UNIT}};',
               '{{WRAPPER}} .el-quantum-slider-btn svg' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
               'is_custom_navigation_buttons' => ''
            ]
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Border::get_type(),
         [
            'name'     => 'style_navigation_controls_border',
            'exclude'  => ['color'],
            'selector' => '{{WRAPPER}} .el-quantum-slider-btn',
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Box_Shadow::get_type(),
         [
            'name'     => 'navigation_controls_box_shadow',
            'label'    => esc_html__( 'Box Shadow', 'quantum-addons' ),
            'selector' => '{{WRAPPER}} .el-quantum-slider-btn',
         ]
      );

      $this->start_controls_tabs( 'style_navigation_controls_colors_tab' );

      $this->start_controls_tab(
         'style_navigation_controls_normal_colors_tab',
         [
            'label' => esc_html__( 'Normal', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'style_navigation_controls_normal_icon_color',
         [
            'label'     => esc_html__( 'Icon color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'color: {{VALUE}}',
            ],
            'global' => [
               'default' => Global_Colors::COLOR_SECONDARY,
            ],
         ]
      );

      $this->add_control(
         'style_navigation_controls_normal_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'background-color: {{VALUE}}',
            ],
            'global' => [
               'default' => Global_Colors::COLOR_ACCENT,
            ],
         ]
      );

      $this->add_control(
         'style_navigation_controls_normal_border_color',
         [
            'label'     => esc_html__( 'Border color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'border-color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'style_navigation_controls_hover_colors_tab',
         [
            'label' => esc_html__( 'Hover', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'style_navigation_controls_hover_icon_color',
         [
            'label'     => esc_html__( 'Icon color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn:hover' => 'color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_navigation_controls_hover_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn:hover' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_navigation_controls_hover_border_color',
         [
            'label'     => esc_html__( 'Border color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-btn:hover' => 'border-color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->end_controls_tabs();

      $this->add_responsive_control(
         'style_navigation_controls_padding',
         [
            'label'      => esc_html__( 'Padding', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'separator'  => 'before',
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'style_navigation_controls_border_radius',
         [
            'label'      => esc_html__( 'Border Radius', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'style_pagination',
         [
            'label' => esc_html__( 'Pagination Dots', 'quantum-addons' ),
            'tab'   => \ELEMENTOR\Controls_Manager::TAB_STYLE
         ]
      );

      $this->add_control(
			'style_pagination_horizontal_alignment',
			[
				'label' => esc_html__( 'Alignment', 'quantum-addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'quantum-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'quantum-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'quantum-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .el-quantum-slider-pagination' => 'text-align: {{VALUE}};',
				],
            'condition' => [
               'pagination_type!' => 'progressbar',
            ],
			]
		);

      $this->add_group_control(
         \Elementor\Group_Control_Border::get_type(),
         [
            'name'     => 'style_pagination_dots_border',
            'exclude'  => ['color'],
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selector' => '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet',
         ]
      );

      $this->add_responsive_control(
         'style_pagination_dots_border_radius',
         [
            'label'      => esc_html__( 'Border Radius', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
               'pagination_type' => 'bullets',
            ],
         ]
      );

      $this->add_group_control(
         \Elementor\Group_Control_Box_Shadow::get_type(),
         [
            'name'     => 'style_pagination_dots_box_shadow',
            'label'    => esc_html__( 'Box Shadow', 'quantum-addons' ),
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selector' => '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet',
         ]
      );

      $this->start_controls_tabs( 'style_pagination_dots_colors_tab' );

      $this->start_controls_tab(
         'style_pagination_dots_normal_colors_tab',
         [
            'label' => esc_html__( 'Normal', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'style_pagination_dots_normal_border_color',
         [
            'label'     => esc_html__( 'Border color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet' => 'border-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_pagination_dots_normal_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#00000061',
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'style_pagination_dots_hover_colors_tab',
         [
            'label' => esc_html__( 'Hover', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'style_pagination_dots_hover_border_color',
         [
            'label'     => esc_html__( 'Border color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_pagination_dots_hover_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->start_controls_tab(
         'style_pagination_dots_active_colors_tab',
         [
            'label' => esc_html__( 'Active', 'quantum-addons' ),
         ]
      );

      $this->add_control(
         'style_pagination_dots_active_border_color',
         [
            'label'     => esc_html__( 'Border color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'border-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_pagination_dots_active_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#000',
            'condition' => [
               'pagination_type' => 'bullets',
            ],
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->end_controls_tab();

      $this->end_controls_tabs();

      $this->add_responsive_control(
         'style_pagination_dots_size',
         [
            'label'      => 'Dot Size',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'rem', 'em'],
            'default'    => [
               'unit' => 'px',
               'size' => '6'
            ],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
            ],
            'condition' => [
               'pagination_type' => 'bullets',
            ],
         ]
      );

      $this->add_responsive_control(
         'style_pagination_dots_space',
         [
            'label'      => esc_html__( 'Space Between', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'rem', 'em'],
            'default'    => [
               'unit' => 'px',
               'size' => '4'
            ],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-pagination .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
               'pagination_type' => 'bullets',
            ],
         ]
      );

      $this->add_responsive_control(
         'style_pagination_dots_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();

      $this->start_controls_section(
         'style_scrollbar',
         [
            'label' => esc_html__( 'Scrollbar', 'quantum-addons' ),
            'tab'   => \ELEMENTOR\Controls_Manager::TAB_STYLE
         ]
      );

      $this->add_control(
         'style_scrollbar_background_color',
         [
            'label'     => esc_html__( 'Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(0, 0, 0, 0.1)',
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-scrollbar' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_control(
         'style_scrollbar_moveable_element_background_color',
         [
            'label'     => esc_html__( 'Moveable Scrollbar Background color', 'quantum-addons' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => 'rgba(0, 0, 0, 0.5)',
            'selectors' => [
               '{{WRAPPER}} .el-quantum-slider-scrollbar .swiper-scrollbar-drag' => 'background-color: {{VALUE}}',
            ],
         ]
      );

      $this->add_responsive_control(
         "style_scrollbar_height",
         [
            'label'      => esc_html__( "Height", "quantum-addons" ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ["px"],
            'default'    => ['unit' => 'px'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-scrollbar' => "height: {{SIZE}}{{UNIT}}",
            ],
            'range'      => [
               "px" => [
                  "min" => 3,
                  "max" => 20
               ]
            ],
         ]
      );

      $this->add_responsive_control(
         'style_scrollbar_border_radius',
         [
            'label'      => esc_html__( 'Border Radius', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-scrollbar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
               '{{WRAPPER}} .el-quantum-slider-scrollbar .swiper-scrollbar-drag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->add_responsive_control(
         'style_scrollbar_margin',
         [
            'label'      => esc_html__( 'Margin', 'quantum-addons' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'rem' ,'em', '%'],
            'selectors'  => [
               '{{WRAPPER}} .el-quantum-slider-scrollbar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
         ]
      );

      $this->end_controls_section();
   }

   protected function render()  {
      $settings = $this->get_settings_for_display();
      $slides = $settings['slides'];
      $current_template = $settings['is_custom_inline_template'] ? $settings['custom_inline_template'] : $this->slides_templates[$settings['select_template']];
      $global_wrap_link_to_slide = $settings['global_wrap_link_to_slide'];
      $global_link_open_to_new_window = $settings['global_link_open_to_new_window'];
      $global_link_nofollow = $settings['global_link_nofollow'];

      $to_new_window_attr = "target='_blank'";
      $no_follow_attr = "rel='nofollow'";
      ?>
      <div class="el-quantum-advance-slider-outer-container">
         <div class="swiper-container el-quantum-advance-slider-container">
            <div class="swiper-wrapper">
               <?php
               if( isset( $current_template ) )  {

                  // remove any ancher tags in the template if link is wrapping around the slides
                  if( $global_wrap_link_to_slide === 'yes' )  {
                     $current_template = quantum_addons_remove_ancher_tags( $current_template );
                  }

                  foreach( $slides as $slide )  {
                     $slide_link = $slide['slide_link']['url'];
                     $slide_open_link_to_new_window = $slide['slide_link']['is_external'];
                     $slide_link_no_follow = $slide['slide_link']['nofollow'];
                     $ancher_or_div_tag = "div";
                     $slide_classes = "swiper-slide el-quantum-slide";
                     $ancher_attributes = "";

                     if( $global_wrap_link_to_slide === 'yes' && $slide_link !== '' )  {
                        $ancher_or_div_tag = "a";

                        $ancher_attributes .= " ";
                        $ancher_attributes .= 'href="' . esc_attr( esc_url( $slide_link ) ) . '"';
                        $ancher_attributes .= " ";

                        if( $slide_open_link_to_new_window || $global_link_open_to_new_window === 'yes' )  {
                           $ancher_attributes .= $to_new_window_attr;
                           $ancher_attributes .= " ";
                        }

                        if( $slide_link_no_follow || $global_link_nofollow === 'yes' )  {
                           $ancher_attributes .= $no_follow_attr;
                           $ancher_attributes .= " ";
                        }

                        $slide_classes .= " ";
                        $slide_classes .= "slide-link";
                     }

                     $ancher_or_div_tag .= " ";

                     $temp_template = quantum_addons_parse_template( $current_template, $this->get_template_tags_regex(), $slide );

                     if( $slide_open_link_to_new_window )  {
                        $temp_template = preg_replace( '/{{Link\.target}}/', $to_new_window_attr , $temp_template );
                     } else if( !$slide_open_link_to_new_window )  {
                        $temp_template = preg_replace( '/{{Link\.target}}/', '' , $temp_template );
                     }

                     if( $slide_link_no_follow )  {
                        $temp_template = preg_replace( '/{{Link\.nofollow}}/', $no_follow_attr, $temp_template );
                     } else if( !$slide_link_no_follow )  {
                        $temp_template = preg_replace( '/{{Link\.nofollow}}/', '', $temp_template );
                     }

                     echo wp_kses_post(
                        '<' . $ancher_or_div_tag . $ancher_attributes . 'class="' . esc_attr( $slide_classes ) . '">'
                           . $temp_template .
                        '</'. $ancher_or_div_tag . '>'
                     );
                  }
               }
               ?>
            </div>

            <?php
            if( isset( $settings['is_custom_pagination'] ) && $settings['is_custom_pagination'] === '' )  { ?>
               <div class="el-quantum-slider-pagination swiper-pagination"></div>
            <?php }

            if( isset( $settings['is_custom_scrollbar'] ) && $settings['is_custom_scrollbar'] === '' )  { ?>
               <div class="el-quantum-slider-scrollbar swiper-scrollbar"></div>
            <?php } ?>
         </div>

         <?php
         if( isset( $settings['is_custom_navigation_buttons'] ) && $settings['is_custom_navigation_buttons'] === '' )  { ?>
            <button class="el-quantum-slider-btn el-quantum-prev-btn">
               <?php Icons_Manager::render_icon( $settings['navigation_prev_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
            </button>
            <button class="el-quantum-slider-btn el-quantum-next-btn">
               <?php Icons_Manager::render_icon( $settings['navigation_next_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
            </button>
         <?php } ?>
      </div>
      <?php
   }
}
