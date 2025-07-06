<?php

namespace CQR\Model;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class Quote {

  public function submitQuoteData() {
    
    // stop if nonce check fails
    if ( !wp_verify_nonce( $_POST['nonce'], 'noncify' ) ) :
      die;
    endif;

    // parse the form data
    parse_str( $_POST['formData'], $formData );

    if ( !$formData ) {
      return;
    }

    // prepare data for quote request & items
    global $wpdb;
    
    $name   = ( $formData['name'] )  ? sanitize_text_field( $formData['name'] )  : null;
    $phone  = ( $formData['phone'] ) ? sanitize_text_field( $formData['phone'] ) : null;
    $email  = ( $formData['email'] ) ? sanitize_text_field( $formData['email'] ) : null;
    
    $quoteFormat = array( '%d', '%s', '%s', '%s', '%s' );
    $quoteData   = array(
      'user_id'       => CURRENT_USER_ID,
      'customer_name' => $name,
      'phone_number'  => $phone,
      'email'         => $email,
      'status'        => 'pending',
    );

    // try to add the data in db
    try {

      // transaction begin
      $wpdb->query( 'START TRANSACTION' ); 

      // add quote request
      $updatedQuote = $wpdb->insert( $wpdb->prefix . 'quote_requests', $quoteData, $quoteFormat );
      if ( !$updatedQuote ) :
        throw new \Exception( __( 'Quote insertion failed! Failed to create quote: ', 'custom-quote-request' ) . $wpdb->last_error );
      endif;

      $quoteId = $wpdb->insert_id;

      // add quote items
      foreach ( $formData as $key => $data ) :
        
        // skip all the non-product items data in a loop
        if ( str_starts_with( $key, 'quote_num' ) ) :

          $productId = ( int ) str_replace( 'quote_num_', '', $key );

          $productFormat = array( '%d', '%d', '%d', '%s' );
          $productData   = array(
            'request_id' => $quoteId,
            'product_id' => $productId,
            'quantity'   => ( int ) $formData["quote_num_$productId"],
            'notes'      => sanitize_textarea_field( $formData["quote_notes_$productId"] ),
          );

          $updatedItems = $wpdb->insert( $wpdb->prefix . 'quote_items', $productData, $productFormat );
          if ( !$updatedItems ) :
            throw new \Exception( __( 'Quote items insertion failed! Failed to add quote items: ', 'custom-quote-request' ) . $wpdb->last_error );
          endif;        

        endif;

      endforeach;

      // transaction commit
      $wpdb->query( 'COMMIT' );

      // remove quotes from options
      delete_transient( "quote_" . CURRENT_USER_ID );

      // success msg
      $output     = json_encode( array(
        'success' => 1,
        'msg'     => __( 'Thanks for submitting the quote! We will be reaching out to you shortly.', 'custom-quote-request' )
      ) );      
      print_r( $output );

    } 
    
    // catch if we fail
    catch ( \Exception $e ) {

      // transaction rollback
      $wpdb->query( 'ROLLBACK' );
      
      // error msg
      $errorMsg = $e->getMessage();
      $output    = json_encode( array(
        'success' => 0,
        'msg'     => __( 'We were unable to create the quote!', 'custom-quote-request' )
      ) );

      print_r( $output );
      error_log( $errorMsg );

    }

    die;

  }  
  
  public function getQuoteRequests() {

    global $wpdb;
    $reqTable = $wpdb->prefix . 'quote_requests';

    // try to get all quote requests
    try {
      
      $requests = $wpdb->get_results(
        "
          SELECT *
          FROM $reqTable
        ",
        ARRAY_A
      );

      if ( !$requests ) :
        throw new \Exception( __( 'Failed to fetch all requests: ', 'custom-quote-request' ) . $wpdb->last_error );        
      endif;
      
    } 
    
    // catch if we fail
    catch ( \Exception $e ) {

      $errorMsg = $e->getMessage();
      error_log( $errorMsg );
      
    }

    return $requests;

  }

  public function getQuoteItems( int $id ) {

    global $wpdb;
    $itemTable = $wpdb->prefix . 'quote_items';

    // try to get all quote items
    try {
      
      $requests = $wpdb->get_results(
        "
          SELECT *
          FROM $itemTable
          WHERE request_id = $id
        ",
        ARRAY_A
      );

      if ( !$requests ) :
        throw new \Exception( __( 'Failed to fetch all quote items: ', 'custom-quote-request' ) . $wpdb->last_error );        
      endif;

    } 
    
    // catch if we fail
    catch ( \Exception $e ) {

      $errorMsg = $e->getMessage();
      error_log( $errorMsg );
      
    }

    return $requests;    

  }

  public function updateQuoteReqStatus( int $id, string $status ) {
    
    global $wpdb;
    $reqTable = $wpdb->prefix . 'quote_requests';

    // try to update requests
    try {
      
    $updatedReq = $wpdb->update(
      $reqTable,
      array('status' => $status ),
      array( 'ID'    => $id ),
      array( '%s' ),
      array( '%d' )
    );

      if ( !$updatedReq ) :
        throw new \Exception( __( 'Failed to update quote requests: ', 'custom-quote-request' ) . $wpdb->last_error );        
      endif;

    } 
    
    // catch if we fail
    catch ( \Exception $e ) {

      $errorMsg = $e->getMessage();
      error_log( $errorMsg );
      
    }    

  }

}