<?php

namespace CQR\Controller;

use CQR\Model\Product;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class ProductController {

  public function __construct() {

    $product = new Product;
 
    add_action( 'wp_ajax_add_product_to_quote', array( $product, 'addProductToQuote' ) );
    add_action( 'wp_ajax_remove_product', array( $product, 'removeProduct' ) );

  }

  public function productSingle() {
    add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'showAddToQuoteBtn' ), 100, 1 );
    add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'showQuoteLink' ), 110, 1 );
  }

  public function productArchive() {
    add_action( 'woocommerce_before_shop_loop', array( $this, 'showQuoteLink' ), 100, 1 );
    add_action( 'woocommerce_after_shop_loop_item', array( $this, 'showAddToQuoteBtn' ), 100, 1 );
  }

  public function showAddToQuoteBtn() {

    if ( !is_user_logged_in() ) :
      return;
    endif;

    global $product;
    $productId      = $product->get_id();
    $productList    = get_transient( "quote_" . CURRENT_USER_ID );
    $isAddedProduct = $productList ? in_array( $productId, $productList ) : false;
    $quoteBtnTxt    = $isAddedProduct ? __( 'Added to quote', 'custom-quote-request' ) : __( 'Add to quote', 'custom-quote-request' );
    
    require CQR_DIR . 'src/View/add-quote-btn.php';

  }  

  public function showQuoteLink() {
   
    if ( !is_user_logged_in() ) :
      return;
    endif;

    $has_quote = get_transient( "quote_" . CURRENT_USER_ID ) ?? false;
    
    require CQR_DIR . 'src/View/quote-link.php';

  }

}
