<?php

/*
  Plugin Name:        Custom Quote Requests
  Requires Plugins:   woocommerce
  Requires PHP:       8.0
  Description:        Allows customers to send custom quotes for any WooCommerce product!
  Version:            1.0
  Author:             Nenad Todorovic
  Author URI:         https://www.upwork.com/freelancers/nenadtodorovic
  License: GPL        v2 or later
  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain:        custom-quote-request
*/

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

require_once __DIR__ . '/vendor/autoload.php';

use CQR\Plugin;

if ( !class_exists( 'CQR' ) ) :
  
  class CQR {
    
    public function __construct() {

      register_activation_hook( __FILE__, array( 'CQR\DB\Installer', 'install' ) );
      register_uninstall_hook( __FILE__, array( 'CQR\DB\Installer', 'uninstall' ) );

      add_action( 'plugins_loaded', array( $this, 'initPlugin' ) );

    }

    public function initPlugin() {
      $plugin = new Plugin( plugin_basename( __FILE__ ) );
      $plugin->init();
    }

  }

  $cqr = new CQR;

endif;  