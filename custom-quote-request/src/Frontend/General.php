<?php

namespace CQR\Frontend;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

use CQR\Controller\QuoteFormController;
use CQR\Controller\ProductController;

class General {

  public function init() {
    
    // enqueues
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueueAssets' ) );

    // hooks
    $productController = new ProductController;
    $productController->productArchive();
    $productController->productSingle();

  }

  public function enqueueAssets() {

    // scripts
    wp_enqueue_script( 'cqr-frontend-script', CQR_URL . 'assets/js/frontend.js', array( 'jquery' ), null, true );

    wp_localize_script( 'cqr-frontend-script', 'ajaxify', array( 
      'url'   => admin_url( 'admin-ajax.php' ),
      'home'  => home_url(),
      'nonce' => wp_create_nonce( 'noncify' ),
    ));
    
    // styles
    wp_enqueue_style( 'cqr-frontend-style', CQR_URL . 'assets/css/frontend.css' );

  }

}
