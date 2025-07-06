<?php

namespace CQR\Model;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class Product {

  public function addProductToQuote() {
    
    // stop if nonce check fails
    if ( !wp_verify_nonce( $_POST['nonce'], 'noncify' ) ) :
      die;
    endif;

    $productId = $_POST['id'] ?? 0;

    // save products in transients
    if ( !get_transient( "quote_" . CURRENT_USER_ID ) ) :
      $productList[] = $productId;
      $initRequest   = true;
    else : 
      $productList = get_transient( "quote_" . CURRENT_USER_ID );
      if ( !in_array( $productId, $productList ) ) :
        $productList[] = $productId;
      endif;
    endif;

    set_transient( "quote_" . CURRENT_USER_ID, $productList, DAY_IN_SECONDS );
    
    // init request - add URL to quote page
    if ( $initRequest ) :

      ob_start();

      ?>

        <a class="quote-page" href="<?php echo home_url( '/quote-review' ); ?>">

          <img src="<?php echo CQR_URL . '/assets/img/quote-icon.svg' ?>" alt="<?php _e( 'Quote Icon', 'custom-quote-request' ) ?>">
          <span><?php	_e( 'Quote review', 'custom-quote-request' ) ?></span>

        </a>
        
      <?php

      $reviewPageLink = ob_get_clean();

      print_r( $reviewPageLink );
      
    // quote page URL is already set
    else : 

      print_r( 0 );
    
    endif;

    die;

  }

  public function removeProduct() {
   
    // stop if nonce check fails
    if ( !wp_verify_nonce( $_POST['nonce'], 'noncify' ) ) :
      die;
    endif;
    
    // remove the product from transients
    $productId = ( $_POST['productId'] ) ? ( string ) $_POST['productId'] : null;

    // try to update the transient
    try {
      
      $currentProducts = get_transient( "quote_" . CURRENT_USER_ID );
      $key             = ( int ) array_search( $productId, $currentProducts, true );

      unset( $currentProducts[$key] );
      $quoteUpdated = set_transient( "quote_" . CURRENT_USER_ID, $currentProducts, DAY_IN_SECONDS );

      if ( !$quoteUpdated ) :
        throw new \Exception( __( 'Failed to update the quote review list!', 'custom-quote-request' ) );
      endif; 

      print_r( 'success' );

    }
    
    // catch if we fail
    catch ( \Exception $e ) {
      
      // error msg
      $error_msg = $e->getMessage();
      print_r( 'fail' );
      error_log( $error_msg );

    }
    
    die;
    
  }

}
