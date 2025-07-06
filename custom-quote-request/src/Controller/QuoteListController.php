<?php

namespace CQR\Controller;

use CQR\Model\Quote;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class QuoteListController {

  private $quote;

  public function __construct() {
    $this->quote = new Quote;
    add_action( 'admin_post_save_quotes', array( $this, 'saveQuotes' ) );
  }

  public function saveQuotes() {

    // only allow if nonce is set & user can manage options
    if ( 
      !current_user_can( 'manage_options' ) || 
      !wp_verify_nonce( $_POST['quote_form_nonce'], 'quote_form_action' ) 
    ) :
      wp_die( __( 'You need to go back, ASAP', 'custom-quote-request' ) );
    endif;

    $postData = $_POST ?? null;

    // update the quote status if post data
    if ( $postData ) :
      
      foreach ( $postData as $key => $data ) :
        
        if ( str_starts_with( $key, 'status' ) ) :
          
          $keyArr = explode( '_', $key );
          $reqId = ( int ) $keyArr[1];
          $this->quote->updateQuoteReqStatus( $reqId, $data );

        endif;

      endforeach;

    endif;

    // redirect with success
    wp_safe_redirect( get_admin_url( null, 'admin.php?page=quote-list&status=success' ) );
    die;

  }

  public function showSuccessMsg() {

    if ( $_GET['page'] == 'quote-list' && $_GET['status'] == 'success' ) :
      require CQR_DIR . 'src/View/quote-list-updated.php';
    endif;

  }

  public function quoteListPage() {

    add_submenu_page(
      'woocommerce',
      __( 'Quote List', 'custom-quote-request' ),
      __( 'Quote List', 'custom-quote-request' ),
      'manage_options',
      'quote-list',
      array( $this, 'renderQuoteListPage' )
    );

  }

  public function getQuoteItems( int $id ) {
    return $this->quote->getQuoteItems( $id );
  }

  public function renderQuoteListPage() {

    $requests   = $this->quote->getQuoteRequests();
    $statusList = array(
    'cancelled'  => __( 'Cancelled', 'custom-quote-request' ),
    'pending'    => __( 'Pending', 'custom-quote-request' ),
    'processing' => __( 'Processing', 'custom-quote-request' ),
    'completed'  => __( 'Completed', 'custom-quote-request' ),
    );

    require CQR_DIR . 'src/View/quote-list.php';

  }

}
