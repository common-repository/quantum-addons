<?php
/**
 * Plugin Name: Quantum Addons For Elementor
 * Description: Elementor Addons Plugin
 * Version: 0.1.2
 * Author: Abhishek Yesankar
 * Author URI: https://github.com/abhy12
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: quantum-addons
 *
 * Elementor tested up to: 3.21.0
 * Elementor Pro tested up to: 3.21.0
 *
 *
 * Quantum Addons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Quantum Addons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Quantum Addons. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */


/** */ ///For IDE

if( !defined( 'ABSPATH' ) )  exit; // Exit if accessed directly.

define( 'QUANTUM_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUANTUM_URL', plugin_dir_url( __FILE__ ) );

define( 'QUANTUM_ADDONS_VERSION', wp_get_environment_type() === "production" ? 0.1 : time() );

// Register Elementor widgets
function quantum_addons_register_elementor_widgets( $widgets_manager )  {
   require_once( __DIR__ . '/widgets/advance-slider/advance-slider.php' );

   $widgets_manager->register( new \Quantum_addons\Widget\Advance_slider() );
}

add_action( 'elementor/widgets/register', 'quantum_addons_register_elementor_widgets' );
