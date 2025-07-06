<?php

namespace CQR\Admin;

use CQR\Controller\QuoteListController;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class General {

  public function init() {

    $quoteListController = new QuoteListController;

    add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAssets' ) );
    add_action( 'admin_menu', array( $quoteListController, 'quoteListPage' ) );  
    add_action( 'admin_notices', array( $quoteListController, 'showSuccessMsg' ) );
  }

  public function enqueueAssets() {

    // styles
    wp_enqueue_style( 'cqr-admin-style', CQR_URL . 'assets/css/admin.css' );    

  }

}
